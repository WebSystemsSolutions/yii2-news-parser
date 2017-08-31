<?php
namespace backend\controllers;

use backend\models\HeadlinesPriceSearch;
use common\helpers\KeywordHelper;
use common\models\HeadlinePrice;
use common\models\db\HeadlinesPrices;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;


/**
 * Class HeadlinesPriceController
 * @package backend\controllers
 */
class HeadlinesPriceController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel  = new HeadlinesPriceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'keywords'     => KeywordHelper::getDropDownPricesKeywords(),
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new HeadlinePrice([
            'isNewRecord' => true,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $headline_price = new HeadlinesPrices();
            $headline_price->setAttributes($model->getAttributes(), false);

            if($headline_price->save(false)) {

                Yii::$app->session->setFlash('success', 'The price has been successfully created');
                return $this->redirect(['update', 'id' => $headline_price->id]);
            }

            $model->addErrors($headline_price->getErrors());
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $headline_price = $this->findModel($id);
        $model = new HeadlinePrice($headline_price->getAttributes());

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $headline_price->setAttributes($model->getAttributes(), false);

            if($headline_price->save(false)) {

                Yii::$app->session->setFlash('success', 'The price has been successfully updated');
                return $this->refresh();
            }

            $model->addErrors($headline_price->getErrors());
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     * @return HeadlinesPrices
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = HeadlinesPrices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Cannot find a price');
    }
}
