<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = 'Appointment #' . $model->appt_id;
$this->params['breadcrumbs'][] = ['label' => 'Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
$isDoctor = $user && $user->isDoctor();
$isPending = empty($model->status);
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
                'data' => ['confirm' => 'Delete?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <!-- PENDING: Accept & Reject -->
    <?php if ($isPending && ($isReceptionist || $isDirector)): ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-check-circle"></i> Accept & Schedule</h5></div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['action' => ['accept', 'appt_id' => $model->appt_id], 'method' => 'post']); ?>
                    <div class="mb-3"><label class="form-label fw-bold">Appointment Date *</label><input type="date" name="TblAppointment[appointment_date]" class="form-control" min="<?= date('Y-m-d') ?>" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">Appointment Time *</label><input type="time" name="TblAppointment[appointment_time]" class="form-control" required></div>
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-check-circle"></i> Accept & Schedule</button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-danger h-100">
                <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-times-circle"></i> Reject / Cancel</h5></div>
                <div class="card-body">
                    <?php $rejectForm = ActiveForm::begin(['action' => ['reject', 'appt_id' => $model->appt_id], 'method' => 'post']); ?>
                    <div class="mb-3"><label class="form-label fw-bold">Reason</label><textarea name="reject_reason" class="form-control" rows="3" placeholder="Reason for rejection..."></textarea></div>
                    <button type="submit" class="btn btn-danger w-100" data-confirm="Cancel this appointment?"><i class="fas fa-times-circle"></i> Reject</button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- SCHEDULED: Cancel & Check-in -->
    <?php if ($canReject && !$isPending): ?>
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fas fa-times-circle"></i> Cancel Appointment</h5></div>
        <div class="card-body">
            <?php $rejectForm = ActiveForm::begin(['action' => ['reject', 'appt_id' => $model->appt_id], 'method' => 'post']); ?>
            <div class="mb-3"><label class="form-label fw-bold">Reason</label><textarea name="reject_reason" class="form-control" rows="2" placeholder="Reason..."></textarea></div>
            <button type="submit" class="btn btn-danger" data-confirm="Cancel?"><i class="fas fa-times-circle"></i> Cancel</button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($model->status === 'scheduled' && ($isReceptionist || $isDirector)): ?>
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fas fa-sign-in-alt"></i> Patient Check-in</h5></div>
        <div class="card-body">
            <?= Html::a('<i class="fas fa-sign-in-alt"></i> Check In Patient', ['check-in', 'appt_id' => $model->appt_id], [
                'class' => 'btn btn-info btn-lg',
                'data' => ['confirm' => 'Check in patient?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($model->status === 'checked_in' && ($isDoctor || $isDirector)): ?>
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-stethoscope"></i> Patient Ready</h5></div>
        <div class="card-body">
            <?= Html::a('<i class="fas fa-notes-medical"></i> Create Medical Record', ['/medical-record/create', 'appt_id' => $model->appt_id], ['class' => 'btn btn-primary btn-lg']) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Appointment Details -->
    <div class="card">
        <div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="fas fa-info-circle"></i> Appointment Details</h5></div>
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
                            $data = (array) $model;
                            $html = '<strong>' . Html::encode(($data['patient_lname'] ?? '') . ', ' . ($data['patient_fname'] ?? '')) . '</strong>';
                            $html .= '<br><small class="text-muted">ID: ' . $model->patient_id . '</small>';
                            return $html;
                        },
                    ],
                    [
                        'attribute' => 'dr_id',
                        'label' => 'Doctor',
                        'format' => 'raw',
                        'value' => function($model) {
                            $data = (array) $model;
                            $html = '<strong>Dr. ' . Html::encode(($data['doctor_fname'] ?? '') . ' ' . ($data['doctor_lname'] ?? '')) . '</strong>';
                            return $html;
                        },
                    ],
                    [
                        'attribute' => 'recep_id',
                        'label' => 'Receptionist',
                        'value' => $model->recep_id ? 'ID: ' . $model->recep_id : '<span class="text-warning">Not yet assigned</span>',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'appointment_date',
                        'format' => 'raw',
                        'value' => $model->appointment_date ? '<strong>' . Yii::$app->formatter->asDate($model->appointment_date, 'long') . '</strong>' : '<span class="text-warning">Not yet scheduled</span>',
                    ],
                    [
                        'attribute' => 'appointment_time',
                        'format' => 'raw',
                        'value' => $model->appointment_time ? '<strong>' . Yii::$app->formatter->asTime($model->appointment_time, 'short') . '</strong>' : '<span class="text-warning">Not yet scheduled</span>',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function($model) {
                            if (empty($model->status)) return '<span class="badge bg-secondary">Pending Acceptance</span>';
                            $labels = [
                                'scheduled' => '<span class="badge bg-warning">Scheduled</span>',
                                'checked_in' => '<span class="badge bg-info">Checked In</span>',
                                'in_progress' => '<span class="badge bg-primary">In Progress</span>',
                                'completed' => '<span class="badge bg-success">Completed</span>',
                                'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                            ];
                            return $labels[$model->status] ?? $model->status;
                        },
                    ],
                    'symptoms_list:ntext',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

</div>