<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 16.04.2017
 * Time: 11:29
 */

namespace common\services;

use common\models\Headline;

/**
 * Interface HeadlinesManagerInterface
 * @package common\services
 */
interface HeadlinesManagerInterface extends ManagerInterface
{
    /**
     * @param string $keyword
     * @param string $id
     *
     * @return null|Headline
     */
    public function getHeadline($keyword, $id);
}