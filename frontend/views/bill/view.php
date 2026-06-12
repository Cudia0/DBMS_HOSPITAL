<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblBill $model */

$this->title = $model->bill_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-bill-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'bill_id' => $model->bill_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'bill_id' => $model->bill_id], [
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
            'bill_id',
            'appt_id',
            'payment_status',
            'qty',
            'dr_fee',
            'totalm_price',
            'total_amount',
        ],
    ]) ?>

</div>
