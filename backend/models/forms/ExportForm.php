<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 5/10/17
 * Time: 11:31 AM
 */

namespace backend\models\forms;

use common\helpers\KeywordHelper;
use common\models\config\GeneratorConfig;
use common\models\config\TypeKeywordConfig;
use yii\base\InvalidConfigException;
use yii\base\Model;
use common\models\config\KeywordConfig;

/**
 * Class ArchiveForm
 * @package backend\models\forms
 */
class ExportForm extends Model
{
    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

    /**
     * @var string
     */
    public $type;

    /**
     * @var boolean detect if it is headlines or headlines price
     */
    public $isHeadlines;

    /**
     * @var boolean
     */
    public $filterByCountRecords;

    /**
     * @var integer
     */
    public $countRecords;

    /**
     * @var array
     */
    public $types = [
        TypeKeywordConfig::TYPE_METALS,
        TypeKeywordConfig::TYPE_COMPANIES,
    ];

    /**
     * @var string
     */
    public $keyword;

    /**
     * @var array
     */
    public $keywords;

    /**
     * @var GeneratorConfig
     */
    public $config;

    /**
     *
     */
    public function init()
    {
        parent::init();

        if ($this->config === null) {
            throw new InvalidConfigException("Property 'config' has to be set: " . self::className());
        }

        $this->keywords = KeywordConfig::getConstants(true, false);
        $this->types = array_combine($this->types, $this->types);
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function rules()
    {
        return [
            [['from', 'to', 'type'], 'required'],
            ['type', 'in', 'range' => $this->types],
            ['keyword', 'in', 'range' => $this->keywords],
            ['filterByCountRecords', 'boolean'],
            ['countRecords', 'number', 'integerOnly' => true],
            [['from', 'to'], 'date', 'format' => 'php:Y-m-d'],
            ['to', 'compare', 'compareAttribute' => 'from', 'operator' => '>='],
        ];
    }

    /**
     * @param array $data
     * @param null|string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {

            if ($this->keyword && ($keyword = array_search($this->keyword, $this->keywords, true)) !== false) {

                $types = array_filter($this->types, function ($type) use ($keyword) {
                    return strpos($keyword, $type) === 0;
                });

                if ($types) {
                    $this->type = array_shift($types);
                }
            }

            return true;
        }

        return false;
    }

    /**
     *
     */
    public function afterValidate()
    {
        if ($this->keyword) {
            $this->keywords = [$this->keyword];
        } else {

            $this->keywords = array_keys(array_filter(array_flip($this->keywords), function ($name) {
                return strpos($name, $this->type) === 0;
            }));
        }

        if ($this->filterByCountRecords) {

            if ($this->isHeadlines) {
                $keywords = KeywordHelper::getCountHeadlinesByKeyword($this->countRecords, $this->from, $this->to);
            } else {
                $keywords = KeywordHelper::getCountPricesByKeyword($this->countRecords, $this->from, $this->to);
            }

            $this->keywords = array_values(array_intersect($this->keywords, array_keys($keywords)));
        }

        parent::afterValidate();
    }

    /**
     * @return false|string
     */
    public function processKeyword()
    {
        if (empty($this->keywords)) {
            return false;
        }

        return $this->config->keyword = array_shift($this->keywords);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'keyword' => 'Asset',
        ];
    }
}