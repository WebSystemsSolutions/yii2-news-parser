<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 7/28/17
 * Time: 1:54 PM
 */

namespace common\services;

use common\models\HeadlineCompany;

/**
 * Interface CompanyManagerInterface
 * @package common\services
 */
interface HeadlinesCompanyManagerInterface
{
    /**
     * @param HeadlineCompany[] $objects
     * @return bool
     */
    public function save($objects);

    /**
     * @return string
     */
    public function getError();

    /**
     * @param string $keyword
     * @return bool
     */
    public function exists($keyword);

    /**
     * @param string $keyword
     * @return HeadlineCompany[]
     */
    public function iterateRange($keyword);
}