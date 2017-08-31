<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use common\models\config\KeywordConfig;

/* @var $this yii\web\View */
/* @var $model \common\models\Headline */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="headline-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(!$model->isNewRecord ? ['readonly' => true, 'disabled' => true]: ['maxlength' => true]); ?>

    <?= $form->field($model, 'keyword')->dropDownList(array_merge(['' => ''],  array_combine($values = KeywordConfig::getConstantsValue(), $values)),!$model->isNewRecord ? ['readonly' => true, 'disabled' => true]: ['maxlength' => true]); ?>

    <?= $form->field($model, 'language')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'created')->widget(DateTimePicker::className(), [
        'type' => DateTimePicker::TYPE_INPUT,
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd hh:ii:ss'
        ]
    ]); ?>

    <?= $form->field($model, 'content')->widget(Redactor::className(), [
        'clientOptions' => [
            'plugins'           => ['video', 'table', 'fullscreen', 'fontsize', 'fontfamily', 'fontcolor'],
            'buttonsHide'       => ['file'],
            'replaceDivs'       => false,
            'removeWithoutAttr' => false,
            'maxHeight'         => 300,
        ]
    ]); ?>

    <?= $form->field($model, 'value_1')->textInput(); ?>

    <?= $form->field($model, 'value_7')->textInput(); ?>

    <?= $form->field($model, 'value_14')->textInput(); ?>

    <?= $form->field($model, 'value_28')->textInput(); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
