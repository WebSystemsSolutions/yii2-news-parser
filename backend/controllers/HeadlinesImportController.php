<?php

namespace backend\controllers;

use backend\models\config\ImportConfig;
use backend\models\forms\HeadlinesImportForm;
use backend\services\FileImportInterface;
use yii\base\Module;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;

/**
 * Class HeadlinesImportController
 * @package backend\controllers
 */
class HeadlinesImportController extends Controller
{
    /**
     * @var FileImportInterface
     */
    private $importer;

    /**
     * HeadlinesExportController constructor.
     * @param string $id
     * @param Module $module
     * @param FileImportInterface $importer
     * @param array $config
     */
    public function __construct($id, Module $module, FileImportInterface $importer, array $config = [])
    {
        $this->importer = $importer;
        $this->importer->setConfig(new ImportConfig(Yii::$app->params['import']));

        parent::__construct($id, $module, $config);
    }

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'import' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws \yii\base\InvalidParamException
     */
    public function actionImport()
    {
        $model = new HeadlinesImportForm();

        if ($model->upload()) {

            if (!$this->importer->split($model->file->tempName)) {

                Yii::$app->session->setFlash('error', 'Something went wrong with an import');
                return $this->redirect(Yii::$app->request->referrer);
            }

            return $this->redirect([$this->id . '/batch-import']);
        }

        return $this->renderAjax('import', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionBatchImport()
    {
        if (($update = $this->importer->update()) !== false) {

            if ($update) {
                return $this->redirect([$this->id . '/batch-import']);
            }

            Yii::$app->session->setFlash('success', 'Articles where updated');
        } else {
            Yii::$app->session->setFlash('error', 'Something went wrong with an import');
        }

        return $this->redirect(['headlines/index']);
    }
}
