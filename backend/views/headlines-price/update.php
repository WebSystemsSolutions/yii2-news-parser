<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\HeadlinePrice */

$this->title = 'Update the price';

$this->params['breadcrumbs'][] = ['label' => 'Price', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="headline-price-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
