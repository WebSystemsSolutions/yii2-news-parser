<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 11:37
 */

namespace common\services;

use common\models\config\ParserConfig;

/**
 * Interface ReceiverInterface
 * @package common\services
 */
interface ReceiverInterface
{
    /**
     * @param string|null $type
     * @param string|null $keyword
     * @param callable|null $callback_log
     * @param boolean $stopped
     * @return bool
     */
    public function begin($type = null, $keyword = null, callable $callback_log = null, &$stopped = false);

    /**
     * @param callable|null $callback_log
     * @return array|null
     */
    public function getCompanies(callable $callback_log = null);

    /**
     * @param ParserConfig $config
     */
    public function setConfig(ParserConfig $config);
}