<?php

use yii\helpers\Html;
use common\repositories\MedlineRepository;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = 'Prescription #' . $model->prescription_id;
$this->params['breadcrumbs'][] = ['label' => 'Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canEdit = $user && ($user->isDirector() || $user->isDoctor());

// Get medlines for this prescription
$medlineRepo = new MedlineRepository();
$medlines = $medlineRepo->findByPrescription($model->prescription_id);

// Build doctor name from available fields
$doctorName = 'N/A';
if (!empty($model->doctor_fname) || !empty($model->doctor_lname)) {
    $doctorName = 'Dr. ' . ($model->doctor_fname ?? '') . ' ' . ($model->doctor_lname ?? '');
}

// Build patient name from available fields
$patientName = 'N/A';
if (!empty($model->patient_fname) || !empty($model->patient_lname)) {
    $patientName = ($model->patient_lname ?? '') . ', ' . ($model->patient_fname ?? '');
}
?>
<div class="tbl-prescription-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canEdit): ?>
            <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'prescription_id' => $model->prescription_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if ($user && $user->isDirector()): ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'prescription_id' => $model->prescription_id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Delete this prescription?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <!-- Prescription Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary h-100">
                <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-info-circle"></i> Prescription Details</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="120">Prescription ID:</th><td>#<?= $model->prescription_id ?></td></tr>
                        <tr><th>Appointment:</th><td>#<?= $model->appt_id ?></td></tr>
                        <tr><th>Date:</th><td><?= Yii::$app->formatter->asDatetime($model->prescription_date, 'medium') ?></td></tr>
                        <tr><th>Duration:</th><td><?= $model->duration_days ? $model->duration_days . ' days' : 'N/A' ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-user"></i> Patient & Doctor</h5></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th width="80">Patient:</th><td><strong><?= Html::encode($patientName) ?></strong></td></tr>
                        <tr><th>Doctor:</th><td><strong><?= Html::encode($doctorName) ?></strong></td></tr>
                        <?php if (!empty($model->appointment_date)): ?>
                        <tr><th>Appt Date:</th><td><?= Yii::$app->formatter->asDate($model->appointment_date, 'medium') ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dosage Instructions -->
    <?php if (!empty($model->dosage_instructions)): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fas fa-pills"></i> Dosage Instructions</h5></div>
        <div class="card-body">
            <div class="p-3  rounded"><?= nl2br(Html::encode($model->dosage_instructions)) ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Medicines -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-capsules"></i> Medicines Prescribed</h5></div>
        <div class="card-body">
            <?php if (!empty($medlines)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Medicine</th>
                            <th>Strength</th>
                            <th>Qty</th>
                            <th>Dosage per Intake</th>
                            <th>Frequency</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalMedCost = 0; ?>
                        <?php foreach ($medlines as $index => $medline): ?>
                        <?php 
                            $itemTotal = ($medline['med_price'] ?? 0) * ($medline['qty'] ?? 0);
                            $totalMedCost += $itemTotal;
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= Html::encode($medline['med_name'] ?? 'N/A') ?></td>
                            <td><?= Html::encode($medline['strength'] ?? '-') ?></td>
                            <td class="text-center"><?= $medline['qty'] ?></td>
                            <td><?= Html::encode($medline['dosage_per_intake'] ?? '-') ?></td>
                            <td><?= Html::encode($medline['frequency'] ?? '-') ?></td>
                            <td class="text-end">₱<?= number_format($medline['med_price'] ?? 0, 2) ?></td>
                            <td class="text-end"><strong>₱<?= number_format($itemTotal, 2) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="7" class="text-end"><strong>Total Medicine Cost:</strong></td>
                            <td class="text-end"><strong>₱<?= number_format($totalMedCost, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted text-center mb-0">No medicines prescribed.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notes -->
    <?php if (!empty($model->notes)): ?>
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white"><h5 class="mb-0"><i class="fas fa-sticky-note"></i> Additional Notes</h5></div>
        <div class="card-body">
            <div class="p-3  rounded"><?= nl2br(Html::encode($model->notes)) ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timestamps -->
    <div class="row">
        <div class="col-md-6">
            <p class="text-muted small">Created: <?= Yii::$app->formatter->asDatetime($model->created_at ?? 'N/A', 'medium') ?></p>
        </div>
        <div class="col-md-6 text-end">
            <p class="text-muted small">Updated: <?= Yii::$app->formatter->asDatetime($model->updated_at ?? 'N/A', 'medium') ?></p>
        </div>
    </div>

</div>