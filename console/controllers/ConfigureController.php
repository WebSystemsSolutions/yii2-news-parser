<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/19/17
 * Time: 12:18 PM
 */

namespace console\controllers;

use common\models\config\GeneratorConfig;
use common\services\FileConverterInterface;
use common\services\HeadlinesManagerInterface;
use common\services\HeadlinesPricesManagerInterface;
use yii\base\Module;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;

/**
 * Class ConfigureController
 * @package console\controllers
 */
class ConfigureController extends Controller
{
    /**
     * @var FileConverterInterface
     */
    private $converter;

    /**
     * @var HeadlinesManagerInterface
     */
    private $headlineManager;

    /**
     * @var HeadlinesPricesManagerInterface
     */
    private $priceManager;

    /**
     * ConfigureController constructor.
     * @param string $id
     * @param Module $module
     * @param FileConverterInterface $converter
     * @param HeadlinesManagerInterface $headline_manager
     * @param HeadlinesPricesManagerInterface $price_manager
     * @param array $config
     */
    public function __construct($id, Module $module, FileConverterInterface $converter, HeadlinesManagerInterface $headline_manager, HeadlinesPricesManagerInterface $price_manager, array $config = [])
    {
        $this->converter = $converter;
        $this->converter->setConfig(new GeneratorConfig(Yii::$app->params['generator']));

        $this->headlineManager = $headline_manager;
        $this->priceManager = $price_manager;

        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @return int
     */
    public function actionGenerateFiles($date_from, $date_to)
    {
        $this->converter->setManager($this->headlineManager);
        return $this->generateFiles($date_from, $date_to);
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @return int
     */
    public function actionGeneratePriceFiles($date_from, $date_to)
    {
        $this->converter->setManager($this->priceManager);
        return $this->generateFiles($date_from, $date_to);
    }

    /**
     * @return int
     */
    public function actionClearFiles()
    {
        $this->converter->setManager($this->headlineManager);
        return $this->clearFiles();
    }

    /**
     * @return int
     */
    public function actionClearPriceFiles()
    {
        $this->converter->setManager($this->priceManager);
        return $this->clearFiles();
    }

    /**
     * @return int
     */
    public function actionDateRange()
    {
        $this->converter->setManager($this->headlineManager);
        return $this->dateRange();
    }

    /**
     * @return int
     */
    public function actionPriceDateRange()
    {
        $this->converter->setManager($this->priceManager);
        return $this->dateRange();
    }

    /**
     * @param string $date_from
     * @param string $date_to
     * @return int
     */
    private function generateFiles($date_from, $date_to)
    {
        if ($this->converter->begin($date_from, $date_to, function ($message, $error = false) {
            $this->stdout("$message\n", $error ? Console::FG_RED : Console::FG_GREEN);
        })) {

            $this->stdout("Complete\n", Console::FG_GREEN, Console::BOLD);
            return self::EXIT_CODE_NORMAL;
        }

        return self::EXIT_CODE_ERROR;
    }

    /**
     * @return int
     */
    private function clearFiles()
    {
        if ($this->converter->clear(function ($message, $error = false) {
            $this->stdout("$message\n", $error ? Console::FG_RED : Console::FG_GREEN);
        })) {

            $this->stdout("Complete\n", Console::FG_GREEN, Console::BOLD);
            return self::EXIT_CODE_NORMAL;
        }

        return self::EXIT_CODE_ERROR;
    }

    /**
     * @return int
     */
    private function dateRange()
    {
        if ($range = $this->converter->getDateRange()) {
            $this->stdout("Date: {$range->min} - {$range->max}\n", Console::FG_GREEN);
        } else {
            $this->stdout("Please use 'parser/load' to load data\n", Console::FG_GREY);
        }

        return self::EXIT_CODE_NORMAL;
    }
}