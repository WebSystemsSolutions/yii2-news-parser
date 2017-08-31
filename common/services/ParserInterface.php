<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 11:14
 */

namespace common\services;

/**
 * Interface ParserInterface
 * @package common\services
 */
interface ParserInterface
{
    /**
     * @param string $uri
     * @param string $login
     * @param string $pwd
     * @return bool|array
     */
    public function login($uri, $login, $pwd);

    /**
     * @param string $uri
     * @param array $query_params
     * @param array $body_params
     * @return null|\stdClass[]
     */
    public function getHeadlines($uri, array $query_params = [], array $body_params = []);

    /**
     * @param string $uri
     * @param array $query_params
     * @return null|string
     */
    public function getHeadlineContent($uri, array $query_params = []);

    /**
     * @param string $uri
     * @param array $body_params
     * @return null|\stdClass[]
     */
    public function getHeadlinePrices($uri, array $body_params = []);

    /**
     * @param string $uri
     * @param array $body_params
     * @return null|array
     */
    public function getHeadlineCompanies($uri, array $body_params = []);
}