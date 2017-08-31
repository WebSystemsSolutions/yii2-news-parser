<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model \backend\models\forms\ExportForm */

$this->title = 'Export';

?>

<div class="export">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?php if (!$model->filterByCountRecords) { ?>

        <?= $form->field($model, 'type')->widget(Select2::className(), [
            'data' => $model->types,
            'options' => [
                'placeholder' => 'Select',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ]
        ]); ?>

        <?= Html::hiddenInput('', $model->keyword, ['id' => $dependency_id = Html::getInputId($model, 'keyword') . '-def']); ?>

        <?= $form->field($model, 'keyword')->widget(DepDrop::className(), [
            'type' => DepDrop::TYPE_SELECT2,
            'select2Options' => [
                'pluginOptions' => [
                    'allowClear' => true
                ]
            ],
            'pluginOptions' => [
                'initialize'  => $model->type ? true : false,
                'placeholder' => 'Select',
                'depends'     => [Html::getInputId($model, 'type')],
                'url'         => Url::toRoute(['ajax/build-keywords', 'headlines' => $model->isHeadlines]),
                'params'      => [$dependency_id],
            ],
            'pluginEvents' => $model->keyword ? [] : [
                'depdrop:afterChange' => 'function(event) { $(event.target).val(""); }',
            ]
        ]); ?>

    <?php } ?>

    <?php if($model->filterByCountRecords) {
        echo $form->field($model, 'countRecords')->textInput();
    } ?>

    <?= $form->field($model, 'from', ['template' => "{label}<div class='wrapper'>\n{input}\n{hint}\n{error}</div>"])->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_INPUT,
        'options' => [
            'class' => 'form-control',
        ],
        'pluginOptions' => [
            'format'         => 'yyyy-mm-dd',
            'autoclose'      => true,
            'todayHighlight' => true,
        ]
    ]); ?>

    <?= $form->field($model, 'to', ['template' => "{label}<div class='wrapper'>\n{input}\n{hint}\n{error}</div>"])->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_INPUT,
        'options' => [
            'class' => 'form-control'
        ],
        'pluginOptions' => [
            'format'         => 'yyyy-mm-dd',
            'autoclose'      => true,
            'todayHighlight' => true,
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Export', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>