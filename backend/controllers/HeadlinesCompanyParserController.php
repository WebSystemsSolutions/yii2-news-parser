<?php

/**
 * Created by PhpStorm.
 * User: kastiel
 * Date: 13.04.2017
 * Time: 13:08
 */
namespace backend\controllers;

use common\models\config\ParserConfig;
use common\models\HeadlineCompany;
use common\services\HeadlinesCompanyManagerInterface;
use common\services\ReceiverInterface;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;

/**
 * Class HeadlinesPriceParserController
 * @package console\controllers
 */
class HeadlinesCompanyParserController extends Controller
{
    /**
     * @var ReceiverInterface
     */
    private $receiver;

    /**
     * @var HeadlinesCompanyManagerInterface
     */
    private $companyManager;

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
     * @param ReceiverInterface $receiver
     * @param HeadlinesCompanyManagerInterface $company_manager
     * @param array $config
     */
    public function __construct($id, Module $module, ReceiverInterface $receiver, HeadlinesCompanyManagerInterface $company_manager, array $config = [])
    {
        $this->receiver = $receiver;
        $this->receiver->setConfig(new ParserConfig(Yii::$app->params['parser']));

        $this->companyManager = $company_manager;

        parent::__construct($id, $module, $config);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionLoad()
    {
        if ($companies = $this->receiver->getCompanies()) {

            $companies = array_map(
                function($company){ return new HeadlineCompany(['keyword' => $company]); }, array_filter($companies, function($company) {
                    //can be done some improvements for checking if a company exists
                    return !$this->companyManager->exists($company);
                })
            );

            if($this->companyManager->save($companies)) {

                Yii::$app->session->setFlash('success', 'Companies have been successfully parsed');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        Yii::$app->session->setFlash('error', 'Something went wrong with parsing companies');
        return $this->redirect(Yii::$app->request->referrer);
    }
}