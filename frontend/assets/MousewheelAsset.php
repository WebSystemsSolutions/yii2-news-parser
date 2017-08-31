<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/28/17
 * Time: 3:59 PM
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class MousewheelAsset
 * @package frontend\assets
 */
class MousewheelAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/bower/jquery-mousewheel';

    /**
     * @var array
     */
    public $js = [
        'jquery.mousewheel.js'
    ];

    /**
     * @var array
     */
    public $publishOptions = [
        'only' => [
            '*.js',
        ]
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}