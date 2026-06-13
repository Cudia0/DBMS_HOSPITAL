<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDirector;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblReceptionist $model */
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

// Phone number limits
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
?>

<div class="tbl-receptionist-form">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-user-tie"></i> Receptionist Information</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'receptionist-form']]); ?>

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
            <h5 class="text-primary mb-3"><i class="fas fa-phone"></i> Contact Information</h5>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'country_code')->dropDownList(
                        $countryCodes,
                        [
                            'prompt' => '-- Select Country Code --',
                            'id' => 'recep-country_code',
                            'class' => 'form-control prompt-select'
                        ]
                    )->label('Country Code *') ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'phone_num')->textInput([
                        'placeholder' => 'Phone number',
                        'id' => 'recep-phone_num',
                        'onkeypress' => 'return isNumber(event)'
                    ])->label('Phone Number *') ?>
                    <small class="text-muted" id="phone-limit-text">Please select a country code first</small>
                </div>
            </div>

            <?= $form->field($model, 'email')->textInput([
                'type' => 'email',
                'placeholder' => 'example@gmail.com or N/A',
                'id' => 'recep-email'
            ])->label('Email (Gmail only or N/A)') ?>
            <small class="text-muted">Use Gmail address (example@gmail.com) or type "N/A" if no email available</small>
            <div id="email-feedback" class="mt-1"></div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-sitemap"></i> Assignment</h5>

            <?= $form->field($model, 'director_id')->dropDownList(
                ArrayHelper::map(
                    TblDirector::find()->orderBy(['director_id' => SORT_ASC])->all(), 
                    'director_id', 
                    function($model) { 
                        return $model->director_id . ' - ' . $model->first_name . ' ' . $model->last_name; 
                    }
                ),
                [
                    'prompt' => '-- Select Supervising Director --',
                    'class' => 'form-control prompt-select'
                ]
            )->label('Supervising Director') ?>
            <small class="text-muted">Select the director who will supervise this receptionist</small>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Receptionist', [
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
    $('#recep-country_code').on('change', function() {
        var countryCode = $(this).val();
        var phoneInput = $('#recep-phone_num');
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
    
    $('#recep-phone_num').on('input', function() {
        var countryCode = $('#recep-country_code').val();
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
        $('#recep-phone_num').trigger('input');
    }
    
    // ==========================================
    // EMAIL VALIDATION
    // ==========================================
    $('#recep-email').on('change blur input', function() {
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
    // FORM SUBMISSION
    // ==========================================
    $('#receptionist-form').on('beforeSubmit', function() {
        var email = $('#recep-email').val().trim();
        if (email && email !== 'N/A' && email !== 'n/a') {
            var gmailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
            if (!gmailRegex.test(email)) {
                alert('Please use a valid Gmail address or type N/A');
                return false;
            }
        }
        return true;
    });
    
    // ==========================================
    // RESET FORM
    // ==========================================
    function resetForm() {
        $('#phone-limit-text').text('Please select a country code first').removeClass('text-danger text-success').addClass('text-muted');
        $('#recep-email').css('border-color', '#ced4da');
        $('#email-feedback').html('');
        
        setTimeout(function() {
            $('.prompt-select').each(function() {
                $(this).find('option[value=\"\"]').prop('disabled', false);
            });
        }, 100);
    }
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    $(document).ready(function() {
        var initialCountryCode = $('#recep-country_code').val();
        if (initialCountryCode) {
            $('#recep-country_code').trigger('change');
        }
        
        var initialEmail = $('#recep-email').val();
        if (initialEmail) {
            $('#recep-email').trigger('change');
        }
        
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
    });
");
?>