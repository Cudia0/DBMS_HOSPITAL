<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblPatient $model */
/** @var yii\widgets\ActiveForm $form */

// Country codes list
$countryCodes = [
    '+1' => '+1 (USA/Canada)',
    '+1-242' => '+1-242 (Bahamas)',
    '+1-246' => '+1-246 (Barbados)',
    '+1-264' => '+1-264 (Anguilla)',
    '+1-268' => '+1-268 (Antigua & Barbuda)',
    '+1-284' => '+1-284 (British Virgin Islands)',
    '+1-345' => '+1-345 (Cayman Islands)',
    '+1-441' => '+1-441 (Bermuda)',
    '+1-473' => '+1-473 (Grenada)',
    '+1-649' => '+1-649 (Turks & Caicos)',
    '+1-664' => '+1-664 (Montserrat)',
    '+1-670' => '+1-670 (Northern Mariana Islands)',
    '+1-671' => '+1-671 (Guam)',
    '+1-684' => '+1-684 (American Samoa)',
    '+1-758' => '+1-758 (Saint Lucia)',
    '+1-767' => '+1-767 (Dominica)',
    '+1-784' => '+1-784 (St. Vincent & Grenadines)',
    '+1-787' => '+1-787 (Puerto Rico)',
    '+1-809' => '+1-809 (Dominican Republic)',
    '+1-829' => '+1-829 (Dominican Republic)',
    '+1-849' => '+1-849 (Dominican Republic)',
    '+1-868' => '+1-868 (Trinidad & Tobago)',
    '+1-869' => '+1-869 (Saint Kitts & Nevis)',
    '+1-876' => '+1-876 (Jamaica)',
    '+1-939' => '+1-939 (Puerto Rico)',
    '+7' => '+7 (Russia/Kazakhstan)',
    '+20' => '+20 (Egypt)',
    '+27' => '+27 (South Africa)',
    '+30' => '+30 (Greece)',
    '+31' => '+31 (Netherlands)',
    '+32' => '+32 (Belgium)',
    '+33' => '+33 (France)',
    '+34' => '+34 (Spain)',
    '+36' => '+36 (Hungary)',
    '+39' => '+39 (Italy)',
    '+40' => '+40 (Romania)',
    '+41' => '+41 (Switzerland)',
    '+43' => '+43 (Austria)',
    '+44' => '+44 (UK)',
    '+45' => '+45 (Denmark)',
    '+46' => '+46 (Sweden)',
    '+47' => '+47 (Norway)',
    '+48' => '+48 (Poland)',
    '+49' => '+49 (Germany)',
    '+51' => '+51 (Peru)',
    '+52' => '+52 (Mexico)',
    '+53' => '+53 (Cuba)',
    '+54' => '+54 (Argentina)',
    '+55' => '+55 (Brazil)',
    '+56' => '+56 (Chile)',
    '+57' => '+57 (Colombia)',
    '+58' => '+58 (Venezuela)',
    '+60' => '+60 (Malaysia)',
    '+61' => '+61 (Australia)',
    '+62' => '+62 (Indonesia)',
    '+63' => '+63 (Philippines)',
    '+64' => '+64 (New Zealand)',
    '+65' => '+65 (Singapore)',
    '+66' => '+66 (Thailand)',
    '+81' => '+81 (Japan)',
    '+82' => '+82 (South Korea)',
    '+84' => '+84 (Vietnam)',
    '+86' => '+86 (China)',
    '+90' => '+90 (Turkey)',
    '+91' => '+91 (India)',
    '+92' => '+92 (Pakistan)',
    '+93' => '+93 (Afghanistan)',
    '+94' => '+94 (Sri Lanka)',
    '+95' => '+95 (Myanmar)',
    '+98' => '+98 (Iran)',
    '+212' => '+212 (Morocco)',
    '+213' => '+213 (Algeria)',
    '+216' => '+216 (Tunisia)',
    '+218' => '+218 (Libya)',
    '+220' => '+220 (Gambia)',
    '+221' => '+221 (Senegal)',
    '+222' => '+222 (Mauritania)',
    '+223' => '+223 (Mali)',
    '+224' => '+224 (Guinea)',
    '+225' => '+225 (Côte d\'Ivoire)',
    '+226' => '+226 (Burkina Faso)',
    '+227' => '+227 (Niger)',
    '+228' => '+228 (Togo)',
    '+229' => '+229 (Benin)',
    '+230' => '+230 (Mauritius)',
    '+231' => '+231 (Liberia)',
    '+232' => '+232 (Sierra Leone)',
    '+233' => '+233 (Ghana)',
    '+234' => '+234 (Nigeria)',
    '+235' => '+235 (Chad)',
    '+236' => '+236 (Central African Republic)',
    '+237' => '+237 (Cameroon)',
    '+238' => '+238 (Cape Verde)',
    '+239' => '+239 (São Tomé & Príncipe)',
    '+240' => '+240 (Equatorial Guinea)',
    '+241' => '+241 (Gabon)',
    '+242' => '+242 (Congo)',
    '+243' => '+243 (DR Congo)',
    '+244' => '+244 (Angola)',
    '+245' => '+245 (Guinea-Bissau)',
    '+246' => '+246 (British Indian Ocean)',
    '+248' => '+248 (Seychelles)',
    '+249' => '+249 (Sudan)',
    '+250' => '+250 (Rwanda)',
    '+251' => '+251 (Ethiopia)',
    '+252' => '+252 (Somalia)',
    '+253' => '+253 (Djibouti)',
    '+254' => '+254 (Kenya)',
    '+255' => '+255 (Tanzania)',
    '+256' => '+256 (Uganda)',
    '+257' => '+257 (Burundi)',
    '+258' => '+258 (Mozambique)',
    '+260' => '+260 (Zambia)',
    '+261' => '+261 (Madagascar)',
    '+262' => '+262 (Réunion)',
    '+263' => '+263 (Zimbabwe)',
    '+264' => '+264 (Namibia)',
    '+265' => '+265 (Malawi)',
    '+266' => '+266 (Lesotho)',
    '+267' => '+267 (Botswana)',
    '+268' => '+268 (Eswatini)',
    '+269' => '+269 (Comoros)',
    '+290' => '+290 (Saint Helena)',
    '+291' => '+291 (Eritrea)',
    '+297' => '+297 (Aruba)',
    '+298' => '+298 (Faroe Islands)',
    '+299' => '+299 (Greenland)',
    '+350' => '+350 (Gibraltar)',
    '+351' => '+351 (Portugal)',
    '+352' => '+352 (Luxembourg)',
    '+353' => '+353 (Ireland)',
    '+354' => '+354 (Iceland)',
    '+355' => '+355 (Albania)',
    '+356' => '+356 (Malta)',
    '+357' => '+357 (Cyprus)',
    '+358' => '+358 (Finland)',
    '+359' => '+359 (Bulgaria)',
    '+370' => '+370 (Lithuania)',
    '+371' => '+371 (Latvia)',
    '+372' => '+372 (Estonia)',
    '+373' => '+373 (Moldova)',
    '+374' => '+374 (Armenia)',
    '+375' => '+375 (Belarus)',
    '+376' => '+376 (Andorra)',
    '+377' => '+377 (Monaco)',
    '+378' => '+378 (San Marino)',
    '+379' => '+379 (Vatican City)',
    '+380' => '+380 (Ukraine)',
    '+381' => '+381 (Serbia)',
    '+382' => '+382 (Montenegro)',
    '+383' => '+383 (Kosovo)',
    '+385' => '+385 (Croatia)',
    '+386' => '+386 (Slovenia)',
    '+387' => '+387 (Bosnia & Herzegovina)',
    '+389' => '+389 (North Macedonia)',
    '+420' => '+420 (Czech Republic)',
    '+421' => '+421 (Slovakia)',
    '+423' => '+423 (Liechtenstein)',
    '+500' => '+500 (Falkland Islands)',
    '+501' => '+501 (Belize)',
    '+502' => '+502 (Guatemala)',
    '+503' => '+503 (El Salvador)',
    '+504' => '+504 (Honduras)',
    '+505' => '+505 (Nicaragua)',
    '+506' => '+506 (Costa Rica)',
    '+507' => '+507 (Panama)',
    '+509' => '+509 (Haiti)',
    '+590' => '+590 (Guadeloupe)',
    '+591' => '+591 (Bolivia)',
    '+592' => '+592 (Guyana)',
    '+593' => '+593 (Ecuador)',
    '+594' => '+594 (French Guiana)',
    '+595' => '+595 (Paraguay)',
    '+596' => '+596 (Martinique)',
    '+597' => '+597 (Suriname)',
    '+598' => '+598 (Uruguay)',
    '+599' => '+599 (Curaçao)',
    '+670' => '+670 (Timor-Leste)',
    '+672' => '+672 (Antarctica)',
    '+673' => '+673 (Brunei)',
    '+674' => '+674 (Nauru)',
    '+675' => '+675 (Papua New Guinea)',
    '+676' => '+676 (Tonga)',
    '+677' => '+677 (Solomon Islands)',
    '+678' => '+678 (Vanuatu)',
    '+679' => '+679 (Fiji)',
    '+680' => '+680 (Palau)',
    '+681' => '+681 (Wallis & Futuna)',
    '+682' => '+682 (Cook Islands)',
    '+683' => '+683 (Niue)',
    '+685' => '+685 (Samoa)',
    '+686' => '+686 (Kiribati)',
    '+687' => '+687 (New Caledonia)',
    '+688' => '+688 (Tuvalu)',
    '+689' => '+689 (French Polynesia)',
    '+690' => '+690 (Tokelau)',
    '+691' => '+691 (Micronesia)',
    '+692' => '+692 (Marshall Islands)',
    '+850' => '+850 (North Korea)',
    '+852' => '+852 (Hong Kong)',
    '+853' => '+853 (Macau)',
    '+855' => '+855 (Cambodia)',
    '+856' => '+856 (Laos)',
    '+880' => '+880 (Bangladesh)',
    '+886' => '+886 (Taiwan)',
    '+960' => '+960 (Maldives)',
    '+961' => '+961 (Lebanon)',
    '+962' => '+962 (Jordan)',
    '+963' => '+963 (Syria)',
    '+964' => '+964 (Iraq)',
    '+965' => '+965 (Kuwait)',
    '+966' => '+966 (Saudi Arabia)',
    '+967' => '+967 (Yemen)',
    '+968' => '+968 (Oman)',
    '+970' => '+970 (Palestine)',
    '+971' => '+971 (UAE)',
    '+972' => '+972 (Israel)',
    '+973' => '+973 (Bahrain)',
    '+974' => '+974 (Qatar)',
    '+975' => '+975 (Bhutan)',
    '+976' => '+976 (Mongolia)',
    '+977' => '+977 (Nepal)',
    '+992' => '+992 (Tajikistan)',
    '+993' => '+993 (Turkmenistan)',
    '+994' => '+994 (Azerbaijan)',
    '+995' => '+995 (Georgia)',
    '+996' => '+996 (Kyrgyzstan)',
    '+998' => '+998 (Uzbekistan)',
];

