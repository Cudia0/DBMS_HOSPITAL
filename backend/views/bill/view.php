<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var object $model */
/** @var array $billItems */

$this->title = 'Bill #' . $model->bill_id;
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
$canProcess = ($model->payment_status === 'pending' || $model->payment_status === 'partial') && ($isReceptionist || $isDirector);
?>
<div class="tbl-bill-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($isReceptionist || $isDirector): ?>
            <?= Html::a('<i class="fas fa-plus-circle"></i> Add Charge', ['bill-item/create', 'bill_id' => $model->bill_id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-print"></i> Print Receipt', ['print', 'bill_id' => $model->bill_id], ['class' => 'btn btn-secondary', 'target' => '_blank']) ?>
        <?php endif; ?>
        <?php if ($isDirector): ?>
            <?= Html::a('<i class="fas fa-edit"></i> Edit Bill', ['update', 'bill_id' => $model->bill_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'bill_id' => $model->bill_id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Delete?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <!-- Payment Status Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <?php
            $statusColors = ['pending' => 'warning', 'partial' => 'info', 'paid' => 'success', 'refunded' => 'secondary', 'cancelled' => 'danger'];
            $color = $statusColors[$model->payment_status] ?? 'secondary';
            ?>
            <div class="card bg-<?= $color ?> text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= strtoupper($model->payment_status) ?></h3>
                    <?php if ($model->payment_method): ?><small>via <?= ucfirst($model->payment_method) ?></small><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Payment -->
    <?php if ($canProcess): ?>
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-cash-register"></i> Process Payment</h5></div>
        <div class="card-body">
            <div class="alert alert-info"><strong>Amount Due:</strong> <span style="font-size: 24px; font-weight: bold;">₱<?= number_format($model->total_amount, 2) ?></span></div>
            <?php $form = ActiveForm::begin(['action' => ['mark-paid', 'bill_id' => $model->bill_id], 'method' => 'post']); ?>
            <div class="row"><div class="col-md-6"><div class="form-group mb-3">
                <label class="form-label fw-bold">Payment Method *</label>
                <select name="payment_method" class="form-select form-select-lg" required>
                    <option value="">-- Select --</option>
                    <option value="cash">Cash</option><option value="gcash">GCash</option><option value="maya">Maya</option>
                    <option value="credit_card">Credit Card</option><option value="debit_card">Debit Card</option>
                    <option value="bank_transfer">Bank Transfer</option><option value="insurance">Insurance</option>
                </select>
            </div></div></div>
            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check-circle"></i> Mark as Paid</button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bill Items Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list-alt"></i> Bill Charges</h5>
            <?php if ($isReceptionist || $isDirector): ?>
                <?= Html::a('<i class="fas fa-plus-circle"></i> Add Charge', ['bill-item/create', 'bill_id' => $model->bill_id], ['class' => 'btn btn-light btn-sm']) ?>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (!empty($billItems)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr><th>#</th><th>Type</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th>
                        <?php if ($isReceptionist || $isDirector): ?><th>Actions</th><?php endif; ?></tr>
                    </thead>
                    <tbody>
                        <?php $totalItems = 0; ?>
                        <?php foreach ($billItems as $index => $item): ?>
                        <?php $totalItems += $item['total_price']; ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <?php
                                $typeLabels = [
                                    'consultation' => '<span class="badge bg-primary">Consultation</span>',
                                    'medicine' => '<span class="badge bg-success">Medicine</span>',
                                    'lab_test' => '<span class="badge bg-warning text-dark">Lab Test</span>',
                                    'procedure' => '<span class="badge bg-info">Procedure</span>',
                                    'other' => '<span class="badge bg-secondary">Other</span>',
                                ];
                                echo $typeLabels[$item['item_type']] ?? $item['item_type'];
                                ?>
                            </td>
                            <td><?= Html::encode($item['description']) ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-end">₱<?= number_format($item['unit_price'], 2) ?></td>
                            <td class="text-end"><strong>₱<?= number_format($item['total_price'], 2) ?></strong></td>
                            <?php if ($isReceptionist || $isDirector): ?>
                            <td>
                                <?= Html::a('<i class="fas fa-edit"></i>', ['bill-item/update', 'bill_item_id' => $item['bill_item_id']], ['class' => 'btn btn-sm btn-primary', 'title' => 'Edit']) ?>
                                <?php if ($isDirector): ?>
                                    <?= Html::a('<i class="fas fa-trash"></i>', ['bill-item/delete', 'bill_item_id' => $item['bill_item_id']], [
                                        'class' => 'btn btn-sm btn-danger', 'title' => 'Delete',
                                        'data' => ['confirm' => 'Delete?', 'method' => 'post'],
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr><td colspan="5" class="text-end"><strong>Total:</strong></td><td class="text-end"><strong>₱<?= number_format($totalItems, 2) ?></strong></td>
                        <?php if ($isReceptionist || $isDirector): ?><td></td><?php endif; ?></tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted text-center mb-0">No charges yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bill Summary -->
    <div class="card">
        <div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="fas fa-info-circle"></i> Bill Summary</h5></div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'bill_id',
                    'appt_id',
                    ['attribute' => 'total_amount', 'format' => 'raw', 'value' => '<span style="font-size: 22px; font-weight: bold;">₱' . number_format($model->total_amount, 2) . '</span>'],
                    ['attribute' => 'payment_status', 'format' => 'raw', 'value' => function($m) {
                        $labels = ['pending' => '<span class="badge bg-warning">Pending</span>', 'paid' => '<span class="badge bg-success">Paid</span>'];
                        return $labels[$m->payment_status] ?? $m->payment_status;
                    }],
                    'payment_method',
                    'bill_date:datetime',
                ],
            ]) ?>
        </div>
    </div>

</div>