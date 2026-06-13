<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblDepartment $model */
/** @var yii\widgets\ActiveForm $form */

// Common hospital department names for autocomplete suggestions
$departmentSuggestions = [
    'Anesthesiology',
    'Burn Center',
    'Cancer Center',
    'Cardiac Surgery',
    'Cardiology',
    'Cardiovascular Intensive Care Unit (CVICU)',
    'Cardiovascular Surgery',
    'Chest Medicine',
    'Coronary Care Unit (CCU)',
    'Critical Care Unit (CCU)',
    'Day Surgery Unit',
    'Dental Surgery',
    'Dentistry',
    'Dermatology',
    'Dermatology Surgery',
    'Dialysis Center',
    'Dietetics and Nutrition',
    'Ear, Nose, and Throat (ENT)',
    'Emergency Department (ER)',
    'Endocrinology',
    'Endoscopy Unit',
    'Family Medicine',
    'Gastroenterology',
    'General Medicine',
    'General Surgery',
    'Genetics',
    'Geriatrics',
    'Gynecology',
    'Hematology',
    'Hepatology',
    'Immunology',
    'Infectious Disease',
    'Intensive Care Unit (ICU)',
    'Internal Medicine',
    'Labor and Delivery',
    'Laboratory Services',
    'Maternity Ward',
    'Medical Oncology',
    'Microbiology',
    'Neonatal Intensive Care Unit (NICU)',
    'Nephrology',
    'Neurology',
    'Neurosurgery',
    'Neurosurgical Intensive Care Unit (NSICU)',
    'Nuclear Medicine',
    'Nutrition Services',
    'Obstetrics',
    'Obstetrics and Gynecology (OB-GYN)',
    'Occupational Therapy',
    'Oncology',
    'Ophthalmology',
    'Oral and Maxillofacial Surgery',
    'Orthopedic Surgery',
    'Orthopedics',
    'Otolaryngology',
    'Outpatient Clinic',
    'Pain Management',
    'Palliative Care',
    'Pathology',
    'Pediatric Cardiology',
    'Pediatric Intensive Care Unit (PICU)',
    'Pediatric Neurology',
    'Pediatric Oncology',
    'Pediatric Surgery',
    'Pediatrics',
    'Pharmacy',
    'Physical Medicine and Rehabilitation',
    'Physical Therapy',
    'Plastic Surgery',
    'Podiatry',
    'Preventive Medicine',
    'Psychiatry',
    'Psychology',
    'Pulmonology',
    'Radiology',
    'Radiology - CT Scan',
    'Radiology - MRI',
    'Radiology - Ultrasound',
    'Radiology - X-Ray',
    'Radiotherapy',
    'Renal Unit',
    'Respiratory Therapy',
    'Rheumatology',
    'Sleep Disorders Center',
    'Speech Therapy',
    'Spine Center',
    'Sports Medicine',
    'Surgical Intensive Care Unit (SICU)',
    'Thoracic Surgery',
    'Transplant Center',
    'Trauma Center',
    'Urology',
    'Vascular Surgery',
    'Wellness Center',
    'Women\'s Health Center',
    'Wound Care Center',
];
?>

