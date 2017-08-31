<?php

namespace frontend\models\forms;

use common\models\config\KeywordConfig;
use common\models\config\DaysConfig;
use common\models\config\TypeKeywordConfig;
use yii\base\Model;


/**
 * Class LoginForm
 * @package backend\models\forms
 */
class SearchForm extends Model
{
    /**
     * @var string
     */
    public $keyword;

    /**
     * @var string
     */
    public $days = DaysConfig::TYPE_WEEK;

    /**
     * @var string
     */
    public $type = TypeKeywordConfig::TYPE_COMPANIES;

    /**
     * @var array
     */
    public $keywords;

    /**
     * @var array
     */
    public $weeks;

    /**
     * @var string
     */
    public $dateFrom;

    /**
     * @var string
     */
    public $dateTo;

    /**
     * @var boolean|null
     */
    public $dateShifted;

    /**
     * @var array
     */
    public $types = [
        TypeKeywordConfig::TYPE_COMPANIES,
        TypeKeywordConfig::TYPE_METALS,
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->keywords = KeywordConfig::getConstantsValue();
        $this->keyword  = current($this->keywords);

        $this->weeks = DaysConfig::getConstantsValue();
        $this->types = array_combine($this->types, $this->types);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['keyword', 'days', 'type'], 'required'],
            ['keyword', 'in', 'range' => $this->keywords],
            ['days', 'in', 'range' => $this->weeks],
            ['type', 'in', 'range' => $this->types],
            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:d-m-Y'],
        ];
    }

    /**
     *
     */
    public function afterValidate()
    {
        parent::afterValidate();

        if ($this->dateFrom && $this->dateTo) {
            $this->dateShifted = true;
        }
    }
}
