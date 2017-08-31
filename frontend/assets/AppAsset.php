<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/28/17
 * Time: 3:23 PM
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package frontend\assets
 */
class AppAsset extends AssetBundle
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
     * @var array
     */
    public $css = [
        'css/main.css',
    ];

    /**
     * @var array
     */
    public $js = [
        'js/main.js',
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'frontend\assets\MousewheelAsset',
        'frontend\assets\NicescrollAsset',
        'frontend\assets\EasingAsset',
    ];
}