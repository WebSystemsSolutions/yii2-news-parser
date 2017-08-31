<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 6/14/17
 * Time: 1:39 PM
 */

namespace backend\services;

use backend\models\config\ImportConfig;
use common\models\db\Headlines;
use common\models\Headline;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class FileImportConverter
 * @package common\services
 */
class FileImportCsv implements FileImportInterface
{
    /**
     * @const
     */
    const MAX_ROW_LENGTH = 10000;

    /**
     * @const
     */
    const DELIMITER = ',';

    /**
     * @var string
     */
    private $primary = 'created';

    /**
     * @var ImportConfig
     */
    private $config;

    /**
     * @var string
     */
    private $error;

    /**
     * @param string $path
     *
     * @return bool
     */
    public function split($path)
    {
        if (!file_exists($path) || !is_writable($path)) {

            $this->setError("The path does not exist or does not have write permission: '{$path}'");
            return false;
        }

        if (!file_exists($this->config->path) || !is_writable($this->config->path)) {

            $this->setError("The path does not exist or does not have write permission: '{$this->config->path}'");
            return false;
        }

        $split_file = null;
        $result = true;

        if (($file = fopen($path, 'rb')) === false) {

            $this->setError("Cannot read a file: '{$path}'");
            return false;
        }

        $header = fgetcsv($file, self::MAX_ROW_LENGTH, self::DELIMITER);

        if (!ArrayHelper::isIn($this->primary, $header)) {

            $this->setError("The primary field does not exist in a file: '{$this->config->path}'");

            $result = false;
            goto end;
        }

        while (($data = fgetcsv($file, self::MAX_ROW_LENGTH, self::DELIMITER)) !== false) {

            if (!$this->writeToSplitFiles($split_file, $header, $data)) {

                $result = false;
                goto end;
            }
        }

        end:

        if ($split_file) {
            fclose($split_file);
        }

        fclose($file);
        return $result;
    }

    /**
     * @return boolean
     * @throws \yii\base\InvalidParamException
     */
    public function update()
    {
        $files = FileHelper::findFiles($this->config->path);

        if (empty($files)) {
            return 0;
        }

        $file_path = array_shift($files);

        if (($file = fopen($file_path, 'rb')) === false) {

            $this->setError("Cannot read a file: '{$file_path}'");
            return false;
        }

        $header = fgetcsv($file, self::MAX_ROW_LENGTH, self::DELIMITER);

        $result = true;
        $headline = new Headline();

        while (($data = fgetcsv($file, self::MAX_ROW_LENGTH, self::DELIMITER)) !== FALSE) {

            $headline->setAttributes(array_combine($header, $data));

            $attributes = array_diff_key(array_filter($headline->getAttributes()), [$this->primary => 0]);

            if (!empty($attributes)) {

                if (!$headline->validate(array_keys($attributes))) {

                    $this->setError('Validation error');

                    $result = false;
                    goto end;
                }

                foreach (Headline::getValuesFields() as $name) {

                    if (!empty($attributes[$name])) {
                        $attributes[$name] *= Headlines::COEFFICIENT;
                    }
                }

                Headlines::updateAll($attributes, "{$this->primary} = :param", [':param' => $headline->{$this->primary}]);

            } else {

                $this->setError("Cannot find correct attributes in a file: '{$file_path}'");

                $result = false;
                goto end;
            }
        }

        end:

        fclose($file);

        if ($result) {
            unlink($file_path);
        }

        return $result ? count($files) : false;
    }

    /**
     * @param $file
     * @param array $header
     * @param array $data
     *
     * @return bool
     */
    private function writeToSplitFiles(&$file, array $header, array $data)
    {
        static $counter = 0;

        if ($file === null || $counter++ >= $this->config->rows) {

            if ($file) {
                fclose($file);
            }

            $file_path = $this->config->path . '/' . md5(uniqid('', true));

            if (($file = fopen($file_path, 'wb')) === false) {

                $file = null;
                $this->setError("Cannot create a file: '{$file_path}'");

                return false;
            }

            if (fputcsv($file, $header, self::DELIMITER) === false) {

                $this->setError("Cannot write to a file: '{$file_path}'");
                return false;
            }

            $counter = 0;
        }

        if (fputcsv($file, $data, self::DELIMITER) === false) {

            $this->setError('Cannot write to a split file.');
            return false;
        }

        return true;
    }

    /**
     * @param ImportConfig $config
     */
    public function setConfig(ImportConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $error
     */
    private function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}