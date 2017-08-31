<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/24/17
 * Time: 2:01 PM
 */

namespace common\models\config;

use yii\base\InvalidParamException;
use yii\base\Object;
use yii\helpers\ArrayHelper;

/**
 * Class Config
 * @package common\models\config
 */
class Config extends Object
{
    /**
     * @param array $excluded
     */
    protected function checkInitProperties(array $excluded = [])
    {
        $class = new \ReflectionClass($this);

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {

            if (!$property->isStatic() && $this->{$prop = $property->getName()} === null && !ArrayHelper::isIn($prop, $excluded)) {
                throw new InvalidParamException("Property '$prop' is empty: " . self::className());
            }
        }
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function getConstants()
    {
        $class = new \ReflectionClass(self::className());
        return $class->getConstants();
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function getConstantsValue()
    {
        return array_values(self::getConstants());
    }
}