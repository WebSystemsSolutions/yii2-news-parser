<?php

namespace backend\models;

use common\models\Headline;
use common\models\db\Headlines;
use yii\data\ActiveDataProvider;

/**
 * Class ProductSearch
 * @package backend\models
 */
class HeadlinesSearch extends Headline
{
    /**
     * @var string
     */
    public $dateFrom;

    /**
     * @var string
     */
    public $dateTo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'keyword', 'title', 'language'], 'string'],
            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:Y-m-d'],
            ['dateTo', 'compare', 'compareAttribute' => 'dateFrom', 'operator' => '>='],
            [['value_1', 'value_7', 'value_14', 'value_28'], 'number'],
            ['created', 'safe'],
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->created && $range = explode(' - ', $this->created)) {

            $this->dateFrom = current($range);
            $this->dateTo   = end($range);
        }

        return parent::beforeValidate();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidParamException
     */
    public function search($params)
    {
        $query = Headlines::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'forcePageParam' => false,
            ],
            'sort'  => [
                'attributes' => [
                    'value',
                    'created',
                ],
                'defaultOrder' => [
                    'created' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {

            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['keyword' => $this->keyword])
            ->andFilterWhere(['language' => $this->language])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['value_1' => $this->value_1 ? $this->value_1 * Headlines::COEFFICIENT : ''])
            ->andFilterWhere(['value_7' => $this->value_7 ? $this->value_7 * Headlines::COEFFICIENT : ''])
            ->andFilterWhere(['value_14' => $this->value_14 ? $this->value_14 * Headlines::COEFFICIENT : ''])
            ->andFilterWhere(['value_28' => $this->value_28 ? $this->value_28 * Headlines::COEFFICIENT : ''])
            ->andFilterWhere(['>=', 'DATE(created)', $this->dateFrom])
            ->andFilterWhere(['<=', 'DATE(created)', $this->dateTo]);

        return $dataProvider;
    }
}
