<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\models\config\TypeKeywordConfig;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HeadlinesCompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Companies';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
    Modal::begin([
        'id'   => 'modal-popup',
        'size' => Modal::SIZE_LARGE,
    ]);
    Modal::end();
?>

<div class="headlines-company-index">

    <div>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-primary']); ?>&nbsp;
        <?= Html::a('Parse',  ['headlines-company-parser/load'], ['class' => 'btn btn-success popup']); ?>&nbsp;
        <?= Html::a('Export', [
            'export/process',
            'filterByCountRecords' => true,
            'countRecords'         => $searchModel->countRecords,
            'type'                 => TypeKeywordConfig::TYPE_COMPANIES,
            'keyword'              => $searchModel->keyword,
            'from'                 => $searchModel->dateFrom,
            'to'                   => $searchModel->dateTo,
        ], ['class' => 'btn btn-success popup']
        ); ?>&nbsp;

        <?= $this->render('_filter', [
            'model' => $searchModel
        ]); ?>

    </div>

    <?= GridView::widget([
        'pager' => [
            'firstPageLabel' => 'First',
            'lastPageLabel'  => 'Last'
        ],
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'keyword',
            'active:boolean',
            'countRecords',
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'headerOptions' => [
                    'style' => 'width: 50px;'
                ],
            ],
        ],
    ]); ?>

</div>
