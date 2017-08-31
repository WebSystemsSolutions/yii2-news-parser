<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 5/4/17
 * Time: 10:21 AM
 */

namespace frontend\helpers;

use common\models\config\DateRange;
use common\models\Headline;
use common\services\HeadlinesManagerInterface;
use common\services\HeadlinesPricesManagerInterface;
use frontend\models\forms\SearchForm;

/**
 * Class HeadlinesHelper
 * @package frontend\helpers
 */
class HeadlinesHelper
{
    /**
     * @const
     */
    const DATE_FORMAT_SHORT = 'd-m-y';

    /**
     * @const
     */
    const DATE_FORMAT_LONG = 'd-m-Y';

    /**
     * @const
     */
    const TIME_FORMAT = 'H:i:s';

    /**
     * @const
     */
    const ROUND_PRECISION = 2;

    /**
     * @var HeadlinesManagerInterface
     */
    private $headlinesManager;

    /**
     * @var HeadlinesPricesManagerInterface
     */
    private $headlinesPriceManager;

    /**
     * HeadlinesHelper constructor.
     * @param HeadlinesManagerInterface $headlines_manager
     * @param HeadlinesPricesManagerInterface $headlines_price_manager
     */
    public function __construct(HeadlinesManagerInterface $headlines_manager, HeadlinesPricesManagerInterface $headlines_price_manager)
    {
        $this->headlinesManager = $headlines_manager;
        $this->headlinesPriceManager = $headlines_price_manager;
    }

    /**
     * @param SearchForm $model
     * @return \common\models\Headline[]
     */
    public function getHeadlines($model)
    {
        if ($range = $this->headlinesManager->getDateRange($model->keyword, $model->dateFrom, $model->dateTo)) {

            $this->shiftMinRangeOfDate($range, $model);

            $headlines = $this->headlinesManager->getRange($model->keyword, $range->min, $range->max);

            foreach ($headlines as &$headline) {

                $headline->created = $this->dateFormat($headline->created, true);
                $headline->value = round($headline->{"value_{$model->days}"}, self::ROUND_PRECISION);
            }

            return $headlines;
        }

        return [];
    }

    /**
     * @param SearchForm $model
     * @return array
     */
    public function getHeadlinesChartPrices($model)
    {
        $objects = [
            'prev' => [],
            'next' => [],
        ];

        if ($range = $this->headlinesPriceManager->getDateRange($model->keyword, $model->dateFrom, $model->dateTo)) {

            $this->shiftMinRangeOfDate($range, $model);
            $objects['prev'] = $this->getHeadlinesPrices($range, $model);

            $this->shiftMaxRangeOfDate($range, $model);
            $objects['next'] = $this->getHeadlinesPrices($range, $model);
        }

        return $objects;
    }

    /**
     * @param DateRange $range
     * @param SearchForm $model
     *
     * @return array
     */
    private function getHeadlinesPrices(DateRange $range, SearchForm $model)
    {
        $objects = [];

        foreach ($this->headlinesPriceManager->getRange($model->keyword, $range->min, $range->max) as $price) {

            $objects[] = [
                $this->dateFormat($price->date), round($price->price, self::ROUND_PRECISION)
            ];
        }

        return $objects;
    }

    /**
     * @param DateRange $range
     * @param SearchForm|null $model
     */
    private function shiftMinRangeOfDate(DateRange $range, $model)
    {
        $format = join($delimiter = '-', array_reverse(explode($delimiter, self::DATE_FORMAT_LONG)));

        $min = new \DateTime($range->min);
        $max = new \DateTime($range->max);

        $days = $min->diff($max)->days;

        if (!$days || (!$model->dateShifted && $days > $model->days)) {

            $date = new \DateTime($range->max);

            $date->setTime(0,0);
            $date->modify('-' . $model->days . ' days');

            $range->min = $date->format($format);
        }

        if (!$model->dateShifted) {

            $model->dateFrom = $this->dateFormat($range->min, false, self::DATE_FORMAT_LONG);
            $model->dateTo   = $this->dateFormat($range->max, false, self::DATE_FORMAT_LONG);

            $model->dateShifted = true;
        }

        $date = new \DateTime($range->min);
        $date->modify('-' . $model->days . ' days');

        $range->min = $date->format($format);
    }

    /**
     * @param \common\models\Headline[] $headlines
     * @param array $price
     * @return array
     */
    public function getHeadlinesValue(&$headlines, array &$price = [])
    {
        if (empty($headlines)) {
            return [];
        }

        $values = [];

        foreach ($headlines as $headline) {

            $date = strtok($headline->created, ' ');

            if (!array_key_exists($date, $values)) {

                $values[$date] = [
                    'value' => 0,
                    'count' => 0,
                ];
            }

            $values[$date]['value'] += $headline->value;
            $values[$date]['count']++;
        }

        foreach (array_keys($values) as $date) {
            $values[$date]['value'] = round($values[$date]['value'], self::ROUND_PRECISION);
        }

        $values = array_reverse($values);

        $last = array_slice($values, -1, 1, true) ;
        $date = key($last);

        foreach ($headlines as $k => $headline) {

            if($date != strtok($headline->created, ' ')) {
                unset($headlines[$k]);
            }
        }

        $price[$date] = $last[$date]['value'];

        return $values;
    }

    /**
     * @param string $keyword
     * @param string $id
     *
     * @return null|Headline
     */
    public function getHeadline($keyword, $id)
    {
        return $this->headlinesManager->getHeadline($keyword, $id);
    }

    /**
     * @param DateRange $range
     * @param SearchForm|null $model
     */
    private function shiftMaxRangeOfDate(DateRange $range, $model)
    {
        $format = join($delimiter = '-', array_reverse(explode($delimiter, self::DATE_FORMAT_LONG)));

        $min = new \DateTime($range->max);
        $min->modify('+1 days');

        $max = clone $min;
        $max->modify('+' . $model->days . ' days');

        $range->min = $min->format($format);
        $range->max = $max->format($format);
    }

    /**
     * @param string $date
     * @param bool $addTime
     * @param string $format
     * @return string
     */
    private function dateFormat($date, $addTime = false, $format = self::DATE_FORMAT_SHORT)
    {
        if ($addTime) {
            $format .= ' ' . self::TIME_FORMAT;
        }

        return date($format, strtotime($date));
    }
}