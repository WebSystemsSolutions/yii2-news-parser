<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 6/14/17
 * Time: 1:31 PM
 */

namespace backend\services;

use backend\models\config\ImportConfig;

/**
 * Interface FileImportConverterInterface
 * @package common\services
 */
interface FileImportInterface
{
    /**
     * @return integer|false
     */
    public function update();

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function split($path);

    /**
     * @param ImportConfig $config
     */
    public function setConfig(ImportConfig $config);

    /**
     * @return string
     */
    public function getError();
}