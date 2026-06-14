<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var array $stats */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

$role = $user->role;
$roleLabel = $user->getRoleLabel();
$fullName = $user->getFullName();
?>
<div class="site-index">

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1"><i class="fas fa-hand-sparkles"></i> Welcome, <?= Html::encode($fullName) ?>!</h2>
                            <p class="mb-0 opacity-75">Role: <strong><?= $roleLabel ?></strong> | <?= date('l, F j, Y') ?></p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge bg-light text-primary fs-5 px-3 py-2"><?= strtoupper($role) ?> PORTAL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($role === 'director'): ?>
    <!-- ============================================ -->
    <!-- DIRECTOR DASHBOARD -->
    <!-- ============================================ -->

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Patients</h6>
                            <h2 class="mb-0"><?= number_format($stats['totalPatients']) ?></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                    <a href="<?= Url::to(['/patient/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Doctors</h6>
                            <h2 class="mb-0"><?= number_format($stats['totalDoctors']) ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-md fa-2x text-success"></i>
                        </div>
                    </div>
                    <a href="<?= Url::to(['/doctor/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Receptionists</h6>
                            <h2 class="mb-0"><?= number_format($stats['totalReceptionists']) ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user fa-2x text-warning"></i>
                        </div>
                    </div>
                    <a href="<?= Url::to(['/receptionist/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Departments</h6>
                            <h2 class="mb-0"><?= number_format($stats['totalDepartments']) ?></h2>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-building fa-2x text-info"></i>
                        </div>
                    </div>
                    <a href="<?= Url::to(['/department/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['pendingAppointments']) ?></h3>
                    <small class="text-muted">Pending Appointments</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['scheduledAppointments']) ?></h3>
                    <small class="text-muted">Scheduled</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-info bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-2x text-info mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['todayAppointments']) ?></h3>
                    <small class="text-muted">Today's Appointments</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-success bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['completedAppointments']) ?></h3>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50">TOTAL REVENUE</h6>
                    <h2 class="mb-0">₱<?= number_format($stats['totalRevenue'], 2) ?></h2>
                    <small class="text-white-50">All time</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50">THIS MONTH</h6>
                    <h2 class="mb-0">₱<?= number_format($stats['monthlyRevenue'], 2) ?></h2>
                    <small class="text-white-50"><?= date('F Y') ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50">PENDING PAYMENTS</h6>
                    <h2 class="mb-0"><?= number_format($stats['pendingPayments']) ?></h2>
                    <small class="text-white-50">Bills awaiting payment</small>
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
                        <div class="col-md-2 col-6 mb-2">
                            <a href="<?= Url::to(['/patient/create']) ?>" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus"></i><br>Add Patient
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="<?= Url::to(['/doctor/create']) ?>" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-md"></i><br>Add Doctor
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="<?= Url::to(['/receptionist/create']) ?>" class="btn btn-outline-warning w-100">
                                <i class="fas fa-user"></i><br>Add Receptionist
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="<?= Url::to(['/director/create']) ?>" class="btn btn-outline-light w-100">
                                <i class="fas fa-user-tie"></i><br>Add Director
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="<?= Url::to(['/medicine/create']) ?>" class="btn btn-outline-info w-100">
                                <i class="fas fa-pills"></i><br>Add Medicine
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-2">
                            <a href="<?= Url::to(['/appointment/create']) ?>" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-calendar-plus"></i><br>Create Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Today's Appointments (<?= date('F j, Y') ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['todayAppointmentList'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['todayAppointmentList'] as $appt): ?>
                                <tr>
                                    <td>#<?= $appt->appt_id ?></td>
                                    <td><?= $appt->appointment_time ? Yii::$app->formatter->asTime($appt->appointment_time, 'short') : '<span class="text-warning">Pending</span>' ?></td>
                                    <td><?= $appt->patient ? Html::encode($appt->patient->getFullName()) : 'N/A' ?></td>
                                    <td><?= $appt->doctor ? 'Dr. ' . Html::encode($appt->doctor->last_name) : 'N/A' ?></td>
                                    <td><?= $appt->getStatusLabel() ?></td>
                                    <td>
                                        <a href="<?= Url::to(['/appointment/view', 'appt_id' => $appt->appt_id]) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0">No appointments scheduled for today.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Recently Registered Patients</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($stats['recentPatients'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recentPatients'] as $patient): ?>
                                <tr>
                                    <td>#<?= $patient->patient_id ?></td>
                                    <td><?= Html::encode($patient->getFullName()) ?></td>
                                    <td><?= Html::encode($patient->sex) ?></td>
                                    <td><?= $patient->getAgeDisplay() ?></td>
                                    <td><?= Yii::$app->formatter->asDatetime($patient->created_at, 'short') ?></td>
                                    <td>
                                        <a href="<?= Url::to(['/patient/view', 'patient_id' => $patient->patient_id]) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0">No patients registered yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($role === 'receptionist'): ?>
    <!-- ============================================ -->
    <!-- RECEPTIONIST DASHBOARD -->
    <!-- ============================================ -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['pendingAppointments']) ?></h3>
                    <small class="text-muted">Pending Acceptance</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['scheduledAppointments']) ?></h3>
                    <small class="text-muted">Scheduled</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-sign-in-alt fa-2x text-info mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['checkedInAppointments']) ?></h3>
                    <small class="text-muted">Checked In</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-2x text-success mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['todayAppointments']) ?></h3>
                    <small class="text-muted">Today</small>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($role === 'doctor'): ?>
    <!-- ============================================ -->
    <!-- DOCTOR DASHBOARD -->
    <!-- ============================================ -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-info bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-2x text-info mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['todayAppointments']) ?></h3>
                    <small class="text-muted">Today's Appointments</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-clock fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['pendingConsultations']) ?></h3>
                    <small class="text-muted">Waiting for Consultation</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success bg-opacity-10 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['completedToday']) ?></h3>
                    <small class="text-muted">Completed Today</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>