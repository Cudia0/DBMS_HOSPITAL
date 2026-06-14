<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Create a new account';
$this->params['breadcrumbs'][] = $this->title;

// Country codes list (same as patient form)
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
    '+263' => '+263 (Zimbabwe)',
    '+264' => '+264 (Namibia)',
    '+265' => '+265 (Malawi)',
    '+266' => '+266 (Lesotho)',
    '+267' => '+267 (Botswana)',
    '+268' => '+268 (Eswatini)',
    '+269' => '+269 (Comoros)',
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
    '+591' => '+591 (Bolivia)',
    '+592' => '+592 (Guyana)',
    '+593' => '+593 (Ecuador)',
    '+595' => '+595 (Paraguay)',
    '+597' => '+597 (Suriname)',
    '+598' => '+598 (Uruguay)',
    '+599' => '+599 (Curaçao)',
    '+670' => '+670 (Timor-Leste)',
    '+673' => '+673 (Brunei)',
    '+674' => '+674 (Nauru)',
    '+675' => '+675 (Papua New Guinea)',
    '+676' => '+676 (Tonga)',
    '+677' => '+677 (Solomon Islands)',
    '+678' => '+678 (Vanuatu)',
    '+679' => '+679 (Fiji)',
    '+680' => '+680 (Palau)',
    '+682' => '+682 (Cook Islands)',
    '+685' => '+685 (Samoa)',
    '+686' => '+686 (Kiribati)',
    '+688' => '+688 (Tuvalu)',
    '+689' => '+689 (French Polynesia)',
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

// Phone number limits
$phoneLimits = [
    '+63' => 10, '+1' => 10, '+44' => 10, '+81' => 10, '+82' => 10,
    '+86' => 11, '+91' => 10, '+61' => 9, '+64' => 9, '+65' => 8,
    '+66' => 9, '+84' => 9, '+60' => 9, '+62' => 10,
];

