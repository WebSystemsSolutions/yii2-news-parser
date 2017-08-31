<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/19/17
 * Time: 6:12 PM
 */

namespace common\services;

/**
 * Interface FileGeneratorInterface
 * @package common\services
 */
interface FileGeneratorInterface
{
    /**
     * @param GeneratorInterface $object
     * @return boolean
     */
    public function generate(GeneratorInterface $object);

    /**
     * @return mixed
     */
    public function clear();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getError();
}