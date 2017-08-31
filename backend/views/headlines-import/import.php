<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model \backend\models\forms\HeadlinesImportForm */

$this->title = 'Import articles';

?>

<div class="import-headlines">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'file')->widget(FileInput::className(), [
        'options' => ['accept' => 'text/csv'],
        'pluginOptions' => [
            'showPreview' => false,
            'showCaption' => true,
            'showRemove'  => false,
            'showUpload'  => false,
        ]
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Import', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end() ?>

</div>