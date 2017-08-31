<?php

/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 13.04.2017
 * Time: 13:08
 */
namespace backend\controllers;

use backend\models\forms\ParserForm;
use common\models\config\ParserConfig;
use common\services\PriceReceiverInterface;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;

/**
 * Class HeadlinesPriceParserController
 * @package console\controllers
 */
class HeadlinesPriceParserController extends Controller
{
    /**
     * @var PriceReceiverInterface
     */
    private $receiver;

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
                    'load' => ['post'],
                ],
            ],
        ];
    }

    /**
     * ParserController constructor.
     * @param string $id
     * @param Module $module
     * @param PriceReceiverInterface $receiver
     * @param array $config
     */
    public function __construct($id, Module $module, PriceReceiverInterface $receiver, array $config = [])
    {
        $this->receiver = $receiver;
        $this->receiver->setConfig(new ParserConfig(Yii::$app->params['parser']));

        parent::__construct($id, $module, $config);
    }

    /**
     * @param string $type
     * @return string|\yii\web\Response
     */
    public function actionLoad($type)
    {
        $model = new ParserForm();

        if ($model->load(Yii::$app->request->get(), '') && $model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($this->receiver->begin($type, $model->keyword)) {
                Yii::$app->session->setFlash('success', 'Prices have been successfully parsed');
            } else {
                Yii::$app->session->setFlash('error', 'Something went wrong with parsing prices');
            }

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('load', [
            'model' => $model,
        ]);
    }
}