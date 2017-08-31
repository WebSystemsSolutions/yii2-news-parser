<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 7/26/17
 * Time: 2:26 PM
 */

namespace common\helpers;

/**
 * Class DepDropKeywordHelper
 * @package common\helpers
 */
class DepDropKeywordHelper
{
    /**
     * @param array $parents
     * @param array $params
     * @param array $keywords
     * @return array
     */
    public static function build(array $parents, array $params, array $keywords)
    {
        $selected = '';
        $output = [];

        if($parents && $params && ($type = array_shift($parents))){

            $selected = array_shift($params);

            $constants = [];

            foreach ($keywords as $name => $value) {

                if (strpos($name, $type) === 0) {
                    $constants[] = $value;
                }
            }

            if (!in_array($selected, $constants, true)) {
                $selected = current($constants);
            }

            $output = array_map(function($value){
                return [
                    'id'   => $value,
                    'name' => $value,
                ];
            }, $constants);
        }

        return [
            'output'   => $output,
            'selected' => $selected,
        ];
    }
}