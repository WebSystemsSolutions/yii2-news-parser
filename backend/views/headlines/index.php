<?php

use yii\grid\GridView;
use common\models\db\Headlines;
use yii\helpers\Html;
use common\models\config\ParserConfig;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HeadlinesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $keywords array */

$this->title = 'Articles';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
    Modal::begin([
        'id'   => 'modal-popup',
        'size' => Modal::SIZE_LARGE,
    ]);
    Modal::end();
?>

<div class="headlines-index">

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-primary']); ?>&nbsp;
        <?= Html::a('Parse new', ['headlines-parser/load', 'type' => ParserConfig::PAGE_TYPE_NEW, 'keyword' => $searchModel->keyword], ['class' => 'btn btn-success popup']); ?>&nbsp;
        <?= Html::a('Parse old', ['headlines-parser/load', 'type' => ParserConfig::PAGE_TYPE_OLD, 'keyword' => $searchModel->keyword], ['class' => 'btn btn-success popup']); ?>&nbsp;
        <?= Html::a('Export',    ['export/process', 'keyword' => $searchModel->keyword, 'from' => $searchModel->dateFrom, 'to' => $searchModel->dateTo], ['class' => 'btn btn-success popup']); ?>&nbsp;
        <?= Html::a('Import',    ['headlines-import/import'], ['class' => 'btn btn-success popup']); ?>
    </p>

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
            'id',
            [
                'attribute' => 'keyword',
                'filter' => Select2::widget([
                    'model'     => $searchModel,
                    'attribute' => 'keyword',
                    'data'      => $keywords,
                    'options'   => [
                        'placeholder' => 'Select',
                    ],
                ]),
                'headerOptions' => ['style' => 'width: 130px;']
            ],
            [
                'attribute' => 'language',
                'headerOptions' => ['style' => 'width: 20px;']
            ],
            [
                'attribute' => 'title',
                'format'    => 'raw',
                'value'     => function(Headlines $model){
                    return \yii\helpers\StringHelper::truncate($model->title, 50);
                },
                'headerOptions' => ['style' => 'min-width: 350px;']
            ],
            'value_1',
            'value_7',
            'value_14',
            'value_28',
            [
                'attribute' => 'created',
                'filter'    => DateRangePicker::widget([
                    'model'     => $searchModel,
                    'attribute' => 'created',
                    'convertFormat' =>  true,
                    'pluginOptions' => [
                        'locale'  => ['format' => 'Y-m-d'],
                    ]
                ]),
            ],
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
