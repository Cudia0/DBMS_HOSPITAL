<?php

/** @var yii\web\View $this */
/** @var array $settings */
/** @var array $stats */

use yii\helpers\Html;

$this->title = 'System Configuration';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">

    <h1><i class="fas fa-cogs"></i> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <!-- System Settings -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Application Settings</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        These settings are configured in the parameter files.
                    </div>

                    <table class="table table-bordered">
                        <tr><th width="200">Setting</th><th>Value</th></tr>
                        <tr><td><strong>Application Name</strong></td><td><code><?= Html::encode($settings['appName']) ?></code></td></tr>
                        <tr><td><strong>Admin Email</strong></td><td><code><?= Html::encode($settings['adminEmail']) ?></code></td></tr>
                        <tr><td><strong>Support Email</strong></td><td><code><?= Html::encode($settings['supportEmail']) ?></code></td></tr>
                        <tr><td><strong>Sender Email</strong></td><td><code><?= Html::encode($settings['senderEmail']) ?></code></td></tr>
                        <tr><td><strong>Sender Name</strong></td><td><code><?= Html::encode($settings['senderName']) ?></code></td></tr>
                        <tr><td><strong>Password Min Length</strong></td><td><code><?= $settings['passwordMinLength'] ?> characters</code></td></tr>
                        <tr><td><strong>Password Reset Expire</strong></td><td><code><?= $settings['passwordResetExpire'] ?> seconds</code></td></tr>
                    </table>

                    <h6>Config Files:</h6>
                    <ul class="small text-muted">
                        <li><code>common/config/params.php</code></li>
                        <li><code>frontend/config/params.php</code></li>
                        <li><code>backend/config/params.php</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-server"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="200">Property</th><th>Value</th></tr>
                        <tr><td><strong>PHP Version</strong></td><td><span class="badge bg-success"><?= $stats['phpVersion'] ?></span></td></tr>
                        <tr><td><strong>Yii2 Version</strong></td><td><span class="badge bg-primary"><?= $stats['yiiVersion'] ?></span></td></tr>
                        <tr><td><strong>Database Size</strong></td><td><span class="badge bg-info"><?= $stats['databaseSize'] ?></span></td></tr>
                        <tr><td><strong>Server Time</strong></td><td><?= date('Y-m-d H:i:s') ?></td></tr>
                        <tr><td><strong>Timezone</strong></td><td><?= date_default_timezone_get() ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Cache Management -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-broom"></i> Maintenance</h5>
                </div>
                <div class="card-body">
                    <?= Html::a('<i class="fas fa-eraser"></i> Clear Application Cache', ['clear-cache'], [
                        'class' => 'btn btn-warning',
                        'data' => [
                            'confirm' => 'Clear all application cache?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-database"></i> Database Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-6 mb-3 text-center">
                            <h3 class="text-primary"><?= number_format($stats['totalPatients']) ?></h3>
                            <small class="text-muted">Patients</small>
                        </div>
                        <div class="col-md-2 col-6 mb-3 text-center">
                            <h3 class="text-success"><?= number_format($stats['totalDoctors']) ?></h3>
                            <small class="text-muted">Doctors</small>
                        </div>
                        <div class="col-md-2 col-6 mb-3 text-center">
                            <h3 class="text-info"><?= number_format($stats['totalDepartments']) ?></h3>
                            <small class="text-muted">Departments</small>
                        </div>
                        <div class="col-md-2 col-6 mb-3 text-center">
                            <h3 class="text-warning"><?= number_format($stats['totalMedicines']) ?></h3>
                            <small class="text-muted">Medicines</small>
                        </div>
                        <div class="col-md-2 col-6 mb-3 text-center">
                            <h3 class="text-danger"><?= number_format($stats['totalAppointments']) ?></h3>
                            <small class="text-muted">Appointments</small>
                        </div>
                        <div class="col-md-2 col-6 mb-3 text-center">
                            <h3 class="text-success"><?= number_format($stats['totalBills']) ?></h3>
                            <small class="text-muted">Bills</small>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <h3 class="text-info"><?= number_format($stats['todayAppointments']) ?></h3>
                            <small class="text-muted">Today's Appointments</small>
                        </div>
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <h3 class="text-warning"><?= number_format($stats['pendingAppointments']) ?></h3>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <h3 class="text-success"><?= number_format($stats['completedAppointments']) ?></h3>
                            <small class="text-muted">Completed</small>
                        </div>
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <h3 class="text-success">₱<?= number_format($stats['totalRevenue'], 2) ?></h3>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>