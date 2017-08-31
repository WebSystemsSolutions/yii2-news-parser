<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 11:45
 */

namespace common\models\config;

use yii\base\Object;

/**
 * Class DateRange
 * @package common\models\config
 */
class DateRange extends Object
{
    /**
     * @var string
     */
    public $min;

    /**
     * @var string
     */
    public $max;
}