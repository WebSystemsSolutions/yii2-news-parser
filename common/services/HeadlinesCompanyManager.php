<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 7/28/17
 * Time: 1:57 PM
 */

namespace common\services;

use common\models\db\HeadlinesCompanies;
use common\models\HeadlineCompany;
use common\models\HeadlinesManagerIterator;

/**
 * Class HeadlinesCompanyManager
 * @package common\services
 */
class HeadlinesCompanyManager implements HeadlinesCompanyManagerInterface
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

            $headline_company = new HeadlinesCompanies();
            $headline_company->setAttributes($object->getAttributes(), false);

            if (!$headline_company->save(false)) {

                $this->setError('Cannot save a company');
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $keyword
     * @return mixed
     */
    public function exists($keyword)
    {
        return HeadlinesCompanies::find()
            ->where(['keyword' => $keyword])
            ->exists();
    }

    /**
     * @param string $keyword
     * @return HeadlinesManagerIterator
     */
    public function iterateRange($keyword)
    {
        return new HeadlinesManagerIterator($this->prepareRangeBatch($keyword), [$this, 'prepareHeadlinesCompanies']);
    }

    /**
     * @param string $keyword
     * @param int $limit
     * @return \yii\db\BatchQueryResult
     */
    private function prepareRangeBatch($keyword, $limit = 1000)
    {
        return HeadlinesCompanies::find()
            ->where(['keyword' => $keyword])
            ->batch($limit);
    }

    /**
     * @param HeadlinesCompanies[] $companies
     * @return array
     */
    public function prepareHeadlinesCompanies($companies)
    {
        $objects = [];

        foreach ($companies as $company) {
            $objects[] = new HeadlineCompany($company->getAttributes());
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