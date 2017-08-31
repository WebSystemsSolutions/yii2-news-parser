<?php
namespace backend\controllers;

use backend\models\HeadlinesSearch;
use common\helpers\KeywordHelper;
use common\models\Headline;
use common\models\db\Headlines;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;


/**
 * Class HeadlinesController
 * @package backend\controllers
 */
class HeadlinesController extends Controller
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
        $searchModel  = new HeadlinesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'keywords'     => KeywordHelper::getDropDownHeadlinesKeywords(),
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Headline([
            'isNewRecord' => true,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $headline = new Headlines();
            $headline->setAttributes($model->getAttributes(), false);

            if($headline->save(false)) {

                Yii::$app->session->setFlash('success', 'The article has been successfully created');
                return $this->redirect(['update', 'id' => $headline->id]);
            }

            $model->addErrors($headline->getErrors());
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $id
     * @param string $keyword
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id, $keyword)
    {
        $headline = $this->findModel($id, $keyword);
        $model = new Headline($headline->getAttributes());

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $headline->setAttributes($model->getAttributes(), false);

            if($headline->save(false)) {

                Yii::$app->session->setFlash('success', 'The article has been successfully updated');
                return $this->refresh();
            }

            $model->addErrors($headline->getErrors());
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param string $id
     * @param string $keyword
     * @return \yii\web\Response
     */
    public function actionDelete($id, $keyword)
    {
        $this->findModel($id, $keyword)->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param string $id
     * @param string $keyword
     * @return Headlines
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $keyword)
    {
        if (($model = Headlines::findOne(['id' => $id, 'keyword' => $keyword])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Cannot find an article');
    }
}
