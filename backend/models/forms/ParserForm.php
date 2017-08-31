<?php

namespace backend\models\forms;

use common\models\config\KeywordConfig;
use yii\base\Model;

/**
 * Class ParserForm
 * @package backend\models\forms
 */
class ParserForm extends Model
{
    /**
     * @var string
     */
    public $keyword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['keyword', 'required'],
            ['keyword', 'in', 'range' => KeywordConfig::getConstantsValue()],
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
}
