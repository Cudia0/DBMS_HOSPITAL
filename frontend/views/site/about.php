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
                    <h5 class="mb-0"><i class="fas fa-heart"></i> Your Health, Our Priority</h5>
                </div>
                <div class="card-body">
                    <p><strong>MediSync</strong> is your personal healthcare companion. We make it easy to manage your health journey - from booking appointments to accessing your medical records, all in one convenient online portal.</p>
                    
                    <h5 class="mt-4 text-primary">What You Can Do</h5>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3" style="width: 50px; height: 50px; text-align: center;">
                                    <i class="fas fa-calendar-check fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <strong>Book Appointments</strong>
                                    <p class="text-muted small mb-0">Schedule visits with your preferred doctors at your convenience.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3" style="width: 50px; height: 50px; text-align: center;">
                                    <i class="fas fa-notes-medical fa-lg text-success"></i>
                                </div>
                                <div>
                                    <strong>View Medical Records</strong>
                                    <p class="text-muted small mb-0">Access your diagnosis, treatment plans, and health history anytime.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3" style="width: 50px; height: 50px; text-align: center;">
                                    <i class="fas fa-prescription fa-lg text-info"></i>
                                </div>
                                <div>
                                    <strong>Track Prescriptions</strong>
                                    <p class="text-muted small mb-0">View your prescribed medicines, dosages, and instructions.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3" style="width: 50px; height: 50px; text-align: center;">
                                    <i class="fas fa-file-invoice fa-lg text-warning"></i>
                                </div>
                                <div>
                                    <strong>Manage Bills</strong>
                                    <p class="text-muted small mb-0">View and track your hospital bills and payment status.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Why Choose MediSync?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-clock text-primary"></i> 24/7 Access</h6>
                            <p class="text-muted small">Access your health information anytime, anywhere through our secure online portal.</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-shield-alt text-success"></i> Secure & Private</h6>
                            <p class="text-muted small">Your medical data is protected with industry-standard security measures.</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-bolt text-warning"></i> Fast & Easy</h6>
                            <p class="text-muted small">Book appointments in minutes. No more waiting on phone calls or long queues.</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-history text-info"></i> Complete History</h6>
                            <p class="text-muted small">Keep all your medical records, prescriptions, and bills organized in one place.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> How It Works</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge bg-primary rounded-circle p-3" style="font-size: 20px;">1</span>
                        <p class="mt-2"><strong>Register</strong><br><small class="text-muted">Create your free patient account</small></p>
                    </div>
                    <div class="text-center mb-3">
                        <span class="badge bg-success rounded-circle p-3" style="font-size: 20px;">2</span>
                        <p class="mt-2"><strong>Book</strong><br><small class="text-muted">Choose your doctor and preferred time</small></p>
                    </div>
                    <div class="text-center mb-3">
                        <span class="badge bg-warning rounded-circle p-3" style="font-size: 20px;">3</span>
                        <p class="mt-2"><strong>Visit</strong><br><small class="text-muted">Attend your scheduled appointment</small></p>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-info rounded-circle p-3" style="font-size: 20px;">4</span>
                        <p class="mt-2"><strong>Access</strong><br><small class="text-muted">View your records and prescriptions online</small></p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-heartbeat"></i> Our Commitment</h5>
                </div>
                <div class="card-body">
                    <p class="small">We are committed to providing you with the best healthcare experience. Our platform is designed to make managing your health simple, secure, and accessible.</p>
                    <p class="small mb-0">Your health is our priority. We continuously improve our services to serve you better.</p>
                </div>
            </div>
        </div>
    </div>

</div>