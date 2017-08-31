<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 14:01
 */

namespace common\models;

use common\models\db\HeadlinesCompanies;
use yii\base\Model;

/**
 * Class HeadlineCompany
 * @package common\models
 */
class HeadlineCompany extends Model
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
     * @var boolean
     */
    public $active;

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
            ['keyword', 'required'],
            ['keyword', 'string', 'max' => 255],
            ['active', 'boolean'],
            ['keyword', 'unique', 'targetClass' => HeadlinesCompanies::className(), 'targetAttribute' => 'keyword', 'when' => function(){
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
            'keyword' => 'Name',
        ];
    }
}