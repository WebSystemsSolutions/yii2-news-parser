<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 08.04.2017
 * Time: 19:01
 */

namespace common\services;

use Goutte\Client as Goutte;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use yii\helpers\Json;

/**
 * Class Parser
 * @package common\services
 */
class Parser implements ParserInterface
{
    /**
     * @var Goutte
     */
    private $goutte;

    /**
     * @var string
     */
    private $base_uri;

    /**
     * @var string
     */
    private $current_uri;

    /**
     * @var array
     */
    private $login_fields = [];

    /**
     * @var boolean
     */
    private $logged;

    /**
     * @const
     */
    const TIMEOUT = 15;

    /**
     * @const string
     */
    const LOGIN_FORM      = "form[name='Login']";

    /**
     * @const string
     */
    const RESPONSE_FORM   = "form[name='Response']";

    /**
     * @const string
     */
    const STORY_CONTAINER = "div[id='storyContent']";

    /**
     * @return Goutte
     */
    private function goutte()
    {
        if (!$this->goutte) {

            $client = new Client([
                'defaults' => [
                    'timeout'         => self::TIMEOUT,
                    'cookies'         => true,
                    'allow_redirects' => true,
                ],
            ]);

            $client->getEmitter()->on('complete', function(CompleteEvent $e){
                $this->current_uri = ($response = $e->getResponse()) ? $response->getEffectiveUrl() : '';
            });

            $this->goutte = new Goutte();
            $this->goutte->setClient($client);
            $this->goutte->setMaxRedirects(7);
        }

        return $this->goutte;
    }

    /**
     * @inheritdoc
     */
    public function login($uri, $login, $pwd)
    {
        if (!$this->logged) {

            try {

                $crawler = $this->goutte()->request('get', $this->base_uri = $uri);

                if (!($form = $this->getLoginForm($crawler))) {
                    return false;
                }

                $this->login_fields = [
                    'IDToken1' => $login,
                    'IDToken2' => $pwd,
                    'IDToken3' => 'TRUE',
                    'IDButton' => 'Submit'
                ];

                foreach (array_diff(array_keys($form->getPhpValues()), array_keys($this->login_fields)) as $name) {
                    $form->remove($name);
                }

                $form->getNode()->setAttribute('action', $this->current_uri);

                $crawler = $this->goutte()->submit($form, $this->login_fields);

                if ($form = $this->getResponseForm($crawler)) {
                    return $this->checkLogin($form);
                }

                if (!($form = $this->getLoginForm($crawler))) {
                    return false;
                }

                $crawler = $this->goutte()->submit($form, [
                    'IDButton' => 'Sign In'
                ]);

                if (!($form = $this->getResponseForm($crawler))) {
                    return false;
                }

                return $this->logged = $this->checkLogin($form);

            } catch (\Exception $e) {
                return false;
            }
        }

        return $this->logged;
    }

    /**
     * @param Form $form
     * @return bool|array
     */
    private function checkLogin(Form $form)
    {
        $crawler = $this->goutte->submit($form);

        /**@var Response $response*/
        if (($response = $this->goutte->getResponse()) && $response->getStatus() == 200) {

            return !$this->getLoginForm($crawler) && !$this->getResponseForm($crawler);
        }

        return false;
    }

    /**
     * @param Crawler $crawler
     * @return null|Form
     */
    private function getLoginForm(Crawler $crawler)
    {
       return $this->getForm($crawler, self::LOGIN_FORM);
    }

    /**
     * @param Crawler $crawler
     * @return null|Form
     */
    private function getResponseForm(Crawler $crawler)
    {
       return $this->getForm($crawler, self::RESPONSE_FORM);
    }

    /**
     * @param Crawler $crawler
     * @param string $identify
     * @return null|Form
     */
    private function getForm(Crawler $crawler, $identify)
    {
        $node = $crawler->filter($identify);

        if ($node->count()) {
            return $node->form();
        }

        return null;
    }

