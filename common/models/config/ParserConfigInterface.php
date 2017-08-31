<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 7/11/17
 * Time: 5:37 PM
 */

namespace common\models\config;

/**
 * Interface ParserConfigInterface
 * @package common\models\config
 */
interface ParserConfigInterface
{
    /**
     * @return boolean
     */
    public function isValidKeyword();
}