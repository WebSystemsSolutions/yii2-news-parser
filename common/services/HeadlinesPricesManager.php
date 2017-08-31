<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 10.04.2017
 * Time: 18:41
 */

namespace common\services;

use common\models\config\DateRange;
use common\models\HeadlinePrice;
use common\models\db\HeadlinesPrices;
use common\models\HeadlinesManagerIterator;

/**
 * Class HeadlinesPricesManager
 * @package common\services
 */
class HeadlinesPricesManager implements HeadlinesPricesManagerInterface
{
    /**
     * @var string
     */
    private $error;

    /**
     * @inheritdoc
     */
    public function save($objects)
    {
        foreach ($objects as $object) {

            if (!$object->validate()) {

                $errors = $object->getFirstErrors();
                $this->setError(array_shift($errors));

                return false;
            }

            $headline_price = new HeadlinesPrices();
            $headline_price->setAttributes($object->getAttributes(), false);

            if (!$headline_price->save(false)) {

                $this->setError('Cannot save a headline price');
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function exists($keyword, $value)
    {
        return HeadlinesPrices::find()
            ->where(['and', ['keyword' => $keyword], ['date' => $value]])
            ->exists();
    }

    /**
     * @inheritdoc
     */
    public function getDateRange($keyword, $date_from = null, $date_to = null)
    {
        $query = HeadlinesPrices::find()
            ->select(['min(date) date_min', 'max(date) date_max'])
            ->where(['keyword' => $keyword])
            ->asArray();

        if ($date_from) {
            $query->andWhere(['>=', 'date', $this->convertToDateTime($date_from)->format('Y-m-d')]);
        }

        if ($date_to) {
            $query->andWhere(['<=', 'date', $this->convertToDateTime($date_to)->format('Y-m-d')]);
        }

        if ($info = array_filter($query->one())) {

            return new DateRange([
                'min' => $info['date_min'],
                'max' => $info['date_max'],
            ]);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function iterateRange($keyword, $date_from, $date_to)
    {
        return new HeadlinesManagerIterator($this->prepareRangeBatch($keyword, $date_from, $date_to), [$this, 'prepareHeadlinesPrices']);
    }

    /**
     * @inheritdoc
     */
    public function getRange($keyword, $date_from, $date_to)
    {
        $objects = [];

        foreach ($this->prepareRangeBatch($keyword, $date_from, $date_to) as $headlines) {
            $objects = array_merge($objects, $this->prepareHeadlinesPrices($headlines));
        }

        return $objects;
    }

    /**
     * @param string $keyword
     * @param string $date_from
     * @param string $date_to
     * @param int $limit
     * @return \yii\db\BatchQueryResult
     */
    private function prepareRangeBatch($keyword, $date_from, $date_to, $limit = 1000)
    {
        return HeadlinesPrices::find()
            ->where([
                'and',
                ['>=', 'date', $this->convertToDateTime($date_from)->format('Y-m-d')],
                ['<=', 'date', $this->convertToDateTime($date_to)->format('Y-m-d')],
                ['keyword' => $keyword]
            ])
            ->orderBy([
                'date' => SORT_ASC
            ])
            ->batch($limit);
    }

    /**
     * @param mixed $date
     * @return \DateTime
     */
    private function convertToDateTime($date)
    {
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }

        return $date;
    }

    /**
     * @param HeadlinesPrices[] $prices
     * @return array
     */
    public function prepareHeadlinesPrices($prices)
    {
        $objects = [];

        foreach ($prices as $price) {
            $objects[] = new HeadlinePrice($price->getAttributes());
        }

        return $objects;
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