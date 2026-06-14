<?php

/** @var yii\web\View $this */
/** @var common\models\User|null $user */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\TblAppointment;
use common\models\TblMedicalRecord;
use common\models\TblPrescription;
use common\models\TblBill;

$this->title = 'MediSync - Patient Portal';

$isGuest = Yii::$app->user->isGuest;
$patientId = !$isGuest ? $user->patient_id : null;

// Get patient stats if logged in
$upcomingAppointments = 0;
$recentRecords = [];
$pendingBills = 0;

if (!$isGuest && $patientId) {
    $upcomingAppointments = TblAppointment::find()
        ->where(['patient_id' => $patientId])
        ->andWhere(['status' => ['scheduled', 'checked_in', 'in_progress']])
        ->andWhere(['>=', 'appointment_date', date('Y-m-d')])
        ->count();
    
    $recentRecords = TblMedicalRecord::find()
        ->joinWith('appointment')
        ->where(['tbl_appointment.patient_id' => $patientId])
        ->orderBy(['record_date' => SORT_DESC])
        ->limit(3)
        ->all();
    
    $pendingBills = TblBill::find()
        ->joinWith('appointment')
        ->where(['tbl_appointment.patient_id' => $patientId])
        ->andWhere(['payment_status' => 'pending'])
        ->count();
}
?>

<div class="site-index">

    <?php if ($isGuest): ?>
    <!-- ========== GUEST VIEW ========== -->
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <?= Html::img('@web/images/medisync-logo.svg', ['alt' => 'MediSync', 'height' => 80]) ?>
                    </div>
                    <h1 class="display-4 fw-bold text-primary">Welcome to MediSync</h1>
                    <p class="lead text-muted">Your Health, Our Priority</p>
                    <p class="mb-4">Book appointments, view medical records, and manage your healthcare - all in one place.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <?= Html::a('<i class="fas fa-user-plus"></i> Register Now', ['site/signup'], ['class' => 'btn btn-primary btn-lg px-4']) ?>
                        <?= Html::a('<i class="fas fa-sign-in-alt"></i> Login', ['site/login'], ['class' => 'btn btn-outline-primary btn-lg px-4']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="row mt-5 text-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h5>Book Appointments</h5>
                    <p class="text-muted">Schedule appointments with your preferred doctors easily.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-notes-medical fa-3x text-success mb-3"></i>
                    <h5>View Medical Records</h5>
                    <p class="text-muted">Access your medical history and prescriptions anytime.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-file-invoice-dollar fa-3x text-info mb-3"></i>
                    <h5>Manage Bills</h5>
                    <p class="text-muted">View and track your hospital bills and payments.</p>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ========== PATIENT DASHBOARD ========== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="mb-1">Welcome, <?= Html::encode($user->getFullName()) ?>!</h2>
                    <p class="mb-0 opacity-75">Patient Portal | <?= date('l, F j, Y') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Upcoming Appointments</h6>
                            <h2 class="mb-0"><?= $upcomingAppointments ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-check fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Bills</h6>
                            <h2 class="mb-0"><?= $pendingBills ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-file-invoice fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Medical Records</h6>
                            <h2 class="mb-0"><?= count($recentRecords) ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-notes-medical fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-2">
                            <?= Html::a('<i class="fas fa-calendar-plus"></i><br>Book Appointment', ['appointment/create'], ['class' => 'btn btn-outline-primary w-100']) ?>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <?= Html::a('<i class="fas fa-calendar-alt"></i><br>My Appointments', ['appointment/index'], ['class' => 'btn btn-outline-info w-100']) ?>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <?= Html::a('<i class="fas fa-notes-medical"></i><br>My Records', ['medical-record/index'], ['class' => 'btn btn-outline-success w-100']) ?>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <?= Html::a('<i class="fas fa-file-invoice"></i><br>My Bills', ['bill/index'], ['class' => 'btn btn-outline-warning w-100']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Medical Records -->
    <?php if (!empty($recentRecords)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Medical Records</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentRecords as $record): ?>
                                <tr>
                                    <td><?= Yii::$app->formatter->asDate($record->record_date, 'medium') ?></td>
                                    <td><?= $record->doctor ? 'Dr. ' . Html::encode($record->doctor->last_name) : 'N/A' ?></td>
                                    <td><?= Html::encode(substr($record->diagnosis ?? 'No diagnosis', 0, 50)) ?>...</td>
                                    <td>
                                        <?= Html::a('<i class="fas fa-eye"></i> View', ['medical-record/view', 'record_id' => $record->record_id], ['class' => 'btn btn-sm btn-primary']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>

</div>