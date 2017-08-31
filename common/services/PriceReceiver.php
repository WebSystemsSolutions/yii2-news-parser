<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 13.04.2017
 * Time: 13:08
 */

namespace common\services;

use common\models\config\ParserConfig;
use common\models\HeadlinePrice;

/**
 * Class PriceReceiver
 * @package common\services
 */
class PriceReceiver implements PriceReceiverInterface
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var HeadlinesPricesManagerInterface
     */
    private $pricesManager;

    /**
     * @var ParserConfig
     */
    private $config;

    /**
     * @param ParserInterface $parser
     * @param HeadlinesPricesManagerInterface $price_manager
     */
    public function __construct(ParserInterface $parser, HeadlinesPricesManagerInterface $price_manager)
    {
        $this->parser = $parser;
        $this->pricesManager = $price_manager;
    }

    /**
     * @inheritdoc
     */
    public function begin($type = null, $keyword = null, callable $callback_log = null, &$stopped = false)
    {
        if (!$this->config->isValidPageType($type)) {

            $this->callbackLog($callback_log,"Wrong page type '{$type}'", true);
            return false;
        }

        if (!$this->config->isValidKeyword($keyword)) {

            $this->callbackLog($callback_log,"Wrong keyword '{$keyword}'", true);
            return false;
        }

        if(!$this->parser->login($this->config->uri, $this->config->login, $this->config->password)){

            $this->callbackLog($callback_log, 'Cannot login', true);
            return false;
        }

        if ($prices = $this->parser->getHeadlinePrices($this->config->apiMonitorPricePage, $this->config->getMonitorPriceParams())) {

            do {

                $range = $this->pricesManager->getDateRange($this->config->apiKeyword);

                if ($type == ParserConfig::PAGE_TYPE_NEW) {

                    $objects = array_slice($range ? array_filter($prices, function ($info) use ($range) {
                        return $info->date > $range->max;
                    }) : $prices, $range ? -30 : 0, $range ? null : 1);

                } else {

                    $objects = array_slice($range ? array_filter($prices, function ($info) use ($range) {
                        return $info->date < $range->min;
                    }) : $prices, 0, 30);
                }

                if (!$this->processPrices($this->config->apiKeyword, $objects, $callback_log)) {
                    return false;
                }

                if ($this->config->apiRequestDelay) {
                    sleep($this->config->apiRequestDelay);
                }

                if ($this->config->apiMaxRequest !== null) {
                    if (!(--$this->config->apiMaxRequest)) {

                        $stopped = true;
                        break;
                    }
                }

            } while (!empty($objects));
        }

        if ($prices === null) {

            $this->callbackLog($callback_log, 'Cannot get prices', true);
            return false;
        }

        return true;
    }

    /**
     * @param string $keyword
     * @param array $objects
     * @param callable|null $callback_log
     *
     * @return bool
     */
    private function processPrices($keyword, &$objects, callable $callback_log = null)
    {
        if ($objects = array_filter($objects, function (\stdClass $object) use ($keyword) {
                return !$this->pricesManager->exists($keyword, $object->date);
            })) {

            $this->callbackLog($callback_log,"Load $keyword prices ", false);

            $prices = [];

            foreach ($objects as $object) {

                $prices[] = new HeadlinePrice([
                    'keyword' => $keyword,
                    'price'   => $object->price,
                    'date'    => $object->date,
                ]);

                $this->callbackLog($callback_log, "parsed - {$keyword} - {$object->date} : {$object->price}");
            }

            if (!$this->pricesManager->save($prices)) {

                $this->callbackLog($callback_log, $this->pricesManager->getError(), true);
                return false;
            }

            $this->callbackLog($callback_log, "saved - {$keyword}");
        }

        return true;
    }

    /**
     * @param callable|null $callback_log
     * @param string $message
     * @param bool $error
     */
    private function callbackLog(callable $callback_log = null, $message = '', $error = false)
    {
        if ($callback_log) {
            $callback_log($message, $error);
        }
    }

    /**
     * @inheritdoc
     */
    public function setConfig(ParserConfig $config)
    {
        $this->config = $config;
    }
}