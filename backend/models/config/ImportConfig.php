<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/24/17
 * Time: 2:00 PM
 */

namespace backend\models\config;

use common\models\config\Config;
use Yii;

/**
 * Class ImportConfig
 * @package backend\models\config
 */
class ImportConfig extends Config
{
    /**
     * @var string path to import files
     */
    public $path;

    /**
     * @var integer
     */
    public $rows;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->path = Yii::getAlias($this->path);
    }
}