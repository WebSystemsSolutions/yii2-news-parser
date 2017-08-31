<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\config\KeywordConfig;

/* @var $this yii\web\View */
/* @var $model \common\models\HeadlinePrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="headline-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'keyword')->dropDownList(array_merge(['' => ''], array_combine($values = KeywordConfig::getConstantsValue(), $values)), ['maxlength' => true]); ?>

    <?= $form->field($model, 'price')->textInput(); ?>

    <?= $form->field($model, 'date')->widget(DatePicker::className(), [
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => [
            'autoclose' => true,
            'todayHighlight' => true,
            'format' => 'yyyy-mm-dd',
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
