<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model \common\models\HeadlineCompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="headline-company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'keyword')->textInput(); ?>

    <?= $form->field($model, 'active')->widget(SwitchInput::className(), [
            'type' => SwitchInput::CHECKBOX,
            'pluginOptions' => [
                'size' => 'mini',
            ],
        ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
