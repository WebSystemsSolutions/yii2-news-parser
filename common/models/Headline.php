<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 14:01
 */

namespace common\models;

use common\models\config\KeywordConfig;
use common\models\db\Headlines;
use common\services\GeneratorInterface;
use yii\base\Model;

/**
 * Class Headline
 * @package common\models
 */
class Headline extends Model implements GeneratorInterface
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $keyword;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    /**
     * @var float
     */
    public $value;

    /**
     * @var float
     */
    public $value_1;

    /**
     * @var float
     */
    public $value_7;

    /**
     * @var float
     */
    public $value_14;

    /**
     * @var float
     */
    public $value_28;

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
        $values = self::getValuesFields();

        return [
            [['id', 'keyword', 'title', 'created'], 'required'],
            [['keyword'], 'string', 'max' => 255],
            [['keyword'], 'in', 'range' => KeywordConfig::getConstantsValue()],
            [['title'], 'string', 'max' => 550],
            [['id'], 'string', 'max' => 50],
            [['language'], 'string', 'max' => 15],
            ['content', 'string', 'max' => 16777215],
            [$values, 'default', 'value' => null],
            [$values, 'number'],
            ['created', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['id', 'keyword'], 'unique', 'targetClass' => Headlines::className(), 'targetAttribute' => ['id', 'keyword'], 'when' => function(){
                return $this->isNewRecord;
            }],
        ];
    }

    /**
     * @return array
     */
    public static function getValuesFields()
    {
        return Headlines::getValuesFields();
    }

    /**
     *
     */
    public function attributeLabels()
    {
        return Headlines::labels();
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
        return [
            'created' => 'time',
            'content' => 'text',
        ];
    }

    /**
     * @return array
     */
    public function getGeneratedFields()
    {
        return $this->getAttributes(null, ['id', 'value', 'isNewRecord']);
    }
}