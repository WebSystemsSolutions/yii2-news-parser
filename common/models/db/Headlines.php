<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 10.04.2017
 * Time: 12:06
 */

namespace common\models\db;

use yii\db\ActiveRecord;

/**
 * Class Headlines
 * @property string $id
 * @property string $keyword
 * @property string $language
 * @property string $title
 * @property string $content
 * @property integer $value_1
 * @property integer $value_7
 * @property integer $value_14
 * @property integer $value_28
 * @property string $created
 * @package common\models\db
 */
class Headlines extends ActiveRecord
{
    /**
     * @const
     */
    const COEFFICIENT = 100;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%headlines}}';
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        foreach (self::getValuesFields() as $value) {

            if ($this->$value === null) {
                $this->$value = mt_rand(-self::COEFFICIENT, self::COEFFICIENT);
                /**@todo temporary, manager will change it via admin panel */
            } else {
                $this->$value = (int)($this->$value * self::COEFFICIENT);
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();

        foreach (self::getValuesFields() as $value) {
            $this->$value /= self::COEFFICIENT;
        }
    }

    /**
     * @return array
     */
    public static function getValuesFields()
    {
        return [
            'value_1',
            'value_7',
            'value_14',
            'value_28'
        ];
    }

    /**
     *
     */
    public function attributeLabels()
    {
        return self::labels();
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return [
            'value_1'  => '1 day',
            'value_7'  => '7 days',
            'value_14' => '14 days',
            'value_28' => '28 days',
            'keyword'  => 'Asset',
        ];
    }
}