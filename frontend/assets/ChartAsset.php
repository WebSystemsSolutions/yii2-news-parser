<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/28/17
 * Time: 3:23 PM
 */

namespace frontend\assets;

use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Class ChartAsset
 * @package frontend\assets
 */
class ChartAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @param \yii\web\View $view
     * @param array $prices
     * @param array $values
     * @return AssetBundle
     * @throws \yii\base\InvalidParamException
     * @throws \yii\base\InvalidConfigException
     */
    public static function register($view, $prices = [], $values = [])
    {
        $view->registerJs('var chartData = {prices: '.Json::encode($prices).', values: '.Json::encode($values).'};', View::POS_HEAD);
        return $view->registerAssetBundle(get_called_class());
    }

    /**
     * @var array
     */
    public $js = [
        '//www.gstatic.com/charts/loader.js',
        'js/chart.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'frontend\assets\MomentAsset',
    ];

}