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
    <!-- DIRECTOR DASHBOARD -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Total Patients</h6><h2 class="mb-0"><?= number_format($stats['totalPatients']) ?></h2></div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3"><i class="fas fa-users fa-2x text-primary"></i></div>
                    </div>
                    <a href="<?= Url::to(['/patient/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Total Doctors</h6><h2 class="mb-0"><?= number_format($stats['totalDoctors']) ?></h2></div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3"><i class="fas fa-user-md fa-2x text-success"></i></div>
                    </div>
                    <a href="<?= Url::to(['/doctor/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Receptionists</h6><h2 class="mb-0"><?= number_format($stats['totalReceptionists']) ?></h2></div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3"><i class="fas fa-user fa-2x text-warning"></i></div>
                    </div>
                    <a href="<?= Url::to(['/receptionist/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="text-muted mb-1">Departments</h6><h2 class="mb-0"><?= number_format($stats['totalDepartments']) ?></h2></div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3"><i class="fas fa-building fa-2x text-info"></i></div>
                    </div>
                    <a href="<?= Url::to(['/department/index']) ?>" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-warning bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-clock fa-2x text-warning mb-2"></i><h3 class="mb-0"><?= number_format($stats['pendingAppointments']) ?></h3><small class="text-muted">Pending</small></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-primary bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-calendar-check fa-2x text-primary mb-2"></i><h3 class="mb-0"><?= number_format($stats['scheduledAppointments']) ?></h3><small class="text-muted">Scheduled</small></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-info bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-calendar-day fa-2x text-info mb-2"></i><h3 class="mb-0"><?= number_format($stats['todayAppointments']) ?></h3><small class="text-muted">Today</small></div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card bg-success bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-check-circle fa-2x text-success mb-2"></i><h3 class="mb-0"><?= number_format($stats['completedAppointments']) ?></h3><small class="text-muted">Completed</small></div></div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3"><div class="card bg-success text-white h-100"><div class="card-body"><h6 class="text-white-50">TOTAL REVENUE</h6><h2 class="mb-0">₱<?= number_format($stats['totalRevenue'], 2) ?></h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-info text-white h-100"><div class="card-body"><h6 class="text-white-50">THIS MONTH</h6><h2 class="mb-0">₱<?= number_format($stats['monthlyRevenue'], 2) ?></h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-danger text-white h-100"><div class="card-body"><h6 class="text-white-50">TOTAL BILLS</h6><h2 class="mb-0"><?= number_format($stats['totalBills']) ?></h2></div></div></div>
    </div>

    <?php elseif ($role === 'receptionist'): ?>
    <!-- RECEPTIONIST DASHBOARD -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3"><div class="card bg-warning bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-clock fa-2x text-warning mb-2"></i><h3 class="mb-0"><?= number_format($stats['pendingAppointments']) ?></h3><small class="text-muted">Pending</small></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-primary bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-calendar-check fa-2x text-primary mb-2"></i><h3 class="mb-0"><?= number_format($stats['scheduledAppointments']) ?></h3><small class="text-muted">Scheduled</small></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-info bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-sign-in-alt fa-2x text-info mb-2"></i><h3 class="mb-0"><?= number_format($stats['checkedInAppointments']) ?></h3><small class="text-muted">Checked In</small></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-success bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-calendar-day fa-2x text-success mb-2"></i><h3 class="mb-0"><?= number_format($stats['todayAppointments']) ?></h3><small class="text-muted">Today</small></div></div></div>
    </div>

    <?php elseif ($role === 'doctor'): ?>
    <!-- DOCTOR DASHBOARD -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3"><div class="card bg-info bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-calendar-day fa-2x text-info mb-2"></i><h3 class="mb-0"><?= number_format($stats['todayAppointments']) ?></h3><small class="text-muted">Today's Appointments</small></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-warning bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-user-clock fa-2x text-warning mb-2"></i><h3 class="mb-0"><?= number_format($stats['pendingConsultations']) ?></h3><small class="text-muted">Waiting</small></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-success bg-opacity-10 h-100"><div class="card-body text-center"><i class="fas fa-check-circle fa-2x text-success mb-2"></i><h3 class="mb-0"><?= number_format($stats['completedToday']) ?></h3><small class="text-muted">Completed Today</small></div></div></div>
    </div>
    <?php endif; ?>

</div>