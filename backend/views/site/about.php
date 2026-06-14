<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'About MediSync';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-hospital"></i> About the System</h5>
                </div>
                <div class="card-body">
                    <p><strong>MediSync</strong> is a comprehensive Hospital Management System designed to streamline healthcare operations.</p>
                    <p>It provides a complete solution for managing patients, doctors, appointments, medical records, prescriptions, laboratory tests, and billing - all in one integrated platform.</p>
                    
                    <h5 class="mt-4 text-primary">Key Features</h5>
                    <ul>
                        <li><strong>Patient Management</strong> - Register patients, manage profiles, track medical history</li>
                        <li><strong>Appointment Scheduling</strong> - Book, accept, reschedule, and track appointments</li>
                        <li><strong>Medical Records</strong> - Create and manage digital medical records with vital signs, diagnosis, and treatment plans</li>
                        <li><strong>Prescriptions</strong> - Generate prescriptions with multiple medicines, dosage instructions</li>
                        <li><strong>Laboratory Tests</strong> - Order lab tests, track status, record results</li>
                        <li><strong>Billing System</strong> - Auto-generated bills, itemized charges, payment processing</li>
                        <li><strong>Role-Based Access</strong> - Director, Doctor, Receptionist, and Patient portals</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> User Roles</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-dark h-100">
                                <div class="card-header bg-dark text-white"><strong><i class="fas fa-user-tie"></i> Director</strong></div>
                                <div class="card-body small">
                                    <ul class="mb-0">
                                        <li>Full system access</li>
                                        <li>Manage departments, staff</li>
                                        <li>Create doctor/receptionist accounts</li>
                                        <li>View all reports and analytics</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white"><strong><i class="fas fa-user-md"></i> Doctor</strong></div>
                                <div class="card-body small">
                                    <ul class="mb-0">
                                        <li>View assigned patients</li>
                                        <li>Create medical records</li>
                                        <li>Prescribe medicines</li>
                                        <li>Order laboratory tests</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-dark"><strong><i class="fas fa-user"></i> Receptionist</strong></div>
                                <div class="card-body small">
                                    <ul class="mb-0">
                                        <li>Manage patient registrations</li>
                                        <li>Accept and schedule appointments</li>
                                        <li>Check-in patients</li>
                                        <li>Process billing and payments</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white"><strong><i class="fas fa-procedures"></i> Patient</strong></div>
                                <div class="card-body small">
                                    <ul class="mb-0">
                                        <li>Register and manage profile</li>
                                        <li>Book appointments online</li>
                                        <li>View medical records</li>
                                        <li>View prescriptions and bills</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0 small">
                        <tr><th>Version:</th><td>1.0.0</td></tr>
                        <tr><th>Framework:</th><td>Yii2 Advanced</td></tr>
                        <tr><th>Database:</th><td>MariaDB</td></tr>
                        <tr><th>PHP Version:</th><td><?= PHP_VERSION ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-database"></i> Technology Stack</h5>
                </div>
                <div class="card-body small">
                    <p><strong>Backend:</strong> PHP 8.x with Yii2 Framework</p>
                    <p><strong>Frontend:</strong> Bootstrap 5, jQuery, JavaScript</p>
                    <p><strong>Database:</strong> MariaDB with raw SQL queries</p>
                    <p><strong>Icons:</strong> Font Awesome 6</p>
                    <p><strong>PDF:</strong> mPDF Library</p>
                </div>
            </div>
        </div>
    </div>

</div>