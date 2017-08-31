<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 09.05.2016
 * Time: 15:06
 */

namespace common\models\db\query;

/**
 * Class ActiveQuery
 * @package common\models\db\query
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param bool|true $active
     *
     * @return \yii\db\ActiveQuery
     */
    public function active($active = true)
    {
        return $this->andWhere(['active' => $active]);
    }
}