<div class="tbl-department-form">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-building"></i> Department Information</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'department-form']]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'dept_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Type or select department name...',
                        'id' => 'dept-name',
                        'list' => 'department-list',
                        'autocomplete' => 'off'
                    ])->label('Department Name *') ?>
                    
                    <datalist id="department-list">
                        <?php foreach ($departmentSuggestions as $dept): ?>
                            <option value="<?= Html::encode($dept) ?>"><?= Html::encode($dept) ?></option>
                        <?php endforeach; ?>
                    </datalist>
                    
                    <small class="text-muted">Start typing to see department suggestions</small>
                </div>
            </div>

            <hr>
            <h5 class="text-info mb-3"><i class="fas fa-calendar-alt"></i> Operating Days</h5>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="control-label">Operating Days *</label>
                    <select id="preset-days" class="form-control prompt-select">
                        <option value="">-- Select Operating Days --</option>
                        <option value="weekdays">Weekdays (Monday - Friday)</option>
                        <option value="weekends">Weekends (Saturday - Sunday)</option>
                        <option value="all-week">All Week (Monday - Sunday)</option>
                        <option value="mwf">MWF (Monday, Wednesday, Friday)</option>
                        <option value="tths">TTHS (Tuesday, Thursday, Saturday)</option>
                        <option value="custom">Custom Selection</option>
                    </select>
                </div>
            </div>

            <!-- Custom days selection -->
            <div id="custom-days-section" style="display:none;">
                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label mb-2 font-weight-bold text-primary">
                            <i class="fas fa-hand-pointer"></i> Select Individual Days
                        </label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-mon" value="Monday">
                                    <label class="custom-control-label" for="day-mon">Monday</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-tue" value="Tuesday">
                                    <label class="custom-control-label" for="day-tue">Tuesday</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-wed" value="Wednesday">
                                    <label class="custom-control-label" for="day-wed">Wednesday</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-thu" value="Thursday">
                                    <label class="custom-control-label" for="day-thu">Thursday</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-fri" value="Friday">
                                    <label class="custom-control-label" for="day-fri">Friday</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-sat" value="Saturday">
                                    <label class="custom-control-label" for="day-sat">Saturday</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input day-checkbox" id="day-sun" value="Sunday">
                                    <label class="custom-control-label" for="day-sun">Sunday</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden field for operating days -->
            <?= $form->field($model, 'operating_days')->hiddenInput([
                'id' => 'operating-days-hidden',
                'maxlength' => true
            ])->label(false) ?>

            <div class="alert alert-info mt-3" id="operating-days-preview" style="display:none;">
                <strong><i class="fas fa-check-circle"></i> Selected Operating Days:</strong>
                <span id="operating-days-text"></span>
            </div>

            <hr>
            <h5 class="text-info mb-3"><i class="fas fa-clock"></i> Office Hours</h5>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Opening Time *</label>
                        <input type="time" id="start-time-input" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Closing Time *</label>
                        <input type="time" id="end-time-input" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Office Hours</label>
                        <div class="input-group">
                            <input type="text" id="office-hours-display" class="form-control" readonly 
                                   placeholder="8:00 AM - 5:00 PM">
                            <div class="input-group-append">
                                <span class="input-group-text" id="office-hours-icon">
                                    <i class="fas fa-clock"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?= $form->field($model, 'office_hours')->hiddenInput([
                'id' => 'office-hours-hidden',
                'maxlength' => true
            ])->label(false) ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Department', [
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
    // REAL-TIME OFFICE HOURS UPDATE
    // ==========================================
    $('#start-time-input, #end-time-input').on('change input', function() {
        updateOfficeHours();
    });
    
    function updateOfficeHours() {
        var startTime = $('#start-time-input').val();
        var endTime = $('#end-time-input').val();
        var displayField = $('#office-hours-display');
        var hiddenField = $('#office-hours-hidden');
        var iconField = $('#office-hours-icon');
        
        if (startTime && endTime) {
            var startFormatted = formatTime(startTime);
            var endFormatted = formatTime(endTime);
            var officeHours = startFormatted + ' - ' + endFormatted;
            
            displayField.val(officeHours);
            hiddenField.val(officeHours);
            displayField.css('background-color', '#d4edda');
            displayField.css('color', '#155724');
            iconField.html('<i class=\"fas fa-check-circle text-success\"></i>');
        } else if (startTime && !endTime) {
            var startFormatted = formatTime(startTime);
            displayField.val(startFormatted + ' - ?:?? ??');
            hiddenField.val('');
            displayField.css('background-color', '#fff3cd');
            displayField.css('color', '#856404');
            iconField.html('<i class=\"fas fa-exclamation-triangle text-warning\"></i>');
        } else if (!startTime && endTime) {
            var endFormatted = formatTime(endTime);
            displayField.val('?:?? ?? - ' + endFormatted);
            hiddenField.val('');
            displayField.css('background-color', '#fff3cd');
            displayField.css('color', '#856404');
            iconField.html('<i class=\"fas fa-exclamation-triangle text-warning\"></i>');
        } else {
            displayField.val('');
            hiddenField.val('');
            displayField.css('background-color', '#ffffff');
            displayField.css('color', '#495057');
            iconField.html('<i class=\"fas fa-clock\"></i>');
        }
    }
    
    function formatTime(time) {
        if (!time) return '';
        var timeParts = time.split(':');
        var hours = parseInt(timeParts[0]);
        var minutes = timeParts[1];
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        return hours + ':' + minutes + ' ' + ampm;
    }
    
    // ==========================================
    // OPERATING DAYS FUNCTIONS
    // ==========================================
    $('#preset-days').on('change', function() {
        var preset = $(this).val();
        var customSection = $('#custom-days-section');
        
        // Uncheck all checkboxes first
        $('.day-checkbox').prop('checked', false);
        
        // Always hide custom section first
        customSection.hide();
        
        switch(preset) {
            case 'weekdays':
                $('#day-mon, #day-tue, #day-wed, #day-thu, #day-fri').prop('checked', true);
                break;
            case 'weekends':
                $('#day-sat, #day-sun').prop('checked', true);
                break;
            case 'all-week':
                $('.day-checkbox').prop('checked', true);
                break;
            case 'mwf':
                $('#day-mon, #day-wed, #day-fri').prop('checked', true);
                break;
            case 'tths':
                $('#day-tue, #day-thu, #day-sat').prop('checked', true);
                break;
            case 'custom':
                // Show custom days section
                customSection.slideDown(300);
                break;
            default:
                break;
        }
        
        updateOperatingDays();
        disablePromptOption(this);
    });
    
    // When individual checkboxes change
    $('.day-checkbox').on('change', function() {
        updateOperatingDays();
    });
    
    function updateOperatingDays() {
        var selectedDays = [];
        
        $('.day-checkbox:checked').each(function() {
            selectedDays.push($(this).val());
        });
        
        var operatingDays = selectedDays.join(', ');
        $('#operating-days-hidden').val(operatingDays);
        
        if (operatingDays) {
            $('#operating-days-preview').slideDown(200);
            $('#operating-days-text').text(operatingDays);
        } else {
            $('#operating-days-preview').slideUp(200);
        }
    }
    
    // ==========================================
    // PARSE EXISTING OFFICE HOURS (for edit form)
    // ==========================================
    function parseExistingOfficeHours(officeHours) {
        if (!officeHours) return;
        
        var parts = officeHours.split(' - ');
        if (parts.length === 2) {
            var startTime = parseTimeString(parts[0].trim());
            var endTime = parseTimeString(parts[1].trim());
            
            if (startTime) {
                $('#start-time-input').val(startTime);
            }
            if (endTime) {
                $('#end-time-input').val(endTime);
            }
        }
    }
    
    function parseTimeString(timeStr) {
        var match = timeStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
        if (match) {
            var hours = parseInt(match[1]);
            var minutes = match[2];
            var ampm = match[3].toUpperCase();
            
            if (ampm === 'PM' && hours < 12) hours += 12;
            if (ampm === 'AM' && hours === 12) hours = 0;
            
            return String(hours).padStart(2, '0') + ':' + minutes;
        }
        return null;
    }
    
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
    
    // ==========================================
    // FORM SUBMISSION
    // ==========================================
    $('#department-form').on('beforeSubmit', function() {
        updateOperatingDays();
        updateOfficeHours();
        
        var operatingDays = $('#operating-days-hidden').val();
        if (!operatingDays) {
            alert('Please select operating days.');
            return false;
        }
        
        var officeHours = $('#office-hours-hidden').val();
        if (!officeHours) {
            alert('Please set both opening and closing time.');
            return false;
        }
        
        return true;
    });
    
    // ==========================================
    // RESET FORM
    // ==========================================
    function resetForm() {
        $('#operating-days-preview').slideUp(200);
        $('#custom-days-section').slideUp(200);
        $('#preset-days').val('');
        $('#office-hours-display').val('').css('background-color', '#ffffff').css('color', '#495057');
        $('#office-hours-hidden').val('');
        $('#start-time-input').val('');
        $('#end-time-input').val('');
        $('#office-hours-icon').html('<i class=\"fas fa-clock\"></i>');
        
        setTimeout(function() {
            $('.day-checkbox').prop('checked', false);
            $('.prompt-select').each(function() {
                $(this).find('option[value=\"\"]').prop('disabled', false);
            });
        }, 100);
    }
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    $(document).ready(function() {
        // Parse existing operating days for update form
        var existingDays = $('#operating-days-hidden').val();
        if (existingDays) {
            var daysArray = existingDays.split(', ');
            
            // Check all matching checkboxes
            daysArray.forEach(function(day) {
                $('.day-checkbox[value=\"' + day.trim() + '\"]').prop('checked', true);
            });
            
            // Determine which preset matches
            var allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            var weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
            var weekends = ['Saturday', 'Sunday'];
            var mwf = ['Monday', 'Wednesday', 'Friday'];
            var tths = ['Tuesday', 'Thursday', 'Saturday'];
            
            var checkedDays = [];
            $('.day-checkbox:checked').each(function() {
                checkedDays.push($(this).val());
            });
            
            if (arraysEqual(checkedDays.sort(), allDays.sort())) {
                $('#preset-days').val('all-week');
            } else if (arraysEqual(checkedDays.sort(), weekdays.sort())) {
                $('#preset-days').val('weekdays');
            } else if (arraysEqual(checkedDays.sort(), weekends.sort())) {
                $('#preset-days').val('weekends');
            } else if (arraysEqual(checkedDays.sort(), mwf.sort())) {
                $('#preset-days').val('mwf');
            } else if (arraysEqual(checkedDays.sort(), tths.sort())) {
                $('#preset-days').val('tths');
            } else if (checkedDays.length > 0) {
                $('#preset-days').val('custom');
                // Show custom section for existing custom days
                $('#custom-days-section').show();
            }
            
            updateOperatingDays();
            disablePromptOption($('#preset-days')[0]);
        }
        
        // Parse existing office hours
        var existingOfficeHours = $('#office-hours-hidden').val();
        if (existingOfficeHours) {
            parseExistingOfficeHours(existingOfficeHours);
            // Update display
            $('#office-hours-display').val(existingOfficeHours);
            $('#office-hours-display').css('background-color', '#d4edda').css('color', '#155724');
            $('#office-hours-icon').html('<i class=\"fas fa-check-circle text-success\"></i>');
        }
        
        // Initialize dropdown prompts
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
    });
    
    // Helper function to compare arrays
    function arraysEqual(a, b) {
        if (a.length !== b.length) return false;
        for (var i = 0; i < a.length; i++) {
            if (a[i] !== b[i]) return false;
        }
        return true;
    }
");
?>

<?php
$this->registerCss("
    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    
    .custom-control-label {
        cursor: pointer;
        padding-left: 5px;
        user-select: none;
    }
    
    .custom-control {
        margin-bottom: 10px;
    }
    
    .day-checkbox:checked + .custom-control-label {
        color: #17a2b8;
        font-weight: bold;
    }
    
    #dept-name {
        font-size: 16px;
    }
    
    datalist option {
        font-size: 14px;
        padding: 5px;
    }
    
    #office-hours-display {
        font-weight: bold;
        font-size: 16px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    #custom-days-section {
        border: 2px dashed #17a2b8;
        padding: 20px;
        border-radius: 8px;
        background-color: #f0f9ff;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    #operating-days-preview {
        transition: all 0.3s ease;
    }
    
    #office-hours-display:focus {
        outline: none;
    }
");
?>