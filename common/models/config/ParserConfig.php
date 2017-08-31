<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 13.04.2017
 * Time: 13:29
 */

namespace common\models\config;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class ParserConfig
 * @package console\models\config
 */
class ParserConfig extends Config implements ParserConfigInterface
{
    /**
     * @const string
     */
    const PAGE_TYPE_OLD = 'older';

    /**
     * @const string
     */
    const PAGE_TYPE_NEW = 'newer';

    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $apiPage;

    /**
     * @var array
     */
    public $apiParams;

    /**
     * @var array
     */
    public $apiBodyParams;

    /**
     * @var string
     */
    public $apiBodyKeywordPattern;

    /**
     * @var string
     */
    public $apiCompaniesPage;

    /**
     * @var array
     */
    public $apiCompaniesParams;

    /**
     * @var string
     */
    public $apiCompaniesListParam;

    /**
     * @var array
     */
    public $apiCompaniesListIds;

    /**
     * @var string
     */
    public $apiMonitorPricePage;

    /**
     * @var array
     */
    public $apiMonitorPriceParams;

    /**
     * @var integer
     */
    public $apiRequestDelay;

    /**
     * @var integer
     */
    public $apiMaxRequest;

    /**
     * @var string
     */
    public $apiDateParam;

    /**
     * @var string
     */
    public $apiKeyword;

    /**
     * @var string
     */
    public $apiPageType;

    /**
     * @var string
     */
    public $detailPageUri;

    /**
     * @var string
     */
    public $detailPageParam;

    /**
     * @var array
     */
    private static $apiPageTypes = [
        self::PAGE_TYPE_OLD,
        self::PAGE_TYPE_NEW,
    ];

    /**
     * @var array
     */
    private static $keywordsMap = [
        KeywordConfig::TYPE_COPPER   => 'CMCU0',
        KeywordConfig::TYPE_ALUMINUM => 'CMAL0',
        KeywordConfig::TYPE_NICKEL   => 'CMNI0',
        KeywordConfig::TYPE_ZINC     => 'CMZN0',
        KeywordConfig::TYPE_LEAD     => 'CMPB0',
        KeywordConfig::TYPE_TIN      => 'CMSN0',
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->checkInitProperties(['apiRequestDelay', 'apiMaxRequest']);
    }

    /**
     * @inheritdoc
     */
    protected function checkInitProperties(array $excluded = [])
    {
        parent::checkInitProperties($excluded);

        if (!isset($this->apiMonitorPriceParams['Entity']['W']['Tickers'])) {
            throw new InvalidConfigException("Property 'apiMonitorPriceParams' need to have keys [Entity][W][Tickers]: " . self::className());
        }
    }

    /**
     *
     */
    public function getBodyParams()
    {
        $params = $this->apiBodyParams[$this->isResourceKeyword()];

        $walk = function (array &$params) use(&$walk) {

            foreach ($params as &$param) {
                $param = is_array($param) ? $walk($param) : str_replace($this->apiBodyKeywordPattern, $this->apiKeyword, $param);
            }

            return $params;
        };

        return $walk($params);
    }

    /**
     *
     */
    public function removeQueryDataParam()
    {
        if (isset($this->apiParams[$this->apiDateParam])) {
            unset($this->apiParams[$this->apiDateParam]);
        }
    }

    /**
     * @param DateRange|null $range
     */
    public function addQueryDataParam($range)
    {
        $this->removeQueryDataParam();

        if ($range) {

            switch ($this->apiPageType) {

                case self::PAGE_TYPE_OLD:
                    $this->apiParams[$this->apiDateParam] = $range->min;
                break;

                case self::PAGE_TYPE_NEW:
                    $this->apiParams[$this->apiDateParam] = $range->max;
                break;
            }
        }
    }

    /**
     * @param string|null $keyword
     *
     * @return bool
     */
    public function isValidKeyword(&$keyword = null)
    {
        return $this->existsInConfig('apiKeyword', $keyword, KeywordConfig::getConstants());
    }

    /**
     * @param string|null $type
     * @return bool
     */
    public function isValidPageType(&$type = null)
    {
        return $this->existsInConfig('apiPageType', $type, self::$apiPageTypes);
    }

    /**
     * @return bool
     */
    public function isNewerPageTypeWithDateParam()
    {
        return self::PAGE_TYPE_NEW == $this->apiPageType && isset($this->apiParams[$this->apiDateParam]);
    }

    /**
     * @return array
     */
    public function getMonitorPriceParams()
    {
        return ArrayHelper::merge($this->apiMonitorPriceParams, [
            'Entity' => [
                'W' => [
                    'Tickers' => [isset(self::$keywordsMap[$this->apiKeyword]) ? self::$keywordsMap[$this->apiKeyword] : $this->apiKeyword],
                ]
            ]
        ]);
    }

    /**
     * @return array
     */
    public function getCompaniesParams()
    {
        return array_map(function ($id) {
            return ArrayHelper::merge($this->apiCompaniesParams, [$this->apiCompaniesListParam => $id]);
        }, $this->apiCompaniesListIds);
    }

    /**
     * @return bool
     */
    private function isResourceKeyword()
    {
        return !ArrayHelper::isIn($this->apiKeyword, KeywordConfig::getMetalConstants());
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param array $array
     *
     * @return bool
     */
    private function existsInConfig($field, &$value, array $array)
    {
        if ($value) {

            if (!ArrayHelper::isIn($value, $array)) {
                return false;
            }

            $this->$field = $value;

        } else {

            $value = $this->$field;

            if (!ArrayHelper::isIn($this->$field, $array)) {
                return false;
            }
        }

        return true;
    }
}