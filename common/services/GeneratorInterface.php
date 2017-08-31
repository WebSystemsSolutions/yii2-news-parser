<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 7/27/17
 * Time: 10:47 AM
 */

namespace common\services;

/**
 * Interface GeneratorInterface
 * @package common\models.
 */
interface GeneratorInterface
{
    /**
     * @return string|integer
     */
    public function getGeneratedName();

    /**
     * @return array
     */
    public function getGeneratedFieldsLabels();

    /**
     * @return array
     */
    public function getGeneratedFields();
}