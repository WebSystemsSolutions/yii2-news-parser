<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 4/19/17
 * Time: 1:38 PM
 */

namespace common\services;

use common\models\config\GeneratorConfig;
use common\models\config\DateRange;

/**
 * Interface FileConverterInterface
 * @package common\services
 */
interface FileConverterInterface
{
    /**
     * @param string $date_from
     * @param string $date_to
     * @param callable|null $callback_log a function is used to show information
     * @return bool
     */
    public function begin($date_from, $date_to, callable $callback_log = null);

    /**
     * @return DateRange|null
     */
    public function getDateRange();

    /**
     * @param GeneratorConfig $config
     * @return mixed
     */
    public function setConfig(GeneratorConfig $config);

    /**
     * @param ManagerInterface $manager
     * @return mixed
     */
    public function setManager(ManagerInterface $manager);

    /**
     * @param callable|null $callback_log a function is used to show information
     * @return bool
     */
    public function clear(callable $callback_log = null);
}