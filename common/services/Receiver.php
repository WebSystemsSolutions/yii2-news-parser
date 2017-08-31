<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 13.04.2017
 * Time: 13:08
 */

namespace common\services;

use common\models\config\ParserConfig;
use common\models\Headline;
use common\models\HeadlinesIterator;

/**
 * Class Receiver
 * @package common\services
 */
class Receiver implements ReceiverInterface
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var HeadlinesManagerInterface
     */
    private $headlinesManager;

    /**
     * @var ParserConfig
     */
    private $config;

    /**
     * @param ParserInterface $parser
     * @param HeadlinesManagerInterface $headlines_manager
     */
    public function __construct(ParserInterface $parser, HeadlinesManagerInterface $headlines_manager)
    {
        $this->parser = $parser;
        $this->headlinesManager = $headlines_manager;
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

        foreach ($iterator = $this->iterate() as $headlines){

            $headlines = array_filter($headlines, function(Headline $headline){
                return !$this->headlinesManager->exists($this->config->apiKeyword, $headline->id);
            });

            if (!empty($headlines)) {

                $this->callbackLog($callback_log,"Load {$this->config->apiKeyword} headlines", false);

                /**@var Headline[] $headlines */
                foreach ($headlines as $headline) {

                    $headline->content = $this->parser->getHeadlineContent($this->config->detailPageUri, [
                        $this->config->detailPageParam => $headline->id
                    ]);

                    if ($headline->content === null) {

                        $this->callbackLog($callback_log, 'Cannot receive a headline content', true);
                        return false;
                    }

                    $this->callbackLog($callback_log, "parsed - {$this->config->apiKeyword} : {$headline->id}");

                    if ($this->config->apiRequestDelay) {
                        sleep($this->config->apiRequestDelay);
                    }
                }

                if (!$this->headlinesManager->save($headlines)) {

                    $this->callbackLog($callback_log, $this->headlinesManager->getError(), true);
                    return false;
                }

                $this->callbackLog($callback_log, "saved - {$this->config->apiKeyword}");

                if ($this->config->apiMaxRequest !== null) {
                    if (!(--$this->config->apiMaxRequest)) {

                        $stopped = true;
                        break;
                    }
                }
            }
        }

        if($error = $iterator->getError()){

            $this->callbackLog($callback_log, $error,true);
            return false;
        }

        return true;
    }


    /**
     * @return HeadlinesIterator
     */
    private function iterate()
    {
        $this->config->addQueryDataParam($this->headlinesManager->getDateRange($this->config->apiKeyword));
        return new HeadlinesIterator($this->parser, $this->config);
    }

    /**
     * @inheritdoc
     */
    public function getCompanies(callable $callback_log = null)
    {
        if(!$this->parser->login($this->config->uri, $this->config->login, $this->config->password)){

            $this->callbackLog($callback_log, 'Cannot login', true);
            return false;
        }

        $list = [];

        foreach ($this->config->getCompaniesParams() as $params) {

            $companies = $this->parser->getHeadlineCompanies($this->config->apiCompaniesPage, $params);

            if ($companies === null) {

                $this->callbackLog($callback_log, 'Cannot receive a list of companies', true);
                return false;
            }

            $list = array_merge($list, $companies);
        }

        return array_unique($list);
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
     * @param ParserConfig $config
     */
    public function setConfig(ParserConfig $config)
    {
        $this->config = $config;
    }
}