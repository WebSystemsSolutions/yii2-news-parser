<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/28/17
 * Time: 3:57 PM
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class EasingAsset
 * @package frontend\assets
 */
class EasingAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/bower/jquery.easing/js';

    /**
     *
     */
    public function init(){

        parent::init();

        $this->publishOptions['beforeCopy'] = function($from){
            return !is_dir($from);
        };
    }

    /**
     * @var array
     */
    public $js = [
        'jquery.easing.js'
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