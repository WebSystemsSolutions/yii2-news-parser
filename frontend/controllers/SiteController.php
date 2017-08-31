<?php

namespace frontend\controllers;

use common\models\Headline;
use frontend\helpers\HeadlinesHelper;
use frontend\models\forms\SearchForm;
use yii\base\Module;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @var HeadlinesHelper
     */
    private $headlineHelper;

    /**
     * SiteController constructor.
     * @param string $id
     * @param Module $module
     * @param HeadlinesHelper $headline_helper
     * @param array $config
     */
    public function __construct($id, Module $module, HeadlinesHelper $headline_helper, array $config = [])
    {
        $this->headlineHelper = $headline_helper;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $values = [];
        $prices = [];
        $price  = [];
        $headlines = [];

        $model = new SearchForm();
        $model->load(Yii::$app->request->get());

        if ($model->validate()) {

            $prices    = $this->headlineHelper->getHeadlinesChartPrices($model);
            $headlines = $this->headlineHelper->getHeadlines($model);
            $values    = $this->headlineHelper->getHeadlinesValue($headlines, $price);
        }

        return $this->render('index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $headlines,
            ]),
            'values' => $values,
            'prices' => $prices,
            'price'  => $price,
            'model'  => $model,
        ]);
    }

    /**
     * @param string $id
     * @param string $keyword
     * @return string|\yii\web\Response
     */
    public function actionView($id, $keyword)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $keyword),
        ]);
    }

    /**
     * @param string $id
     * @param string $keyword
     * @return Headline
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $keyword)
    {
        if (($model = $this->headlineHelper->getHeadline($keyword, $id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Cannot find an article');
    }
}
