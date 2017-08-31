<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model \common\models\HeadlinePrice */

$this->title = 'Create a price';

$this->params['breadcrumbs'][] = ['label' => 'Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="headline-price-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
