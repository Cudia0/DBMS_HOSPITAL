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
                    <p>For support, inquiries, or assistance with the MediSync Hospital Management System, please contact us through any of the channels below.</p>
                    
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <div>
                                <strong>Address</strong><br>
                                <span class="text-muted">123 Health Street, Medical City, Philippines</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-phone fa-2x text-success"></i>
                            </div>
                            <div>
                                <strong>Phone</strong><br>
                                <span class="text-muted">(02) 8123-4567</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-envelope fa-2x text-info"></i>
                            </div>
                            <div>
                                <strong>Email</strong><br>
                                <span class="text-muted">support@medisync.com</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-globe fa-2x text-warning"></i>
                            </div>
                            <div>
                                <strong>Website</strong><br>
                                <span class="text-muted">www.medisync.com</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-dark bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-clock fa-2x text-dark"></i>
                            </div>
                            <div>
                                <strong>Business Hours</strong><br>
                                <span class="text-muted">Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 12:00 PM</span>
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
                                    How do I reset my password?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Go to Profile Settings and use the "Change Password" option. You need to enter your current password first.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    How do I add a new doctor?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Only Directors can add doctors. Go to Staff → Doctors → Create Doctor. A user account will be automatically generated.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How are bills generated?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Bills are automatically generated when a doctor creates a prescription. Receptionists can add additional charges like lab tests.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Can a patient have multiple appointments?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body small">
                                    Yes, patients can book multiple appointments. Each appointment is independent and can be with different doctors on different dates.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-life-ring"></i> Technical Support</h5>
                </div>
                <div class="card-body">
                    <p class="small">For technical issues or system bugs, please contact the IT department:</p>
                    <ul class="small mb-0">
                        <li>Email: it@medisync.com</li>
                        <li>Internal Extension: 888</li>
                        <li>Submit a ticket through the help desk system</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>