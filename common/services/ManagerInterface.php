<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 11:29
 */

namespace common\services;

use common\models\config\DateRange;
use common\models\Headline;
use common\models\HeadlinePrice;

/**
 * Interface ManagerInterface
 * @package common\services
 */
interface ManagerInterface
{
    /**
     * @param Headline[]|HeadlinePrice[] $objects
     * @return bool
     */
    public function save($objects);

    /**
     * @return string
     */
    public function getError();

    /**
     * @param string $keyword
     * @param string $value
     * @return bool
     */
    public function exists($keyword, $value);

    /**
     * @param string $keyword
     * @param string|\DateTime $date_from
     * @param string|\DateTime $date_to
     * @return Headline[]|HeadlinePrice[]
     */
    public function iterateRange($keyword, $date_from, $date_to);

    /**
     * @param string $keyword
     * @param string|\DateTime $date_from
     * @param string|\DateTime $date_to
     * @return Headline[]|HeadlinePrice[]
     */
    public function getRange($keyword, $date_from, $date_to);

    /**
     * @param string $keyword
     * @param mixed|null $date_from
     * @param mixed|null $date_to
     * @return DateRange
     */
    public function getDateRange($keyword, $date_from = null, $date_to = null);
}