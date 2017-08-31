<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 5/10/17
 * Time: 10:15 AM
 */

namespace backend\services;

use backend\models\config\ArchiveConfig;

/**
 * Interface ArchiveInterface
 * @package backend\services
 */
interface ArchiveInterface
{
    /**
     * @param string $name it is a name of archive
     * @return bool|string
     */
    public function archive($name);

    /**
     * @param ArchiveConfig $config
     */
    public function setConfig(ArchiveConfig $config);

    /**
     * @return string
     */
    public function getError();

    /**
     * @return mixed
     */
    public function clear();
}