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
 * Interface PriceReceiverInterface
 * @package common\services
 */
interface PriceReceiverInterface
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
     * @param ParserConfig $config
     *
     * @return void
     */
    public function setConfig(ParserConfig $config);
}