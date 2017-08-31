<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 10.04.2017
 * Time: 18:41
 */

namespace common\services;

use common\models\config\DateRange;
use common\models\Headline;
use common\models\db\Headlines;
use common\models\HeadlinesManagerIterator;

/**
 * Class HeadlinesManager
 * @package common\services
 */
class HeadlinesManager implements HeadlinesManagerInterface
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

            $headline = new Headlines();
            $headline->setAttributes($object->getAttributes(), false);

            if (!$headline->save(false)) {

                $this->setError('Cannot save a headline');
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
        return Headlines::find()
            ->where(['and', ['id' => $value], ['keyword' => $keyword]])
            ->exists();
    }

    /**
     * @inheritdoc
     */
    public function getDateRange($keyword, $date_from = null, $date_to = null)
    {
        $query = Headlines::find()
            ->select(['min(created) created_min', 'max(created) created_max'])
            ->where(['keyword' => $keyword])
            ->asArray();

        if ($date_from) {
            $query->andWhere(['>=', 'created', $this->convertToDateTime($date_from)->format('Y-m-d H:i:s')]);
        }

        if ($date_to) {
            $query->andWhere(['<=', 'created', $this->convertToDateTime($date_to, true)->format('Y-m-d H:i:s')]);
        }

        if ($info = array_filter($query->one())) {

            return new DateRange([
                'min' => $info['created_min'],
                'max' => $info['created_max'],
            ]);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function iterateRange($keyword, $date_from, $date_to)
    {
        return new HeadlinesManagerIterator($this->prepareRangeBatch($keyword, $date_from, $date_to), [$this, 'prepareHeadlines']);
    }

    /**
     * @inheritdoc
     */
    public function getRange($keyword, $date_from, $date_to)
    {
        $objects = [];

        foreach ($this->prepareRangeBatch($keyword, $date_from, $date_to) as $headlines) {
            $objects = array_merge($objects, $this->prepareHeadlines($headlines));
        }

        return $objects;
    }

    /**
     * @inheritdoc
     */
    private function prepareRangeBatch($keyword, $date_from, $date_to, $limit = 1000)
    {
        return Headlines::find()
            ->where([
                'and',
                ['>=', 'DATE(created)', $this->convertToDateTime($date_from)->format('Y-m-d')],
                ['<=', 'DATE(created)', $this->convertToDateTime($date_to)->format('Y-m-d')],
                ['keyword' => $keyword]
            ])
            ->orderBy([
                'created' => SORT_DESC
            ])
            ->batch($limit);
    }

    /**
     * @inheritdoc
     */
    public function getHeadline($keyword, $id)
    {
        /**@var $headline Headlines*/
        $headline = Headlines::find()
            ->where(['and', ['id' => $id], ['keyword' => $keyword]])
            ->one();

        return $this->prepareHeadline($headline);
    }

    /**
     * @param mixed $date
     * @param boolean $max_time
     * @return \DateTime
     */
    private function convertToDateTime($date, $max_time = false)
    {
        if (!($date instanceof \DateTime)) {

            $date = new \DateTime($date);

            if($max_time) {
                $date->setTime(23, 59, 59);
            }
        }

        return $date;
    }

    /**
     * @param Headlines[] $headlines
     * @return array
     */
    public function prepareHeadlines($headlines)
    {
        $objects = [];

        foreach ($headlines as $headline) {
            $objects[] = $this->prepareHeadline($headline);
        }

        return $objects;
    }

    /**
     * @param Headlines|null $headline
     * @return Headline|null
     */
    private function prepareHeadline($headline)
    {
        if ($headline) {
            return new Headline($headline->getAttributes());
        }

        return null;
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