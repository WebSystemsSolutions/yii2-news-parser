<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 8/14/17
 * Time: 1:50 PM
 */

namespace common\helpers;

use common\models\config\KeywordConfig;
use common\models\db\Headlines;
use common\models\db\HeadlinesPrices;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class HeadlinesHelper
 * @package common\helpers
 */
class KeywordHelper
{
    /**
     * @param integer|null $count_records
     * @param string|null $date_from
     * @param string|null $date_to
     *
     * @return array
     */
    public static function getCountHeadlinesByKeyword($count_records = null, $date_from = null, $date_to = null)
    {
        return self::getObjectsByKeyword(Headlines::className(), 'DATE(created)', $count_records, $date_from, $date_to);
    }

    /**
     * @param integer|null $count_records
     * @param string|null $date_from
     * @param string|null $date_to
     *
     * @return array
     */
    public static function getCountPricesByKeyword($count_records = null, $date_from = null, $date_to = null)
    {
        return self::getObjectsByKeyword(HeadlinesPrices::className(), 'date', $count_records, $date_from, $date_to);
    }

    /**
     * @return array
     */
    public static function getHeadlinesKeywords()
    {
        return self::getKeywords(Headlines::className());
    }

    /**
     * @return array
     */
    public static function getPricesKeywords()
    {
        return self::getKeywords(HeadlinesPrices::className());
    }

    /**
     * @param bool $nested
     *
     * @return array
     */
    public static function getDropDownHeadlinesKeywords($nested = true)
    {
        return self::getDropDownKeywords(true, false, self::getKeywords(Headlines::className()), $nested);
    }

    /**
     * @param bool $nested
     *
     * @return array
     */
    public static function getDropDownPricesKeywords($nested = true)
    {
        return self::getDropDownKeywords(true, false, self::getKeywords(HeadlinesPrices::className()), $nested);
    }

    /**
     * @param bool $metals
     * @param bool $active_companies
     * @param array $keywords
     * @param bool $nested
     *
     * @return array
     */
    public static function getDropDownKeywords($metals = false, $active_companies = true, array $keywords = [], $nested = true)
    {
        $constants = [];

        foreach (KeywordConfig::getConstants($metals, $active_companies) as $name => $value) {

            if (empty($keywords) || ArrayHelper::isIn($value, $keywords)) {

                if ($nested) {
                    $constants[substr($name, 0, strpos($name, KeywordConfig::$separator))][$value] = $value;
                } else {
                    $constants[$name] = $value;
                }
            }
        }

        return $constants;
    }

    /**
     * @param ActiveRecord|string $class
     * @param string $field
     * @param string|null $count_records
     * @param string|null $date_from
     * @param string|null $date_to
     *
     * @return array
     */
    private static function getObjectsByKeyword($class, $field, $count_records = null, $date_from = null, $date_to = null)
    {
        $keywords = $class::find()
            ->select(['keyword', 'count(*) as records'])
            ->andFilterWhere(['>=', $field, $date_from])
            ->andFilterWhere(['<=', $field, $date_to])
            ->groupBy(['keyword'])
            ->andFilterHaving(['>=', 'records', $count_records])
            ->orderBy(['records' => SORT_DESC])
            ->asArray()
            ->all();

        return ArrayHelper::map($keywords, 'keyword', 'records');
    }

    /**
     * @param ActiveRecord|string $class
     *
     * @return array
     */
    private static function getKeywords($class)
    {
        $keywords = $class::find()
            ->select(['keyword'])
            ->distinct()
            ->orderBy(['keyword' => SORT_ASC])
            ->asArray()
            ->all();

        return ArrayHelper::getColumn($keywords, 'keyword', false);
    }
}