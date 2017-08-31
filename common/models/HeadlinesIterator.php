<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 12:29
 */

namespace common\models;

use common\models\config\ParserConfig;
use common\services\ParserInterface;
use yii\helpers\ArrayHelper;

/**
 * Class HeadlinesIterator
 * @package common\models
 */
class HeadlinesIterator implements \Iterator
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var ParserConfig
     */
    private $config;

    /**
     * @var \stdClass|bool|null
     */
    private $response;

    /**
     * @var Headline[]
     */
    private $headlines = [];

    /**
     * @var integer
     */
    private $counter = -1;

    /**
     * @var string
     */
    private $error;

    /**
     * HeadlinesIterator constructor.
     * @param ParserInterface $parser
     * @param ParserConfig $config
     */
    public function __construct(ParserInterface $parser, ParserConfig $config)
    {
        $this->parser = $parser;
        $this->config = $config;
    }

    /**
     * @return Headline[]
     */
    public function current()
    {
        return $this->headlines;
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->response = $this->parser->getHeadlines(
            $this->config->apiPage,
            $this->config->apiParams,
            $this->config->getBodyParams()
        );

        if (!$this->response) {
            $this->setError('Cannot get headlines');
        } else {

            if ($this->config->isNewerPageTypeWithDateParam()) {
                $this->next();
            }
        }
    }

    /**
     * @return integer
     */
    public function key()
    {
        return $this->counter;
    }

    /**
     * @return void
     */
    public function next()
    {
        if (!empty($this->response->{$page_type = $this->config->apiPageType})) {

            $this->response = $this->parser->getHeadlines(
                $this->config->apiPage,
                $this->getQueryParams($this->response->$page_type),
                $this->config->getBodyParams()
            );

            if ($this->response) {

                if(empty($this->response->headlines)) {
                    $this->response->$page_type = null;
                }

            } else {
                $this->setError('Cannot get headlines');
            }
        }
    }

    /**
     * @return bool
     */
    private function prepare()
    {
        $headlines = [];

        foreach ((array)$this->response->headlines as $headline) {

            /**@var \stdClass $headline*/
            if (!empty($headline->storyId) && preg_match('~:(\w+):\d+$~', $headline->storyId, $matches)) {

                if (!isset($headlines[$id = $matches[1]])) {

                    $language = '';

                    if (!empty($headline->language) && preg_match('~^L:(\w+)~', $headline->language, $matches)) {
                        $language = $matches[1];
                    }

                    $headlines[$id] = new Headline([
                        'id'       => $id,
                        'keyword'  => $this->config->apiKeyword,
                        'language' => $language,
                        'title'    => $headline->text,
                        'created'  => (new \DateTime($headline->firstCreated))->format('Y-m-d H:i:s'),
                    ]);
                }

            } else {

                $this->setError('Cannot find a headline id');
                return false;
            }
        }

        $this->headlines = array_values($headlines);
        $this->counter++;

        return true;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return !empty($this->response->headlines) && $this->prepare();
    }

    /**
     * @param string $uri
     * @return array
     */
    private function getQueryParams($uri)
    {
        $this->config->removeQueryDataParam();

        parse_str(parse_url($uri, PHP_URL_QUERY), $params);

        return ArrayHelper::merge($params, $this->config->apiParams);
    }

    /**
     * @param string $error
     */
    private function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}