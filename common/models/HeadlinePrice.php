<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 14:01
 */

namespace common\models;

use common\models\config\KeywordConfig;
use common\models\db\HeadlinesPrices;
use common\services\GeneratorInterface;
use yii\base\Model;

/**
 * Class HeadlinePrice
 * @package common\models
 */
class HeadlinePrice extends Model implements GeneratorInterface
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $keyword;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $date;

    /**
     * @var boolean
     */
    public $isNewRecord;

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function rules()
    {
        return [
            [['keyword', 'price', 'date'], 'required'],
            [['keyword'], 'string', 'max' => 255],
            [['keyword'], 'in', 'range' => KeywordConfig::getConstantsValue()],
            ['price', 'number'],
            ['date', 'date', 'format' => 'php:Y-m-d'],
            [['keyword', 'date'], 'unique', 'targetClass' => HeadlinesPrices::className(), 'targetAttribute' => ['keyword', 'date'], 'when' => function(){
                return $this->isNewRecord;
            }],
        ];
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

    /**
     * @inheritdoc
     */
    public function getGeneratedName()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getGeneratedFieldsLabels()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getGeneratedFields()
    {
        return $this->getAttributes(null, ['id', 'isNewRecord']);
    }
}