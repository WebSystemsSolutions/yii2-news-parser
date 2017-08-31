<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/24/17
 * Time: 2:01 PM
 */

namespace common\models\config;

/**
 * Class DaysConfig
 * @package common\models\config
 */
class DaysConfig extends Config
{
    /**
     * @const
     */
    const TYPE_DAY  = 1;

    /**
     * @const
     */
    const TYPE_WEEK = 7;

    /**
     * @const
     */
    const TYPE_WEEK_2 = 14;

    /**
     * @const
     */
    const TYPE_WEEK_4 = 28;
}