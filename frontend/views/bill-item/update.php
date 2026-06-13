<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBillItem $model */

$this->title = 'Update Tbl Bill Item: ' . $model->bill_item_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bill Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bill_item_id, 'url' => ['view', 'bill_item_id' => $model->bill_item_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bill-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
