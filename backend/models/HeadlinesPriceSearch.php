<?php

namespace backend\models;

use common\models\HeadlinePrice;
use common\models\db\HeadlinesPrices;
use yii\data\ActiveDataProvider;

/**
 * Class HeadlinesPriceSearch
 * @package backend\models
 */
class HeadlinesPriceSearch extends HeadlinePrice
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
            [['keyword'], 'string'],
            ['price', 'number'],
            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:Y-m-d'],
            ['dateTo', 'compare', 'compareAttribute' => 'dateFrom', 'operator' => '>='],
            ['date', 'safe'],
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if ($this->date && $range = explode(' - ', $this->date)) {

            $this->dateFrom = current($range);
            $this->dateTo   = end($range);
        }

        return parent::beforeValidate();
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidParamException
     */
    public function search($params)
    {
        $query = HeadlinesPrices::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'forcePageParam' => false,
            ],
            'sort'  => [
                'attributes' => [
                    'price',
                    'date',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {

            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['keyword' => $this->keyword])
              ->andFilterWhere(['price' => $this->price ? $this->price * HeadlinesPrices::COEFFICIENT : ''])
              ->andFilterWhere(['>=', 'date', $this->dateFrom])
              ->andFilterWhere(['<=', 'date', $this->dateTo]);

        return $dataProvider;
    }
}
