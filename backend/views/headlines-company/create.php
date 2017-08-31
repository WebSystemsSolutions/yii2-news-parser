<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model \common\models\HeadlineCompany */

$this->title = 'Create a company';

$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="headline-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
