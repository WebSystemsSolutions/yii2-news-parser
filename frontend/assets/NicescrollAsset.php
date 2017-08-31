<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/28/17
 * Time: 3:52 PM
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Class NicescrollAsset
 * @package frontend\assets
 */
class NicescrollAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/bower/jquery.nicescroll';

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
        'jquery.nicescroll.js'
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