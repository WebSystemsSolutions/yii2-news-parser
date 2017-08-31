<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 7/18/17
 * Time: 10:19 AM
 */

namespace common\helpers;

/**
 * Class HeadlineHelper
 * @package common\helpers
 */
class FormatHelper
{
    /**
     * @param string $value
     * @return string
     */
    public static function formatValue($value)
    {
        $value = preg_replace("~\s?".PHP_EOL.'~', ' ', strip_tags(html_entity_decode($value)));
        $value = preg_replace("~\s{2,}~", ' ', strip_tags($value));

        return trim($value);
    }
}