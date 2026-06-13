<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TblPatient $model */
/** @var yii\widgets\ActiveForm $form */

// Country codes list (kept for brevity - same as before)
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

$countries = [
    'Philippines', 'USA', 'Canada', 'UK', 'Australia', 'Japan', 'South Korea',
    'China', 'India', 'Singapore', 'Malaysia', 'Indonesia', 'Vietnam', 'Thailand',
    'France', 'Germany', 'Italy', 'Spain', 'Russia', 'Brazil', 'Mexico',
];
?>

<div class="tbl-patient-form">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-plus"></i> Patient Registration</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'patient-form']]); ?>

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

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'sex')->dropDownList([ 
                        'Male' => 'Male', 
                        'Female' => 'Female',
                    ], [
                        'prompt' => '-- Select Sex --',
                        'required' => true,
                        'id' => 'patient-sex',
                        'class' => 'form-control prompt-select'
                    ])->label('Sex *') ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Age</label>
                        <input type="text" id="patient-age-display" class="form-control" 
                               readonly placeholder="Auto-calculated from DOB">
                    </div>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'date_of_birth')->input('date', [
                        'max' => date('Y-m-d'),
                        'id' => 'patient-dob'
                    ])->label('Date of Birth') ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h5>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'country_code')->dropDownList(
                        $countryCodes,
                        [
                            'prompt' => '-- Select Country Code --',
                            'id' => 'patient-country_code',
                            'class' => 'form-control prompt-select'
                        ]
                    )->label('Country Code *') ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'phone_num')->textInput([
                        'placeholder' => 'Phone number',
                        'id' => 'patient-phone_num',
                        'onkeypress' => 'return isNumber(event)'
                    ])->label('Phone Number *') ?>
                    <small class="text-muted" id="phone-limit-text">Please select a country code first</small>
                </div>
            </div>

            <?= $form->field($model, 'email')->textInput([
                'type' => 'email',
                'placeholder' => 'example@gmail.com or N/A',
                'id' => 'patient-email'
            ])->label('Email (Gmail only or N/A)') ?>
            <small class="text-muted">Use Gmail address or type "N/A" if no email available</small>
            <div id="email-feedback" class="mt-1"></div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-map-marker-alt"></i> Address Information</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Address Line 1 *</label>
                        <input type="text" id="address-line1" class="form-control" 
                               placeholder="House/Unit No., Street name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Address Line 2</label>
                        <input type="text" id="address-line2" class="form-control" 
                               placeholder="Barangay, Subdivision (optional)">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">City / Town *</label>
                        <input type="text" id="address-city" class="form-control" 
                               placeholder="City or Town">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">State / Province *</label>
                        <input type="text" id="address-state" class="form-control" 
                               placeholder="State or Province">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Postal / ZIP Code *</label>
                        <input type="text" id="address-zip" class="form-control" 
                               placeholder="Postal code">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Country *</label>
                        <select id="address-country" class="form-control prompt-select">
                            <option value="">-- Select Country --</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country ?>"><?= $country ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <?= $form->field($model, 'address')->hiddenInput(['id' => 'patient-address'])->label(false) ?>

            <div class="alert alert-info mt-3" id="address-preview" style="display:none;">
                <strong><i class="fas fa-eye"></i> Address Preview:</strong><br>
                <span id="address-preview-text"></span>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> Register Patient', [
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
    $('#patient-country_code').on('change', function() {
        var countryCode = $(this).val();
        var phoneInput = $('#patient-phone_num');
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
    
    $('#patient-phone_num').on('input', function() {
        var countryCode = $('#patient-country_code').val();
        var phoneNum = $(this).val().replace(/[^0-9]/g, '');
        var limitText = $('#phone-limit-text');
        $(this).val(phoneNum);
        
        if (countryCode && phoneLimits[countryCode]) {
            var limit = phoneLimits[countryCode];
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
        $('#patient-phone_num').trigger('input');
    }
    
    // Email validation
    $('#patient-email').on('change blur input', function() {
        var email = $(this).val().trim();
        if (email === '' || email === 'N/A' || email === 'n/a') {
            $(this).css('border-color', '#28a745');
            $('#email-feedback').html('<small class=\"text-success\">✓ No email required</small>');
        } else if (/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email)) {
            $(this).css('border-color', '#28a745');
            $('#email-feedback').html('<small class=\"text-success\">✓ Valid Gmail address</small>');
        } else {
            $(this).css('border-color', '#dc3545');
            $('#email-feedback').html('<small class=\"text-danger\">✗ Use valid Gmail or N/A</small>');
        }
    });
    
    // Address combination
    $('#address-line1, #address-line2, #address-city, #address-state, #address-zip, #address-country').on('change input', function() {
        var parts = [];
        var line1 = $('#address-line1').val().trim();
        var line2 = $('#address-line2').val().trim();
        var city = $('#address-city').val().trim();
        var state = $('#address-state').val().trim();
        var zip = $('#address-zip').val().trim();
        var country = $('#address-country').val();
        
        if (line1) parts.push(line1);
        if (line2) parts.push(line2);
        if (city) parts.push(city);
        if (state) parts.push(state);
        if (zip) parts.push(zip);
        if (country) parts.push(country);
        
        var fullAddress = parts.join(', ');
        $('#patient-address').val(fullAddress);
        
        if (fullAddress) {
            $('#address-preview').show();
            $('#address-preview-text').text(fullAddress);
        } else {
            $('#address-preview').hide();
        }
    });
    
    // Age auto-calculation from DOB (display only, not stored)
    $('#patient-dob').on('change', function() {
        var dob = $(this).val();
        if (dob) {
            var today = new Date();
            var birthDate = new Date(dob);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
            
            if (age >= 0) {
                $('#patient-age-display').val(age + ' years old');
                
            } else {
                $('#patient-age-display').val('Invalid date');
                
            }
        } else {
            $('#patient-age-display').val('');
            
        }
    });
    
    // Reset form
    function resetForm() {
        $('#address-preview').hide();
        $('#phone-limit-text').text('Please select a country code first').removeClass('text-danger text-success').addClass('text-muted');
        $('#patient-email').css('border-color', '#ced4da');
        $('#email-feedback').html('');
        $('#patient-age-display').val('').css('background-color', '#ffffff');
        setTimeout(function() {
            $('.prompt-select').each(function() {
                $(this).find('option[value=\"\"]').prop('disabled', false);
            });
        }, 100);
    }
    
    // Form submission
    $('#patient-form').on('beforeSubmit', function() {
        var email = $('#patient-email').val().trim();
        if (email && email !== 'N/A' && email !== 'n/a') {
            if (!/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email)) {
                alert('Please use a valid Gmail address or type N/A');
                return false;
            }
        }
        $('#address-line1').trigger('change');
        var address = $('#patient-address').val();
        if (!address) {
            alert('Please fill in at least Address Line 1 and City');
            return false;
        }
        return true;
    });
    
    // Initialize
    $(document).ready(function() {
        $('.prompt-select').each(function() { disablePromptOption(this); });
        
        var initialCountryCode = $('#patient-country_code').val();
        if (initialCountryCode) $('#patient-country_code').trigger('change');
        
        var initialEmail = $('#patient-email').val();
        if (initialEmail) $('#patient-email').trigger('change');
        
        // Parse existing address for update form
        var existingAddress = $('#patient-address').val();
        if (existingAddress) {
            var parts = existingAddress.split(', ');
            if (parts.length >= 1) $('#address-line1').val(parts[0] || '');
            if (parts.length >= 2) $('#address-line2').val(parts[1] || '');
            if (parts.length >= 3) $('#address-city').val(parts[2] || '');
            if (parts.length >= 4) $('#address-state').val(parts[3] || '');
            if (parts.length >= 5) $('#address-zip').val(parts[4] || '');
            if (parts.length >= 6) $('#address-country').val(parts[5] || '');
            $('#address-line1').trigger('change');
        }
        
        // Calculate age from existing DOB
        var existingDob = $('#patient-dob').val();
        if (existingDob) $('#patient-dob').trigger('change');
    });
");
?>