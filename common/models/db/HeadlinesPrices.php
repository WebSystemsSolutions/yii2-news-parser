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
 * Class HeadlinesPrices
 * @property string $id
 * @property string $keyword
 * @property integer $price
 * @property string $date
 * @package common\models\db
 */
class HeadlinesPrices extends ActiveRecord
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
        return '{{%headlines_prices}}';
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->price = (int)($this->price * self::COEFFICIENT);
        return parent::beforeSave($insert);
    }

    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->price /= self::COEFFICIENT;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'keyword' => 'Asset'
        ];
    }
}