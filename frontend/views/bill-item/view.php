<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblBillItem $model */

$this->title = $model->bill_item_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bill Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-bill-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'bill_item_id' => $model->bill_item_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'bill_item_id' => $model->bill_item_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bill_item_id',
            'bill_id',
            'item_type',
            'description',
            'reference_id',
            'quantity',
            'unit_price',
            'total_price',
            'created_at',
        ],
    ]) ?>

</div>
