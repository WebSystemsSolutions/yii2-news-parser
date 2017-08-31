<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\helpers\KeywordHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model \backend\models\forms\ParserForm */

$this->title = 'Parse prices';

?>
<div class="parse-headlines">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'keyword')->widget(Select2::className(), [
            'data' => KeywordHelper::getDropDownKeywords(),
            'options' => ['placeholder' => 'Select'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>

        <div class="form-group">
            <?= Html::submitButton('Parse', ['class' => 'btn btn-success']); ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
