<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblLabTest $model */

$this->title = 'Lab Test #' . $model->test_id;
$this->params['breadcrumbs'][] = ['label' => 'Lab Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canEdit = $user && ($user->isDirector() || $user->isDoctor());
?>
<div class="tbl-lab-test-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canEdit): ?>
            <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'test_id' => $model->test_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if ($user && $user->isDirector()): ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'test_id' => $model->test_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this lab test?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <!-- Status Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <?php
            $statusColors = [
                'ordered' => 'info',
                'collected' => 'primary',
                'processing' => 'warning',
                'completed' => 'success',
                'cancelled' => 'danger',
            ];
            $color = $statusColors[$model->status] ?? 'secondary';
            ?>
            <div class="card bg-<?= $color ?> text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= strtoupper($model->status) ?></h3>
                    <?php if ($model->is_abnormal): ?>
                        <span class="badge bg-danger mt-2">⚠️ Abnormal Results</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Details -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-flask"></i> Test Details</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th width="150">Test ID</th><td>#<?= $model->test_id ?></td></tr>
                <tr><th>Appointment</th><td>#<?= $model->appt_id ?></td></tr>
                <tr><th>Test Name</th><td><strong><?= Html::encode($model->test_name) ?></strong></td></tr>
                <tr><th>Category</th><td><?= Html::encode(ucfirst($model->test_category ?? 'N/A')) ?></td></tr>
                <tr><th>Status</th><td><?= ucfirst($model->status) ?></td></tr>
                <tr><th>Ordered Date</th><td><?= Yii::$app->formatter->asDatetime($model->ordered_date, 'medium') ?></td></tr>
                <?php if ($model->results_date): ?>
                <tr><th>Results Date</th><td><?= Yii::$app->formatter->asDatetime($model->results_date, 'medium') ?></td></tr>
                <?php endif; ?>
            </table>

            <?php if ($model->results): ?>
            <hr>
            <h6><i class="fas fa-clipboard-check"></i> Results</h6>
            <div class="p-3  rounded">
                <?= nl2br(Html::encode($model->results)) ?>
            </div>
            <?php endif; ?>

            <?php if ($model->notes): ?>
            <hr>
            <h6><i class="fas fa-sticky-note"></i> Notes</h6>
            <div class="p-3  rounded">
                <?= nl2br(Html::encode($model->notes)) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>