<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDepartment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblDoctor $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-doctor-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-user-md"></i> <?= $model->isNewRecord ? 'Create Doctor' : 'Update Doctor' ?></h4>
        </div>
        <div class="card-body">
            
            <?php if ($model->isNewRecord): ?>
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Account Generation Info:</strong><br>
                A user account will be automatically created for this doctor.<br>
                <strong>Username format:</strong> <code>dr.firstname.lastname</code><br>
                <strong>Password format:</strong> <code>Lastname@emailusername</code> (e.g., email <code>jose.rizal@gmail.com</code> → password <code>Rizal@joserizal</code>)
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'doctor-form']]); ?>

            <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'first_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'First name',
                        'required' => true
                    ])->label('First Name *') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'middle_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Middle name (optional)'
                    ])->label('Middle Name') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'last_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Last name',
                        'required' => true,
                        'id' => 'doctor-lastname'
                    ])->label('Last Name *') ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-id-card"></i> Professional Information</h5>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'license_number')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'PRC License Number'
                    ])->label('License Number') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'dr_fee')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'placeholder' => '500.00'
                    ])->label('Consultation Fee (₱)') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'dept_id')->dropDownList(
                        ArrayHelper::map(
                            TblDepartment::find()->orderBy(['dept_id' => SORT_ASC])->all(),
                            'dept_id',
                            function($model) {
                                return $model->dept_id . ' - ' . $model->dept_name;
                            }
                        ),
                        ['prompt' => '-- Select Department --', 'class' => 'form-control prompt-select']
                    )->label('Department') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'specialization')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'e.g., Cardiology, Pediatrics'
                    ])->label('Specialization') ?>
                </div>
            </div>

            <?= $form->field($model, 'certification')->textInput([
                'maxlength' => true,
                'placeholder' => 'e.g., Board Certified - Philippine College of Cardiology'
            ])->label('Certification') ?>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-envelope"></i> Account Information</h5>

            <?= $form->field($model, 'email')->textInput([
                'type' => 'email',
                'placeholder' => 'doctor@hospital.com',
                'required' => true,
                'id' => 'doctor-email'
            ])->label('Email (for login) *') ?>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> This email will be used to create the doctor's login account. <strong>Required.</strong> Must be unique.
            </small>
            <div id="email-feedback" class="mt-1"></div>

            <?php if ($model->isNewRecord): ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold"><i class="fas fa-user"></i> Generated Username</label>
                        <div class="input-group">
                            <input type="text" id="username-preview" class="form-control" readonly 
                                   placeholder="dr.firstname.lastname" style="background-color: #f8f9fa; color: #212529; font-weight: 600;">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('username-preview')" title="Copy username">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold"><i class="fas fa-key"></i> Generated Password</label>
                        <div class="input-group">
                            <input type="text" id="password-preview" class="form-control" readonly 
                                   placeholder="Lastname@emailusername" style="background-color: #f8f9fa; color: #212529; font-weight: 600;">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('password-preview')" title="Copy password">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($model->isNewRecord ? 'Create Doctor & Generate Account' : 'Update Doctor'), [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?= Html::resetButton('<i class="fas fa-undo"></i> Reset', [
                    'class' => 'btn btn-secondary btn-lg',
                    'onclick' => 'resetForm()'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // ==========================================
    // DROPDOWN PROMPT DISABLE
    // ==========================================
    function disablePromptOption(selectElement) {
        var selectedValue = $(selectElement).val();
        $(selectElement).find('option[value=\"\"]').prop('disabled', selectedValue !== '');
        if (selectedValue === '') {
            $(selectElement).find('option[value=\"\"]').prop('disabled', false);
        }
    }
    
    $(document).on('change', '.prompt-select', function() {
        disablePromptOption(this);
    });
    
    // ==========================================
    // COPY TO CLIPBOARD FUNCTION
    // ==========================================
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(function() {
            var btn = copyText.nextElementSibling;
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class=\"fas fa-check text-success\"></i>';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-secondary');
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 1500);
        }).catch(function(err) {
            document.execCommand('copy');
        });
    }
    
    // ==========================================
    // UPDATE USERNAME & PASSWORD PREVIEW
    // ==========================================
    function updateCredentialPreview() {
        var firstname = $('input[name*=\"first_name\"]').val() || '';
        var lastname = $('input[name*=\"last_name\"]').val() || '';
        var email = $('#doctor-email').val() || '';
        
        // Generate username: dr.firstname.lastname
        var cleanFirst = firstname.toLowerCase().replace(/[^a-z0-9]/g, '');
        var cleanLast = lastname.toLowerCase().replace(/[^a-z0-9]/g, '');
        if (cleanFirst && cleanLast) {
            var username = 'dr.' + cleanFirst + '.' + cleanLast;
            $('#username-preview').val(username);
        } else {
            $('#username-preview').val('');
        }
        
        // Generate password: Lastname@emailusername
        var cleanLastCapitalized = '';
        if (lastname) {
            cleanLastCapitalized = lastname.charAt(0).toUpperCase() + lastname.slice(1).toLowerCase().replace(/[^a-zA-Z]/g, '');
        }
        var emailParts = email.split('@');
        var emailUsername = emailParts[0] || '';
        emailUsername = emailUsername.toLowerCase().replace(/[^a-z0-9]/g, '');
        
        if (cleanLastCapitalized && emailUsername) {
            var password = cleanLastCapitalized + '@' + emailUsername;
            $('#password-preview').val(password);
        } else {
            $('#password-preview').val('');
        }
    }
    
    // Update preview on input changes
    $('input[name*=\"first_name\"], input[name*=\"last_name\"]').on('input', function() {
        updateCredentialPreview();
    });
    
    // Email validation + update preview
    $('#doctor-email').on('change blur input', function() {
        var email = $(this).val().trim();
        if (email && email.includes('@')) {
            $(this).css('border-color', '#28a745');
            $('#email-feedback').html('<small class=\"text-success\">✓ Valid email</small>');
        } else if (email) {
            $(this).css('border-color', '#dc3545');
            $('#email-feedback').html('<small class=\"text-danger\">Please enter a valid email</small>');
        } else {
            $(this).css('border-color', '#ced4da');
            $('#email-feedback').html('');
        }
        updateCredentialPreview();
    });
    
    // ==========================================
    // RESET FORM
    // ==========================================
    function resetForm() {
        $('#doctor-email').css('border-color', '#ced4da');
        $('#email-feedback').html('');
        $('#username-preview').val('');
        $('#password-preview').val('');
    }
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    $(document).ready(function() {
        $('.prompt-select').each(function() { disablePromptOption(this); });
        
        var initialEmail = $('#doctor-email').val();
        if (initialEmail) $('#doctor-email').trigger('change');
        
        if ($('#doctor-lastname').val()) {
            updateCredentialPreview();
        }
    });
");
?>