    /**
     * @param Crawler $crawler
     * @return bool|mixed
     */
    private function loginAgain(Crawler $crawler)
    {
        if ($form = $this->getLoginForm($crawler)) {

            $crawler = $this->goutte->submit($form, $this->login_fields);

            if ($form = $this->getResponseForm($crawler)) {
                return $this->checkLogin($form);
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getHeadlines($uri, array $query_params = [], array $body_params = [])
    {
        try {

            load:
            $response = $this->goutte->getClient()->post($uri = $this->toRoute($uri), [
                'body'    => $body_params,
                'query'   => $query_params,
                'cookies' => $this->getCookies($uri),
            ]);

            if ($response->getStatusCode() == 200) {

                if ($data = $this->getJson($response)) {
                    return $data;
                }

                if ($this->isNeededLogin($response)) {
                    goto load;
                }
            }

        } catch (\Exception $e) {}

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getHeadlineContent($uri, array $query_params = [])
    {
        try {

            load:
            $response  = $this->goutte->getClient()->get($uri = $this->toRoute($uri), [
                'timeout' => self::TIMEOUT * 12,
                'cookies' => $this->getCookies($uri),
                'query'   => $query_params
            ]);

            if ($response->getStatusCode() == 200) {

                if ($this->isNeededLogin($response, $crawler)) {
                    goto load;
                }

                /**@var Crawler $crawler*/
                $node = $crawler->filter(self::STORY_CONTAINER);

                if ($node->count()) {
                    return $node->html();
                }

                return '';
            }

        } catch (\Exception $e) {}

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getHeadlinePrices($uri, array $body_params = [])
    {
        try {

            load:
            $response = $this->goutte->getClient()->post($uri = $this->toRoute($uri), [
                'json'    => $body_params,
                'cookies' => $this->getCookies($uri),
            ]);

            if ($response->getStatusCode() == 200) {

                if ($data = $this->getJson($response)) {

                    if (isset($data->R) && ($data = current($data->R)) && isset($data->Data, $data->Info) && ($info = current($data->Info)) && !isset($info->Error)) {

                        $prices = [];

                        /** @var $row \stdClass */
                        foreach ((array)$data->Data as $row) {

                            if (!empty($row->Date) && !empty($row->Close) && is_numeric($row->Close)) {

                                $info = new \stdClass();
                                $info->date = (new \DateTime($row->Date))->format('Y-m-d');
                                $info->price = $row->Close;

                                $prices[] = $info;
                            }
                        }

                        return array_reverse($prices);
                    }

                } else
                if ($this->isNeededLogin($response)) {
                    goto load;
                }
            }

        } catch (\Exception $e) {}

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getHeadlineCompanies($uri, array $body_params = [])
    {
        try {

            load:
            $response = $this->goutte->getClient()->post($uri = $this->toRoute($uri), [
                'json'    => $body_params,
                'cookies' => $this->getCookies($uri),
            ]);

            if ($response->getStatusCode() == 200) {

                if ($data = $this->getJson($response, false)) {

                    $companies = [];

                    if (!empty($data->Result) && !empty($data->Result->ItemDescs)) {

                        /** @var $row \stdClass */
                        foreach ((array)$data->Result->ItemDescs as $row) {

                            if (!empty($row->Ric)) {
                                $companies[] = $row->Ric;
                            }
                        }
                    }

                    return $companies;
                }

                if ($this->isNeededLogin($response)) {
                    goto load;
                }
            }

        } catch (\Exception $e) {}

        return null;
    }

    /**
     * @param ResponseInterface $response
     * @param Crawler|null $crawler
     *
     * @return bool
     */
    private function isNeededLogin(ResponseInterface $response, Crawler &$crawler = null)
    {
        $crawler = new Crawler(null, $this->current_uri);
        $crawler->addContent(($body = $response->getBody()) ? $body->getContents() : '');

        if ($this->loginAgain($crawler)) {
            return true;
        }

        return false;
    }

    /**
     * @param ResponseInterface $response
     * @param boolean $checkJson
     *
     * @return mixed|null
     */
    private function getJson(ResponseInterface $response, $checkJson = true)
    {
        if ($checkJson && !preg_match('~'.preg_quote(\yii\web\Response::FORMAT_JSON).'~', $response->getHeader('Content-Type'))) {
            return null;
        }

        try {
            return Json::decode($response->getBody(), false);
        } catch (\Exception $e) {}

        return null;
    }

    /**
     * @param string $uri
     * @return CookieJar
     */
    private function getCookies($uri)
    {
        return CookieJar::fromArray($this->goutte->getCookieJar()->allRawValues($uri), parse_url($uri, PHP_URL_HOST));
    }

    /**
     * @param string $uri
     * @return string
     */
    private function toRoute($uri)
    {
        if ($host = parse_url($uri, PHP_URL_HOST)) {
            return $uri;
        }

        return join([$this->base_uri, $uri]);
    }
}