// Phone number limits by country code
$phoneLimits = [
    '+1' => 10, '+1-242' => 7, '+1-246' => 7, '+1-264' => 7, '+1-268' => 7,
    '+1-284' => 7, '+1-345' => 7, '+1-441' => 7, '+1-473' => 7, '+1-649' => 7,
    '+1-664' => 7, '+1-670' => 7, '+1-671' => 7, '+1-684' => 7, '+1-758' => 7,
    '+1-767' => 7, '+1-784' => 7, '+1-787' => 10, '+1-809' => 10, '+1-829' => 10,
    '+1-849' => 10, '+1-868' => 7, '+1-869' => 7, '+1-876' => 10, '+1-939' => 10,
    '+44' => 10, '+63' => 10, '+91' => 10, '+86' => 11, '+81' => 10,
    '+82' => 10, '+49' => 11, '+33' => 9, '+39' => 10, '+7' => 10,
    '+34' => 9, '+55' => 11, '+61' => 9, '+64' => 9, '+65' => 8,
    '+66' => 9, '+84' => 9, '+90' => 10, '+92' => 10, '+98' => 10,
];

// Countries list for address
$countries = [
    'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina',
    'Armenia', 'Australia', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain',
    'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin',
    'Bhutan', 'Bolivia', 'Bosnia & Herzegovina', 'Botswana', 'Brazil', 'Brunei',
    'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada',
    'Cape Verde', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros',
    'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic',
    'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
    'El Salvador', 'Estonia', 'Ethiopia', 'Fiji', 'Finland', 'France',
    'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece',
    'Grenada', 'Guatemala', 'Guinea', 'Guyana', 'Haiti', 'Honduras',
    'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq',
    'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan',
    'Kazakhstan', 'Kenya', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia',
    'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania',
    'Luxembourg', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali',
    'Malta', 'Mexico', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro',
    'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nepal', 'Netherlands',
    'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'Norway',
    'Oman', 'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea',
    'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar',
    'Romania', 'Russia', 'Rwanda', 'Saudi Arabia', 'Senegal', 'Serbia',
    'Seychelles', 'Singapore', 'Slovakia', 'Slovenia', 'Somalia', 'South Africa',
    'South Korea', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden',
    'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand',
    'Togo', 'Tonga', 'Trinidad & Tobago', 'Tunisia', 'Turkey', 'Turkmenistan',
    'Uganda', 'Ukraine', 'UAE', 'UK', 'USA', 'Uruguay',
    'Uzbekistan', 'Vatican City', 'Venezuela', 'Vietnam', 'Yemen', 'Zambia',
    'Zimbabwe',
];
?>

