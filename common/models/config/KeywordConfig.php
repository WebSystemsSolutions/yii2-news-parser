<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/24/17
 * Time: 2:01 PM
 */

namespace common\models\config;

use common\models\db\HeadlinesCompanies;
use yii\helpers\ArrayHelper;

/**
 * Class KeywordConfig
 * @package common\models\config
 */
class KeywordConfig extends Config
{
    /**
     * @const
     */
    const TYPE_COPPER = 'Copper';

    /**
     * @const
     */
    const TYPE_ALUMINUM = 'Aluminium';

    /**
     * @const
     */
    const TYPE_NICKEL = 'Nickel';

    /**
     * @const
     */
    const TYPE_ZINC = 'Zinc';

    /**
     * @const
     */
    const TYPE_LEAD = 'Lead';

    /**
     * @const
     */
    const TYPE_TIN = 'Tin';

    /**
     * @var string
     */
    public static $separator = '_';

    /**
     * @return array
     */
    public static function getMetalConstants()
    {
        return parent::getConstants();
    }

    /**
     * @param bool $metals
     * @param bool $active_companies
     *
     * @return array
     */
    public static function getConstants($metals = true, $active_companies = true)
    {
        static $constants = null;

        if ($constants === null) {

            $constants = [];

            if ($metals) {

                foreach (self::getMetalConstants() as $metal) {
                    $constants[TypeKeywordConfig::TYPE_METALS . self::$separator . strtoupper($metal)] = $metal;
                }
            }

            $query = HeadlinesCompanies::find();

            if ($active_companies) {
                $query->active();
            }

            $keywords = $query
                ->orderBy(['keyword' => SORT_ASC])
                ->asArray()
                ->all();

            foreach (ArrayHelper::getColumn($keywords, 'keyword') as $company) {
                $constants[TypeKeywordConfig::TYPE_COMPANIES . self::$separator . strtoupper(str_replace('.', '', $company))] = $company;
            }
        }

        return $constants;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function getConstantsValue()
    {
        return array_values(self::getConstants());
    }
}