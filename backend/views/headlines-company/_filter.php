<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HeadlinesCompanySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="headlines-company-filter">

    <?php $form = ActiveForm::begin([
        'action'  => ['index'],
        'method'  => 'get',
        'options' => [
            'class' => 'form-inline'
        ],
    ]); ?>

    <?= $form->field($model, 'dateFrom')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_INPUT,
        'options' => [
            'class' => 'form-control',
            'placeholder' => $model->getAttributeLabel('dateFrom'),
        ],
        'pluginOptions' => [
            'format'         => 'yyyy-mm-dd',
            'autoclose'      => true,
            'todayHighlight' => true,
        ]
    ])->label(false); ?>

    <?= $form->field($model, 'dateTo')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_INPUT,
        'options' => [
            'class' => 'form-control',
            'placeholder' => $model->getAttributeLabel('dateTo'),
        ],
        'pluginOptions' => [
            'format'         => 'yyyy-mm-dd',
            'autoclose'      => true,
            'todayHighlight' => true,
        ]
    ])->label(false); ?>

    <div class="form-group filter">
        <?= Html::submitButton('Filter', ['class' => 'btn btn-primary']) ?>
    </div>

    <?= $form->field($model, 'keyword',      ['template' => '{input}'])->hiddenInput(); ?>
    <?= $form->field($model, 'countRecords', ['template' => '{input}'])->hiddenInput(); ?>

    <?php ActiveForm::end(); ?>
</div>