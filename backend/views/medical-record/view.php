<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblMedicalRecord $model */

$this->title = 'Medical Record #' . $model->record_id;
$this->params['breadcrumbs'][] = ['label' => 'Medical Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canEdit = $user && ($user->isDirector() || $user->isDoctor());
?>
<div class="tbl-medical-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canEdit): ?>
            <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'record_id' => $model->record_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if ($user && $user->isDirector()): ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'record_id' => $model->record_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this medical record?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <!-- Appointment Reference -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Record Information</h5>
        </div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr>
                    <th width="150">Record ID:</th>
                    <td>#<?= $model->record_id ?></td>
                </tr>
                <tr>
                    <th>Appointment ID:</th>
                    <td>#<?= $model->appt_id ?></td>
                </tr>
                <tr>
                    <th>Record Date:</th>
                    <td><?= Yii::$app->formatter->asDatetime($model->record_date, 'medium') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Medical Details -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Consultation Details</h5>
        </div>
        <div class="card-body">

            <div class="mb-3">
                <h6 class="text-muted"><i class="fas fa-heartbeat"></i> Vital Signs</h6>
                <?php if ($model->vital_signs): ?>
                    <div class="p-3  rounded">
                        <strong><?= Html::encode($model->vital_signs) ?></strong>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Not recorded</p>
                <?php endif; ?>
            </div>

            <hr>

            <div class="mb-3">
                <h6 class="text-muted"><i class="fas fa-diagnoses"></i> Diagnosis</h6>
                <?php if ($model->diagnosis): ?>
                    <div class="p-3  rounded">
                        <?= nl2br(Html::encode($model->diagnosis)) ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Not provided</p>
                <?php endif; ?>
            </div>

            <hr>

            <div class="mb-3">
                <h6 class="text-muted"><i class="fas fa-prescription"></i> Treatment Plan</h6>
                <?php if ($model->treatment_plan): ?>
                    <div class="p-3  rounded">
                        <?= nl2br(Html::encode($model->treatment_plan)) ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Not provided</p>
                <?php endif; ?>
            </div>

            <hr>

            <div class="mb-3">
                <h6 class="text-muted"><i class="fas fa-sticky-note"></i> Additional Notes</h6>
                <?php if ($model->notes): ?>
                    <div class="p-3  rounded">
                        <?= nl2br(Html::encode($model->notes)) ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No additional notes</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>