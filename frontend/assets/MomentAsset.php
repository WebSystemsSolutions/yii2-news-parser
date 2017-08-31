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
 * Class MomentAsset
 * @package frontend\assets
 */
class MomentAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/bower/moment';

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
        'moment.js'
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