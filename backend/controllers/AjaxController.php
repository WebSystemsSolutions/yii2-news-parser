<?php

namespace backend\controllers;

use common\helpers\KeywordHelper;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\helpers\DepDropKeywordHelper;

/**
 * Class AjaxController
 * @package frontend\controllers
 */
class AjaxController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException('Cannot find a page.');
        }

        return parent::beforeAction($action);
    }

    /**
     * @param bool $headlines
     *
     * @return array
     */
    public function actionBuildKeywords($headlines)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($headlines) {
            $keywords = KeywordHelper::getDropDownHeadlinesKeywords(false);
        } else {
            $keywords = KeywordHelper::getDropDownPricesKeywords(false);
        }

        return DepDropKeywordHelper::build(
            Yii::$app->request->post('depdrop_parents'),
            Yii::$app->request->post('depdrop_params'),
            $keywords
        );
    }
}