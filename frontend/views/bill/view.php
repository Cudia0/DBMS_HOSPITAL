<?php

use yii\helpers\Html;
use common\models\TblBillItem;

/** @var yii\web\View $this */
/** @var common\models\TblBill $model */

$this->title = 'Bill #' . $model->bill_id;
$this->params['breadcrumbs'][] = ['label' => 'My Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$billItems = TblBillItem::find()->where(['bill_id' => $model->bill_id])->orderBy(['item_type' => SORT_ASC])->all();
?>
<div class="tbl-bill-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mb-4">
        <div class="col-12">
            <?php
            $statusColors = ['pending' => 'warning', 'paid' => 'success'];
            $color = $statusColors[$model->payment_status] ?? 'secondary';
            ?>
            <div class="card bg-<?= $color ?> text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= strtoupper($model->payment_status) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-list-alt"></i> Charges</h5></div>
        <div class="card-body">
            <?php if (!empty($billItems)): ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>#</th><th>Type</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <?php $totalItems = 0; ?>
                    <?php foreach ($billItems as $index => $item): ?>
                    <?php $totalItems += $item->total_price; ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= ucfirst($item->item_type) ?></td>
                        <td><?= Html::encode($item->description) ?></td>
                        <td class="text-center"><?= $item->quantity ?></td>
                        <td class="text-end">₱<?= number_format($item->unit_price, 2) ?></td>
                        <td class="text-end"><strong>₱<?= number_format($item->total_price, 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr><td colspan="5" class="text-end"><strong>Grand Total:</strong></td><td class="text-end"><strong>₱<?= number_format($totalItems, 2) ?></strong></td></tr>
                </tfoot>
            </table>
            <?php else: ?>
            <p class="text-muted text-center">No charges recorded.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="fas fa-info-circle"></i> Bill Summary</h5></div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th width="150">Bill ID:</th><td>#<?= $model->bill_id ?></td></tr>
                <tr><th>Appointment:</th><td>#<?= $model->appt_id ?></td></tr>
                <tr><th>Total Amount:</th><td><strong>₱<?= number_format($model->total_amount, 2) ?></strong></td></tr>
                <tr><th>Status:</th><td><?= ucfirst($model->payment_status) ?></td></tr>
                <tr><th>Date:</th><td><?= Yii::$app->formatter->asDatetime($model->bill_date, 'medium') ?></td></tr>
            </table>
        </div>
    </div>

</div>