<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = 'Bill Item #' . $model->bill_item_id;
$this->params['breadcrumbs'][] = ['label' => 'Bill Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bill-item-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Edit', ['update', 'bill_item_id' => $model->bill_item_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Bill', ['bill/view', 'bill_id' => $model->bill_id], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bill_item_id',
            'bill_id',
            [
                'attribute' => 'item_type',
                'value' => ucfirst($model->item_type),
            ],
            'description',
            'reference_id',
            'quantity',
            [
                'attribute' => 'unit_price',
                'value' => '₱' . number_format($model->unit_price, 2),
            ],
            [
                'attribute' => 'total_price',
                'value' => '₱' . number_format($model->total_price, 2),
            ],
            'created_at:datetime',
        ],
    ]) ?>

</div>