// Countries list for address
$countries = [
    'Philippines', 'USA', 'Canada', 'UK', 'Australia', 'Japan', 'South Korea',
    'China', 'India', 'Singapore', 'Malaysia', 'Indonesia', 'Vietnam', 'Thailand',
    'France', 'Germany', 'Italy', 'Spain', 'Russia', 'Brazil', 'Mexico',
];
?>
<div class="site-signup">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus"></i> <?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    
                    <p class="text-muted">Please fill out all fields to create your account and patient profile:</p>

                    <?php $form = ActiveForm::begin(['id' => 'form-signup', 'options' => ['id' => 'signup-form']]); ?>

                    <!-- ACCOUNT INFORMATION -->
                    <h5 class="text-primary mb-3"><i class="fas fa-lock"></i> Account Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'username')->textInput([
                                'autofocus' => true,
                                'placeholder' => 'Choose a username',
                                'class' => 'form-control'
                            ])->label('Username *') ?>
                            <small class="text-muted">This will be your login username</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'password')->passwordInput([
                                'placeholder' => 'Choose a password (min 6 characters)',
                                'class' => 'form-control'
                            ])->label('Password *') ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <?= $form->field($model, 'email')->textInput([
                            'type' => 'email',
                            'placeholder' => 'example@gmail.com',
                            'class' => 'form-control',
                            'id' => 'signup-email'
                        ])->label('Email (Gmail only) *') ?>
                        <small class="text-muted">Must be a valid Gmail address. This will be used for login and communication.</small>
                        <div id="email-feedback" class="mt-1"></div>
                    </div>

                    <hr>
                    <!-- PERSONAL INFORMATION -->
                    <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Personal Information</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?= $form->field($model, 'first_name')->textInput([
                                'placeholder' => 'First name',
                                'class' => 'form-control'
                            ])->label('First Name *') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <?= $form->field($model, 'middle_name')->textInput([
                                'placeholder' => 'Middle name (optional)',
                                'class' => 'form-control'
                            ])->label('Middle Name') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <?= $form->field($model, 'last_name')->textInput([
                                'placeholder' => 'Last name',
                                'class' => 'form-control'
                            ])->label('Last Name *') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?= $form->field($model, 'sex')->dropDownList([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ], [
                                'prompt' => '-- Select Sex --',
                                'class' => 'form-select prompt-select',
                                'id' => 'signup-sex'
                            ])->label('Sex *') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <?= $form->field($model, 'date_of_birth')->input('date', [
                                'max' => date('Y-m-d'),
                                'class' => 'form-control',
                                'id' => 'signup-dob'
                            ])->label('Date of Birth') ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Age</label>
                            <input type="text" id="signup-age-display" class="form-control" readonly 
                                   placeholder="Auto-calculated from DOB">
                        </div>
                    </div>

                    <hr>
                    <!-- CONTACT INFORMATION -->
                    <h5 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?= $form->field($model, 'country_code')->dropDownList(
                                $countryCodes,
                                [
                                    'prompt' => '-- Select Country Code --',
                                    'class' => 'form-select prompt-select',
                                    'id' => 'signup-country-code'
                                ]
                            )->label('Country Code') ?>
                        </div>
                        <div class="col-md-8 mb-3">
                            <?= $form->field($model, 'phone_num')->textInput([
                                'placeholder' => 'Phone number',
                                'class' => 'form-control',
                                'id' => 'signup-phone-num',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57'
                            ])->label('Phone Number') ?>
                            <small class="text-muted" id="signup-phone-limit-text">Please select a country code first</small>
                        </div>
                    </div>

                    <hr>
                    <!-- ADDRESS INFORMATION -->
                    <h5 class="text-primary mb-3"><i class="fas fa-map-marker-alt"></i> Address Information</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address Line 1 *</label>
                            <input type="text" id="address-line1" class="form-control" 
                                   placeholder="House/Unit No., Street name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" id="address-line2" class="form-control" 
                                   placeholder="Barangay, Subdivision (optional)">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City / Town *</label>
                            <input type="text" id="address-city" class="form-control" 
                                   placeholder="City or Town">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State / Province *</label>
                            <input type="text" id="address-state" class="form-control" 
                                   placeholder="State or Province">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Postal / ZIP Code *</label>
                            <input type="text" id="address-zip" class="form-control" 
                                   placeholder="Postal code">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country *</label>
                            <select id="address-country" class="form-select prompt-select">
                                <option value="">-- Select Country --</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= $country ?>"><?= $country ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?= $form->field($model, 'address')->hiddenInput(['id' => 'signup-address'])->label(false) ?>

                    <div class="alert alert-info mt-3" id="address-preview" style="display:none;">
                        <strong><i class="fas fa-eye"></i> Address Preview:</strong><br>
                        <span id="address-preview-text"></span>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> 
                        By creating an account, your patient profile will be automatically set up. 
                        You can book appointments immediately after registration.
                    </div>

                    <div class="form-group mt-4">
                        <?= Html::submitButton('<i class="fas fa-user-plus"></i> Create Account &amp; Patient Profile', [
                            'class' => 'btn btn-primary btn-lg w-100',
                            'name' => 'signup-button',
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="text-center mt-3">
                        Already have an account? <?= Html::a('Login here', ['site/login']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var phoneLimits = " . json_encode($phoneLimits) . ";
    
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
    // PHONE NUMBER FUNCTIONS
    // ==========================================
    $('#signup-country-code').on('change', function() {
        var countryCode = $(this).val();
        var phoneInput = $('#signup-phone-num');
        var limitText = $('#signup-phone-limit-text');
        
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
    
    $('#signup-phone-num').on('input', function() {
        var countryCode = $('#signup-country-code').val();
        var phoneNum = $(this).val().replace(/[^0-9]/g, '');
        var limitText = $('#signup-phone-limit-text');
        
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
        $('#signup-phone-num').trigger('input');
    }
    
    // ==========================================
    // EMAIL VALIDATION
    // ==========================================
    $('#signup-email').on('change blur input', function() {
        var email = $(this).val().trim();
        if (email && /^[a-zA-Z0-9._%+-]+@gmail\\.com$/.test(email)) {
            $(this).css('border-color', '#28a745');
            $('#email-feedback').html('<small class=\"text-success\">✓ Valid Gmail address</small>');
        } else if (email) {
            $(this).css('border-color', '#dc3545');
            $('#email-feedback').html('<small class=\"text-danger\">✗ Must be a valid Gmail address (example@gmail.com)</small>');
        } else {
            $(this).css('border-color', '#ced4da');
            $('#email-feedback').html('');
        }
    });
    
    // ==========================================
    // ADDRESS COMBINATION
    // ==========================================
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
        $('#signup-address').val(fullAddress);
        
        if (fullAddress) {
            $('#address-preview').show();
            $('#address-preview-text').text(fullAddress);
        } else {
            $('#address-preview').hide();
        }
    });
    
    // ==========================================
    // AGE AUTO-CALCULATION
    // ==========================================
    $('#signup-dob').on('change', function() {
        var dob = $(this).val();
        if (dob) {
            var today = new Date();
            var birthDate = new Date(dob);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
            
            if (age >= 0) {
                $('#signup-age-display').val(age + ' years old');
                
            } else {
                $('#signup-age-display').val('Invalid date');
                ;
            }
        } else {
            $('#signup-age-display').val('');
            
        }
    });
    
    // ==========================================
    // FORM SUBMISSION
    // ==========================================
    $('#signup-form').on('beforeSubmit', function() {
        // Combine address before submit
        $('#address-line1').trigger('change');
        
        var address = $('#signup-address').val();
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
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
    });
");
?>