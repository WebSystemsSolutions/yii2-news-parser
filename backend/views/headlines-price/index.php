<?php

use yii\grid\GridView;
use yii\helpers\Html;
use common\models\config\ParserConfig;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HeadlinesPriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $keywords array */

$this->title = 'Prices';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
    Modal::begin([
        'id'   => 'modal-popup',
        'size' => Modal::SIZE_LARGE,
    ]);
    Modal::end();
?>

<div class="headlines-price-index">

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-primary']); ?>&nbsp;
        <?= Html::a('Parse new', ['headlines-price-parser/load', 'type' => ParserConfig::PAGE_TYPE_NEW, 'keyword' => $searchModel->keyword], ['class' => 'btn btn-success popup']); ?>&nbsp;
        <?= Html::a('Parse old', ['headlines-price-parser/load', 'type' => ParserConfig::PAGE_TYPE_OLD, 'keyword' => $searchModel->keyword], ['class' => 'btn btn-success popup']); ?>&nbsp;
        <?= Html::a('Export',    ['export/price-process', 'keyword' => $searchModel->keyword, 'from' => $searchModel->dateFrom, 'to' => $searchModel->dateTo], ['class' => 'btn btn-success popup']); ?>
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
            [
                'attribute' => 'keyword',
                'filter' => Select2::widget([
                    'model'      => $searchModel,
                    'attribute'  => 'keyword',
                    'data'       => $keywords,
                    'options' => [
                        'placeholder' => 'Select',
                    ]
                ]),
            ],
            'price',
            [
                'attribute' => 'date',
                'filter'    => DateRangePicker::widget([
                    'model'         => $searchModel,
                    'attribute'     => 'date',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => ['format' => 'Y-m-d'],
                    ],
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
