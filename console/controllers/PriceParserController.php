<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 13.04.2017
 * Time: 13:08
 */

namespace console\controllers;

use common\models\config\KeywordConfig;
use common\models\config\ParserConfig;
use common\services\PriceReceiverInterface;
use console\helpers\KeywordWalker;
use yii\base\Module;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;

/**
 * Class PriceParserController
 * @package console\controllers
 */
class PriceParserController extends Controller
{
    /**
     * @var PriceReceiverInterface
     */
    private $receiver;

    /**
     * @var ParserConfig
     */
    private $config;

    /**
     * @var bool
     */
    private $stopped;

    /**
     * @var array
     */
    private $keywords;

    /**
     * ParserController constructor.
     * @param string $id
     * @param Module $module
     * @param PriceReceiverInterface $receiver
     * @param array $config
     */
    public function __construct($id, Module $module, PriceReceiverInterface $receiver, array $config = [])
    {
        $this->config = new ParserConfig(Yii::$app->params['parser']);

        $this->receiver = $receiver;
        $this->receiver->setConfig($this->config);

        $this->keywords = KeywordConfig::getConstantsValue();

        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $type
     * @param string $keyword
     * @return int
     */
    public function actionLoad($type, $keyword)
    {
        if ($this->receiver->begin($type, $keyword, function ($message, $error = false, $newline = false) {
            $this->stdout(($newline ? "\n" : '') . "$message\n", $error ? Console::FG_RED : Console::FG_GREEN);
        }, $this->stopped)) {

            $this->stdout("Complete - '{$this->config->apiKeyword}'\n", Console::FG_GREEN, Console::BOLD);
            return self::EXIT_CODE_NORMAL;
        }

        return self::EXIT_CODE_ERROR;
    }

    /**
     * @param string|null $type
     * @param integer $requests
     * @return int
     */
    public function actionLoadAll($type = null, $requests = 2)
    {
        while ($keyword = KeywordWalker::process($this->keywords, $this->stopped)) {

            $this->stopped = false;
            $this->config->apiMaxRequest = $requests;

            if ($code = $this->actionLoad($type, $keyword)) {
                return $code;
            }
        }

        return self::EXIT_CODE_NORMAL;
    }
}