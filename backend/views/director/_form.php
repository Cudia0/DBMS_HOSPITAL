<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TblDirector $model */
/** @var yii\widgets\ActiveForm $form */

$countryCodes = [
    '+63' => '+63 (Philippines)',
    '+1' => '+1 (USA/Canada)',
    '+44' => '+44 (UK)',
    '+81' => '+81 (Japan)',
    '+82' => '+82 (South Korea)',
    '+86' => '+86 (China)',
    '+91' => '+91 (India)',
    '+61' => '+61 (Australia)',
    '+64' => '+64 (New Zealand)',
    '+65' => '+65 (Singapore)',
    '+66' => '+66 (Thailand)',
    '+84' => '+84 (Vietnam)',
    '+60' => '+60 (Malaysia)',
    '+62' => '+62 (Indonesia)',
    '+33' => '+33 (France)',
    '+49' => '+49 (Germany)',
    '+39' => '+39 (Italy)',
    '+34' => '+34 (Spain)',
    '+7' => '+7 (Russia)',
    '+55' => '+55 (Brazil)',
    '+52' => '+52 (Mexico)',
    '+90' => '+90 (Turkey)',
    '+20' => '+20 (Egypt)',
    '+27' => '+27 (South Africa)',
    '+971' => '+971 (UAE)',
    '+966' => '+966 (Saudi Arabia)',
];

$phoneLimits = [
    '+63' => 10, '+1' => 10, '+44' => 10, '+81' => 10, '+82' => 10,
    '+86' => 11, '+91' => 10, '+61' => 9, '+64' => 9, '+65' => 8,
    '+66' => 9, '+84' => 9, '+60' => 9, '+62' => 10,
];
?>

<div class="tbl-director-form">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4 class="mb-0"><i class="fas fa-user-tie"></i> <?= $model->isNewRecord ? 'Create Director' : 'Update Director' ?></h4>
        </div>
        <div class="card-body">
            
            <?php if ($model->isNewRecord): ?>
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Note:</strong> 
                A user account will be automatically created for this director.<br>
                <strong>Username format:</strong> <code>dir.firstname.lastname</code><br>
                <strong>Password format:</strong> <code>Lastname@emaildomain</code> (e.g., <code>Reyes@hospitalcom</code>)
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'director-form']]); ?>

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
                        'required' => true
                    ])->label('Last Name *') ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-envelope"></i> Account Information</h5>

            <?= $form->field($model, 'email')->textInput([
                'type' => 'email',
                'placeholder' => 'director@hospital.com',
                'required' => true,
                'id' => 'director-email'
            ])->label('Email *') ?>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> This email will be used for login. <strong>Required.</strong> Must be unique.
            </small>
            <div id="email-feedback" class="mt-1"></div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h5>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'country_code')->dropDownList(
                        $countryCodes,
                        [
                            'prompt' => '-- Select Country Code --',
                            'id' => 'director-country_code',
                            'class' => 'form-control prompt-select'
                        ]
                    )->label('Country Code') ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'phone_num')->textInput([
                        'placeholder' => 'Phone number',
                        'id' => 'director-phone_num',
                        'onkeypress' => 'return isNumber(event)'
                    ])->label('Phone Number') ?>
                    <small class="text-muted" id="phone-limit-text">Please select a country code first</small>
                </div>
            </div>

            <!-- Password Preview -->
            <?php if ($model->isNewRecord): ?>
            <div class="alert alert-warning mt-3" id="password-preview" style="display:none;">
                <strong><i class="fas fa-key"></i> Generated Password Preview:</strong><br>
                <span id="password-preview-text"></span>
            </div>
            <?php endif; ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($model->isNewRecord ? 'Create Director & Generate Account' : 'Update Director'), [
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
    var phoneLimits = " . json_encode($phoneLimits) . ";
    
    // Dropdown prompt disable
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
    
    // Phone number functions
    $('#director-country_code').on('change', function() {
        var countryCode = $(this).val();
        var phoneInput = $('#director-phone_num');
        var limitText = $('#phone-limit-text');
        
        if (countryCode && phoneLimits[countryCode]) {
            var limit = phoneLimits[countryCode];
            phoneInput.attr('maxlength', limit);
            limitText.text('Maximum ' + limit + ' digits for ' + countryCode);
            limitText.removeClass('text-danger').addClass('text-muted');
        } else if (countryCode) {
            phoneInput.attr('maxlength', 15);
            limitText.text('Maximum 15 digits');
            limitText.removeClass('text-danger').addClass('text-muted');
        } else {
            phoneInput.attr('maxlength', 15);
            limitText.text('Please select a country code first');
            limitText.removeClass('text-danger').addClass('text-muted');
        }
        validatePhoneNumber();
    });
    
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
        return true;
    }
    
    $('#director-phone_num').on('input', function() {
        var countryCode = $('#director-country_code').val();
        var phoneNum = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(phoneNum);
        
        if (countryCode && phoneLimits[countryCode]) {
            var limit = phoneLimits[countryCode];
            var limitText = $('#phone-limit-text');
            if (phoneNum.length > limit) {
                $(this).val(phoneNum.substring(0, limit));
                limitText.text('Phone number limited to ' + limit + ' digits').addClass('text-danger');
            } else if (phoneNum.length === limit) {
                limitText.text('✓ Complete (' + limit + ' digits)').removeClass('text-danger').addClass('text-success');
            } else {
                limitText.text(phoneNum.length + '/' + limit + ' digits').removeClass('text-danger text-success').addClass('text-muted');
            }
        }
    });
    
    function validatePhoneNumber() {
        $('#director-phone_num').trigger('input');
    }
    
    // Email validation - Updated password preview
    $('#director-email').on('change blur input', function() {
    var email = $(this).val().trim();
    if (email && email.includes('@')) {
        $(this).css('border-color', '#28a745');
        $('#email-feedback').html('<small class=\"text-success\">✓ Valid email</small>');
        
        // Show password preview: Lastname@emailusername
        var lastname = $('input[name*=\"last_name\"]').val() || 'Lastname';
        lastname = lastname.charAt(0).toUpperCase() + lastname.slice(1).toLowerCase().replace(/[^a-zA-Z]/g, '');
        var emailUsername = email.split('@')[0] || 'user';
        emailUsername = emailUsername.toLowerCase().replace(/[^a-z0-9]/g, '');
        var password = lastname + '@' + emailUsername;
        $('#password-preview').show();
        $('#password-preview-text').html('<code>' + password + '</code>');
    } else if (email) {
        $(this).css('border-color', '#dc3545');
        $('#email-feedback').html('<small class=\"text-danger\">Please enter a valid email</small>');
        $('#password-preview').hide();
    } else {
        $(this).css('border-color', '#ced4da');
        $('#email-feedback').html('');
        $('#password-preview').hide();
    }
});
    
    // Update password preview when lastname changes
    $('input[name*=\"last_name\"]').on('input', function() {
        $('#director-email').trigger('change');
    });
    
    // Reset form
    function resetForm() {
        $('#phone-limit-text').text('Please select a country code first').removeClass('text-danger text-success').addClass('text-muted');
        $('#director-email').css('border-color', '#ced4da');
        $('#email-feedback').html('');
        $('#password-preview').hide();
    }
    
    // Initialize
    $(document).ready(function() {
        $('.prompt-select').each(function() { disablePromptOption(this); });
        var initialCountryCode = $('#director-country_code').val();
        if (initialCountryCode) $('#director-country_code').trigger('change');
        var initialEmail = $('#director-email').val();
        if (initialEmail) $('#director-email').trigger('change');
    });
");
?>