<div class="tbl-patient-form">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user-plus"></i> Patient Registration</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'options' => ['id' => 'patient-form'],
            ]); ?>

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
                               readonly placeholder="Auto-calculated">
                        <?= $form->field($model, 'age')->hiddenInput(['id' => 'patient-age'])->label(false) ?>
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
            <small class="text-muted">Use Gmail address (example@gmail.com) or type "N/A" if no email available</small>
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
                    'class' => 'btn btn-success btn-lg',
                    'id' => 'submit-btn'
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
    
    // ==========================================
    // DROPDOWN PROMPT DISABLE FUNCTION
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
    
    $(document).ready(function() {
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
    });
    
    // ==========================================
    // PHONE NUMBER FUNCTIONS
    // ==========================================
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
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
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
                limitText.text('Phone number limited to ' + limit + ' digits').addClass('text-danger').removeClass('text-muted text-success');
            } else if (phoneNum.length === limit) {
                limitText.text('✓ Complete (' + limit + ' digits)').removeClass('text-danger text-muted').addClass('text-success');
            } else {
                limitText.text(phoneNum.length + '/' + limit + ' digits').removeClass('text-danger text-success').addClass('text-muted');
            }
        }
    });
    
    function validatePhoneNumber() {
        $('#patient-phone_num').trigger('input');
    }
    
    // ==========================================
    // EMAIL VALIDATION
    // ==========================================
    $('#patient-email').on('change blur input', function() {
        var email = $(this).val().trim();
        var emailField = $(this);
        var feedback = $('#email-feedback');
        
        if (email === '' || email === 'N/A' || email === 'n/a') {
            emailField.css('border-color', '#28a745');
            feedback.html('<small class=\"text-success\">✓ No email required</small>');
            return true;
        }
        
        var gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        if (gmailRegex.test(email)) {
            emailField.css('border-color', '#28a745');
            feedback.html('<small class=\"text-success\">✓ Valid Gmail address</small>');
            return true;
        } else {
            emailField.css('border-color', '#dc3545');
            feedback.html('<small class=\"text-danger\">✗ Please use a valid Gmail address (example@gmail.com) or type N/A</small>');
            return false;
        }
    });
    
    // ==========================================
    // ADDRESS COMBINATION
    // ==========================================
    $('#address-line1, #address-line2, #address-city, #address-state, #address-zip, #address-country').on('change input', function() {
        var line1 = $('#address-line1').val().trim();
        var line2 = $('#address-line2').val().trim();
        var city = $('#address-city').val().trim();
        var state = $('#address-state').val().trim();
        var zip = $('#address-zip').val().trim();
        var country = $('#address-country').val();
        
        var addressParts = [];
        if (line1) addressParts.push(line1);
        if (line2) addressParts.push(line2);
        if (city) addressParts.push(city);
        if (state) addressParts.push(state);
        if (zip) addressParts.push(zip);
        if (country) addressParts.push(country);
        
        var fullAddress = addressParts.join(', ');
        $('#patient-address').val(fullAddress);
        
        if (fullAddress) {
            $('#address-preview').show();
            $('#address-preview-text').text(fullAddress);
        } else {
            $('#address-preview').hide();
        }
    });
    
    // ==========================================
    // AGE AUTO-CALCULATION (Fixed)
    // ==========================================
    $('#patient-dob').on('change', function() {
        var dob = $(this).val();
        if (dob) {
            var today = new Date();
            var birthDate = new Date(dob);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            if (age >= 0) {
                // Store integer in hidden field for database
                $('#patient-age').val(age);
                // Display formatted text in readonly field
                $('#patient-age-display').val(age + ' years old');
                $('#patient-age-display').css('background-color', '#d4edda');
            } else {
                // Store 0 in hidden field for invalid date
                $('#patient-age').val(0);
                $('#patient-age-display').val('Invalid date');
                $('#patient-age-display').css('background-color', '#f8d7da');
            }
        } else {
            // Clear both fields if no DOB
            $('#patient-age').val('');
            $('#patient-age-display').val('');
            $('#patient-age-display').css('background-color', '#ffffff');
        }
    });
    
    // ==========================================
    // RESET FORM
    // ==========================================
    function resetForm() {
        $('#address-preview').hide();
        $('#phone-limit-text').text('Please select a country code first').removeClass('text-danger text-success').addClass('text-muted');
        $('#patient-email').css('border-color', '#ced4da');
        $('#email-feedback').html('');
        $('#patient-age').val('');
        $('#patient-age-display').val('').css('background-color', '#ffffff');
        
        setTimeout(function() {
            $('.prompt-select').each(function() {
                $(this).find('option[value=\"\"]').prop('disabled', false);
            });
        }, 100);
    }
    
    // ==========================================
    // FORM SUBMISSION
    // ==========================================
    $('#patient-form').on('beforeSubmit', function() {
        var email = $('#patient-email').val().trim();
        if (email && email !== 'N/A' && email !== 'n/a') {
            var gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
            if (!gmailRegex.test(email)) {
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
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    $(document).ready(function() {
        // Initialize country code
        var initialCountryCode = $('#patient-country_code').val();
        if (initialCountryCode) {
            $('#patient-country_code').trigger('change');
        }
        
        // Initialize email validation
        var initialEmail = $('#patient-email').val();
        if (initialEmail) {
            $('#patient-email').trigger('change');
        }
        
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
        
        // Initialize age display if editing
        var existingAge = $('#patient-age').val();
        if (existingAge && existingAge > 0) {
            $('#patient-age-display').val(existingAge + ' years old');
            $('#patient-age-display').css('background-color', '#d4edda');
        }
        
        // Calculate age if DOB exists
        var existingDob = $('#patient-dob').val();
        if (existingDob) {
            $('#patient-dob').trigger('change');
        }
        
        // Initialize all dropdown prompts
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
    });
");
?>