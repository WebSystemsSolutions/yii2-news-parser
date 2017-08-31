<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/24/17
 * Time: 2:00 PM
 */

namespace common\models\config;

use Yii;

/**
 * Class GeneratorConfig
 * @package app\models\config
 */
class GeneratorConfig extends Config
{
    /**
     * @var string
     */
    public $keyword;

    /**
     * @var string
     */
    public $headlineFilesPath;

    /**
     * @var string
     */
    public $priceFilesPath;

    /**
     *
     * @throws \yii\base\InvalidParamException
     */
    public function init()
    {
        parent::init();

        $this->headlineFilesPath = Yii::getAlias($this->headlineFilesPath);
        $this->priceFilesPath = Yii::getAlias($this->priceFilesPath);
    }
}