<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/19/17
 * Time: 6:13 PM
 */

namespace common\services;

use common\helpers\FormatHelper;
use yii\helpers\FileHelper;

/**
 * Class FileGenerator
 * @package common\services
 */
class FileGenerator implements FileGeneratorInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $error;

    /**
     * @inheritdoc
     */
    public function generate(GeneratorInterface $object)
    {
        if (!$this->validPath()){

            $this->setError("The path does not exist: '{$this->path}'");
            return false;
        }

        if (($file = fopen($file_path = $this->path . DIRECTORY_SEPARATOR . $object->getGeneratedName(),'wb+')) === false) {

            $this->setError("Cannot create a file: '$file_path'");
            return false;
        }

        $labels = $object->getGeneratedFieldsLabels();

        foreach ($object->getGeneratedFields() as $name => $value) {

            if (isset($labels[$name])) {
                $name = $labels[$name];
            }

            if (($write = fwrite($file, "[$name]>" . FormatHelper::formatValue($value) . "<[$name]".PHP_EOL)) === false) {

                fclose($file);
                unlink($file_path);

                $this->setError("Cannot write to a file: '$file_path'");

                return false;
            }
        }

        return fclose($file);
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\Exception
     */
    public function clear()
    {
        if (!$this->validPath()){

            $this->setError("The path does not exist: {$this->path}");
            return false;
        }

        foreach (FileHelper::findFiles($this->path) as $file) {

            if(!unlink($file)) {

                $this->setError("Cannot remove a file: '$file'");
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param bool $create
     * @return bool
     * @throws \yii\base\Exception
     */
    private function validPath($create = true)
    {
        if (!file_exists($this->path)) {

            if($create) {
                return FileHelper::createDirectory($this->path);
            }

            return false;
        }

        return true;
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