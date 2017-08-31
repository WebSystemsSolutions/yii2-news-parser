<?php

namespace backend\models;

use common\models\db\Headlines;
use common\models\db\HeadlinesCompanies;
use common\models\HeadlineCompany;
use yii\data\ActiveDataProvider;

/**
 * Class HeadlinesCompanySearch
 * @package backend\models
 */
class HeadlinesCompanySearch extends HeadlineCompany
{
    /**
     * @var
     */
    public $countRecords;

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
            ['active', 'boolean'],
            ['keyword', 'string', 'max' => 255],
            ['dateFrom', 'required', 'when' => function(){
                return $this->dateTo;
            }],
            ['dateTo', 'required', 'when' => function(){
                return $this->dateFrom;
            }],
            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:Y-m-d'],
            ['dateTo', 'compare', 'compareAttribute' => 'dateFrom', 'operator' => '>='],
            [['countRecords'], 'safe'],
        ];
    }

    /**
     * @param $params
     *
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidParamException
     */
    public function search($params)
    {
        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'forcePageParam' => false,
            ],
            'sort' => [
                'attributes' => [
                    'keyword',
                    'active',
                    'countRecords',
                ],
                'defaultOrder' => [
                    'countRecords' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {

            $dataProvider->query = HeadlinesCompanies::find()->where('0=1');
            return $dataProvider;
        }

        $headlines_query = Headlines::find()
            ->alias('h')
            ->select('count(*)')
            ->where('h.keyword = c.keyword')
            ->andFilterWhere(['>=', 'DATE(h.created)', $this->dateFrom])
            ->andFilterWhere(['<=', 'DATE(h.created)', $this->dateTo]);

        $query = HeadlinesCompanies::find()
            ->alias('c')
            ->select(['c.*', 'countRecords' => $headlines_query])
            ->joinWith('headlines h', false)
            ->andFilterWhere(['active' => $this->active])
            ->andFilterWhere(['like', 'c.keyword', $this->keyword])
            ->andFilterWhere(['>=', 'DATE(h.created)', $this->dateFrom])
            ->andFilterWhere(['<=', 'DATE(h.created)', $this->dateTo])
            ->groupBy(['c.id'])
            ->andFilterHaving(['>=', 'countRecords', $this->countRecords]);

        $query
            ->asArray();

        $dataProvider->query = $query;
        return $dataProvider;
    }
}
