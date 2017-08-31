<?php
namespace backend\controllers;

use backend\models\forms\ExportForm;
use backend\services\ArchiveInterface;
use backend\models\config\ArchiveConfig;
use common\models\config\GeneratorConfig;
use common\services\FileConverterInterface;
use common\services\HeadlinesManagerInterface;
use common\services\HeadlinesPricesManagerInterface;
use yii\base\Module;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;

/**
 * Class HeadlinesExportController
 * @package backend\controllers
 */
class ExportController extends Controller
{
    /**
     * @var FileConverterInterface
     */
    private $converter;

    /**
     * @var ArchiveInterface
     */
    private $archiver;

    /**
     * @var HeadlinesManagerInterface
     */
    private $headlineManager;

    /**
     * @var HeadlinesPricesManagerInterface
     */
    private $priceManager;

    /**
     * @var GeneratorConfig
     */
    private $generatorConfig;

    /**
     * @var ArchiveConfig
     */
    private $archiverConfig;

    /**
     * HeadlinesExportController constructor.
     * @param string $id
     * @param Module $module
     * @param FileConverterInterface $converter
     * @param ArchiveInterface $archiver
     * @param HeadlinesManagerInterface $headline_manager
     * @param HeadlinesPricesManagerInterface $price_manager
     * @param array $config
     */
    public function __construct($id, Module $module, FileConverterInterface $converter, ArchiveInterface $archiver, HeadlinesManagerInterface $headline_manager, HeadlinesPricesManagerInterface $price_manager, array $config = [])
    {
        $this->generatorConfig = new GeneratorConfig(Yii::$app->params['generator']);
        $this->archiverConfig = new ArchiveConfig(Yii::$app->params['archive']);

        $this->converter = $converter;
        $this->archiver  = $archiver;

        $this->headlineManager = $headline_manager;
        $this->priceManager = $price_manager;

        $this->converter->setConfig($this->generatorConfig);
        $this->archiver->setConfig($this->archiverConfig);

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
                    'export' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionProcess()
    {
        //set a path for an archive where get files
        $this->archiverConfig->filesPath = $this->generatorConfig->headlineFilesPath;
        $this->converter->setManager($this->headlineManager);

        return $this->processExport(true);
    }

    /**
     * @return string
     */
    public function actionPriceProcess()
    {
        //set a path for an archive where get files
        $this->archiverConfig->filesPath = $this->generatorConfig->priceFilesPath;
        $this->converter->setManager($this->priceManager);

        return $this->processExport(false);
    }

    /**
     * @param boolean $is_headlines
     *
     * @return $this|string|\yii\web\Response
     */
    private function processExport($is_headlines)
    {
        $model = new ExportForm(['config' => $this->generatorConfig, 'isHeadlines' => $is_headlines]);

        $model->load(Yii::$app->request->get(), '');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $this->clearDirectories();

            while ($keyword = $model->processKeyword()) {

                if (!$this->converter->begin($model->from, $model->to) || !$this->archiver->archive($keyword)) {
                    goto end;
                }

                $this->converter->clear();
            }

            //change directory for archiving all archives
            $this->archiverConfig->filesPath = $this->archiverConfig->path;

            if (($archive = $this->archiver->archive(($is_headlines ? 'articles' : 'prices') . "($model->from - $model->to)")) !== false) {

                $response = Yii::$app->response->sendFile($archive);

                $this->clearDirectories();
                return $response;
            }

            end:

            $this->clearDirectories();

            Yii::$app->session->setFlash('error', 'Something went wrong with an export');
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('form', [
            'model' => $model,
        ]);
    }

    /**
     *
     */
    private function clearDirectories()
    {
        $this->converter->clear();
        $this->archiver->clear();
    }
}
