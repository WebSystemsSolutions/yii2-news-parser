<?php
namespace backend\controllers;

use backend\models\HeadlinesCompanySearch;
use common\models\db\HeadlinesCompanies;
use common\models\HeadlineCompany;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Class HeadlinesCompanyController
 * @package backend\controllers
 */
class HeadlinesCompanyController extends Controller
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
        $searchModel  = new HeadlinesCompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new HeadlineCompany([
            'isNewRecord' => true,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $headline_company = new HeadlinesCompanies();
            $headline_company->setAttributes($model->getAttributes(), false);

            if($headline_company->save(false)) {

                Yii::$app->session->setFlash('success', 'The company has been successfully created');
                return $this->redirect(['update', 'id' => $headline_company->id]);
            }

            $model->addErrors($headline_company->getErrors());
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
        $headline_company = $this->findModel($id);
        $model = new HeadlineCompany($headline_company->getAttributes());

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $headline_company->setAttributes($model->getAttributes(), false);

            if($headline_company->save(false)) {

                Yii::$app->session->setFlash('success', 'The company has been successfully updated');
                return $this->refresh();
            }

            $model->addErrors($headline_company->getErrors());
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
     * @return HeadlinesCompanies
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = HeadlinesCompanies::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Cannot find a company');
    }
}
