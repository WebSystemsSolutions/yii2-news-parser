<?php
/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 10.04.2017
 * Time: 12:06
 */

namespace common\models\db;

use common\models\db\query\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class HeadlinesCompanies
 * @property string $id
 * @property string $keyword
 * @property boolean $active
 * @property Headlines[] $headlines
 *
 * @package common\models\db
 */
class HeadlinesCompanies extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%headlines_companies}}';
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }

    /**
     *
     */
    public function afterDelete()
    {
        parent::afterDelete();

        Headlines::deleteAll(['keyword' => $this->keyword]);
        HeadlinesPrices::deleteAll(['keyword' => $this->keyword]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHeadlines()
    {
        return $this->hasMany(Headlines::className(), ['keyword' => 'keyword']);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'keyword' => 'Name',
        ];
    }
}