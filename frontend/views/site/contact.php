<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Contact Us';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-envelope"></i> Get in Touch</h5>
                </div>
                <div class="card-body">
                    <p>Have questions or need assistance? We're here to help! Reach out to us through any of the channels below.</p>
                    
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <div>
                                <strong>Our Location</strong><br>
                                <span class="text-muted">123 Health Street, Medical City, Philippines</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-phone fa-2x text-success"></i>
                            </div>
                            <div>
                                <strong>Phone</strong><br>
                                <span class="text-muted">(02) 8123-4567</span><br>
                                <span class="text-muted small">Available during clinic hours</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-envelope fa-2x text-info"></i>
                            </div>
                            <div>
                                <strong>Email</strong><br>
                                <span class="text-muted">support@medisync.com</span><br>
                                <span class="text-muted small">We'll respond within 24 hours</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-dark bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-clock fa-2x text-dark"></i>
                            </div>
                            <div>
                                <strong>Clinic Hours</strong><br>
                                <span class="text-muted">Monday - Friday: 8:00 AM - 5:00 PM</span><br>
                                <span class="text-muted">Saturday: 9:00 AM - 12:00 PM</span><br>
                                <span class="text-muted">Sunday: Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Frequently Asked Questions</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I book an appointment?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    After logging in, click "Book Appointment" from the menu. Select your preferred doctor, describe your symptoms, and submit. The receptionist will confirm your schedule.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I view my medical records?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Go to "My Medical Records" from the menu. You can view all your past consultations, diagnoses, and treatment plans.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Can I cancel an appointment?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Please contact the clinic directly to cancel or reschedule your appointment. You can reach us by phone during clinic hours.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    I forgot my password. What should I do?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Click "Forgot password?" on the login page. Enter your email address and we'll send you a password reset link.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    How do I pay my bill?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Bills can be paid at the clinic during your visit. View your bill details under "My Bills" to see the breakdown of charges.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card ">
                <div class="card-body text-center">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5>Need Immediate Help?</h5>
                    <p class="small text-muted">For urgent medical concerns, please call emergency services or visit the nearest hospital.</p>
                    <p class="small mb-0"><strong>Emergency Hotline:</strong> 911</p>
                </div>
            </div>
        </div>
    </div>

</div>