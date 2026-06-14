<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TblAppointment $model */

$this->title = 'Appointment #' . $model->appt_id;
$this->params['breadcrumbs'][] = ['label' => 'Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
$isDoctor = $user && $user->isDoctor();
$isPending = $model->status === null || $model->status === '';
$canReject = ($isPending || $model->status === 'scheduled') && ($isReceptionist || $isDirector);
?>
<div class="tbl-appointment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($isDirector || $isReceptionist): ?>
            <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'appt_id' => $model->appt_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        
        <?php if ($isDirector): ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'appt_id' => $model->appt_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this appointment?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <!-- PENDING: Show Accept & Reject -->
    <?php if ($isPending && ($isReceptionist || $isDirector)): ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Accept & Schedule</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['action' => ['accept', 'appt_id' => $model->appt_id], 'method' => 'post']); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Appointment Date *</label>
                        <input type="date" name="TblAppointment[appointment_date]" class="form-control" 
                               min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Appointment Time *</label>
                        <input type="time" name="TblAppointment[appointment_time]" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check-circle"></i> Accept & Schedule
                    </button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times-circle"></i> Reject / Cancel</h5>
                </div>
                <div class="card-body">
                    <?php $rejectForm = ActiveForm::begin(['action' => ['reject', 'appt_id' => $model->appt_id], 'method' => 'post']); ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Rejection</label>
                        <textarea name="reject_reason" class="form-control" rows="3" 
                                  placeholder="e.g., Doctor not available, Patient requested cancellation, etc."></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100" 
                            data-confirm="Are you sure you want to cancel this appointment request?">
                        <i class="fas fa-times-circle"></i> Reject Appointment
                    </button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- SCHEDULED: Show Cancel option -->
    <?php if ($canReject && !$isPending): ?>
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> Cancel Appointment</h5>
        </div>
        <div class="card-body">
            <?php $rejectForm = ActiveForm::begin(['action' => ['reject', 'appt_id' => $model->appt_id], 'method' => 'post']); ?>
            <div class="mb-3">
                <label class="form-label fw-bold">Reason for Cancellation</label>
                <textarea name="reject_reason" class="form-control" rows="2" 
                          placeholder="e.g., Patient no-show, Rescheduled, etc."></textarea>
            </div>
            <button type="submit" class="btn btn-danger" 
                    data-confirm="Are you sure you want to cancel this scheduled appointment?">
                <i class="fas fa-times-circle"></i> Cancel This Appointment
            </button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- SCHEDULED: Show Check-in Button -->
    <?php if ($model->status === 'scheduled' && ($isReceptionist || $isDirector)): ?>
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-sign-in-alt"></i> Patient Check-in</h5>
        </div>
        <div class="card-body">
            <p>The patient has arrived for their scheduled appointment.</p>
            <?= Html::a('<i class="fas fa-sign-in-alt"></i> Check In Patient', ['check-in', 'appt_id' => $model->appt_id], [
                'class' => 'btn btn-info btn-lg',
                'data' => [
                    'confirm' => 'Confirm patient check-in? This will change the status to "Checked In".',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- CHECKED_IN: Show info for doctor -->
    <?php if ($model->status === 'checked_in' && ($isDoctor || $isDirector)): ?>
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-stethoscope"></i> Patient Ready for Consultation</h5>
        </div>
        <div class="card-body">
            <p>Patient is checked in and waiting. You can now create a medical record.</p>
            <?= Html::a('<i class="fas fa-notes-medical"></i> Create Medical Record', ['/medical-record/create', 'appt_id' => $model->appt_id], ['class' => 'btn btn-primary btn-lg']) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Appointment Details -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Appointment Details</h5>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'appt_id',
                    [
                        'attribute' => 'patient_id',
                        'label' => 'Patient',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->patient) {
                                $html = '<strong>' . Html::encode($model->patient->getFullName()) . '</strong>';
                                $html .= '<br><small class="text-muted">ID: ' . $model->patient_id . '</small>';
                                if ($model->patient->sex) {
                                    $html .= '<br><small class="text-muted">Sex: ' . $model->patient->sex . '</small>';
                                }
                                if ($model->patient->date_of_birth) {
                                    $html .= '<br><small class="text-muted">Age: ' . $model->patient->getAgeDisplay() . '</small>';
                                }
                                return $html;
                            }
                            return '<span class="text-muted">N/A</span>';
                        },
                    ],
                    [
                        'attribute' => 'dr_id',
                        'label' => 'Doctor',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->doctor) {
                                $html = '<strong>Dr. ' . Html::encode($model->doctor->first_name . ' ' . $model->doctor->last_name) . '</strong>';
                                if ($model->doctor->specialization) {
                                    $html .= '<br><small class="text-muted">' . $model->doctor->specialization . '</small>';
                                }
                                return $html;
                            }
                            return '<span class="text-muted">N/A</span>';
                        },
                    ],
                    [
                        'attribute' => 'recep_id',
                        'label' => 'Receptionist',
                        'value' => $model->receptionist ? $model->receptionist->first_name . ' ' . $model->receptionist->last_name : '<span class="text-warning">Not yet assigned</span>',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'appointment_date',
                        'format' => 'raw',
                        'value' => $model->appointment_date 
                            ? '<strong>' . Yii::$app->formatter->asDate($model->appointment_date, 'long') . '</strong>' 
                            : '<span class="text-warning"><i class="fas fa-clock"></i> Not yet scheduled</span>',
                    ],
                    [
                        'attribute' => 'appointment_time',
                        'format' => 'raw',
                        'value' => $model->appointment_time 
                            ? '<strong>' . Yii::$app->formatter->asTime($model->appointment_time, 'short') . '</strong>' 
                            : '<span class="text-warning"><i class="fas fa-clock"></i> Not yet scheduled</span>',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => $model->getStatusLabel(),
                    ],
                    [
                        'attribute' => 'symptoms_list',
                        'format' => 'ntext',
                        'value' => $model->symptoms_list ?: '<span class="text-muted">No symptoms provided</span>',
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <?php if ($model->status !== null && $model->status !== '' && $model->status !== 'cancelled'): ?>
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php if ($isDoctor || $isDirector): ?>
                <div class="col-md-3 mb-2">
                    <?= Html::a('<i class="fas fa-notes-medical"></i> Medical Record', ['/medical-record/create', 'appt_id' => $model->appt_id], ['class' => 'btn btn-outline-primary w-100']) ?>
                </div>
                <div class="col-md-3 mb-2">
                    <?= Html::a('<i class="fas fa-prescription"></i> Prescription', ['/prescription/create', 'appt_id' => $model->appt_id], ['class' => 'btn btn-outline-success w-100']) ?>
                </div>
                <div class="col-md-3 mb-2">
                    <?= Html::a('<i class="fas fa-flask"></i> Lab Test', ['/lab-test/create', 'appt_id' => $model->appt_id], ['class' => 'btn btn-outline-warning w-100']) ?>
                </div>
                <?php endif; ?>
                <?php if ($isReceptionist || $isDirector): ?>
                <div class="col-md-3 mb-2">
                    <?= Html::a('<i class="fas fa-file-invoice"></i> Generate Bill', ['/bill/create', 'appt_id' => $model->appt_id], ['class' => 'btn btn-outline-info w-100']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>