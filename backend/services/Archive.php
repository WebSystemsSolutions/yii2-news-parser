<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 5/10/17
 * Time: 10:17 AM
 */

namespace backend\services;

use backend\models\config\ArchiveConfig;
use yii\helpers\FileHelper;

/**
 * Class Archive
 * @package backend\services
 */
class Archive implements ArchiveInterface
{
    /**
     * @var ArchiveConfig
     */
    private $config;

    /**
     * @var string
     */
    private $error;

    /**
     * @param string $name it is a name of archive
     * @return bool|string
     */
    public function archive($name)
    {
        if (!$this->validPath($this->config->path)) {

            $this->setError("The path does not exist: '{$this->config->path}'");
            return false;
        }

        if (!$this->validPath($this->config->filesPath, false)) {

            $this->setError("Files path does not exist: '{$this->config->filesPath}'");
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath = $this->config->path . DIRECTORY_SEPARATOR . $name . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {

            $this->setError("Cannot create a zip file: '$filePath'");
            return false;
        }

        foreach ($files = FileHelper::findFiles($this->config->filesPath) as $file) {

            if ($filePath !== $file) { //if true it means a same directory for archive
                $zip->addFile($file, basename($file));
            }
        }

        if (empty($files)) {
            $zip->addFromString('empty', '');
        }

        if (!$zip->close()) {

            $this->setError("Cannot close a zip archive: : '$filePath'");
            return false;
        }

        return $filePath;
    }

    /**
     * @param string $path
     * @param bool $create
     * @return bool
     * @throws \yii\base\Exception
     */
    private function validPath($path, $create = true)
    {
        if (!file_exists($path)) {

            if($create) {
                return FileHelper::createDirectory($path);
            }

            return false;
        }

        return true;
    }

    /**
     * @param ArchiveConfig $config
     */
    public function setConfig(ArchiveConfig $config)
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

    /**
     * @return mixed
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\Exception
     */
    public function clear()
    {
        if (!$this->validPath($this->config->path)){

            $this->setError("The path does not exist: {$this->config->path}");
            return false;
        }

        foreach (FileHelper::findFiles($this->config->path) as $file) {

            if(!unlink($file)) {

                $this->setError("Cannot remove a file: '$file'");
                return false;
            }
        }

        return true;
    }
}