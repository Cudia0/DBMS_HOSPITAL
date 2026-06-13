<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDoctor;
use common\models\TblDepartment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblScheduleSlot $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-schedule-slot-form">

    <?php $form = ActiveForm::begin(['id' => 'schedule-slot-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'dr_id')->dropDownList(
                ArrayHelper::map(
                    TblDoctor::find()
                        ->with('department')
                        ->orderBy(['dr_id' => SORT_ASC])
                        ->all(), 
                    'dr_id', 
                    function($doctor) { 
                        $dept = $doctor->department ? $doctor->department->dept_name : 'No Dept';
                        $deptHours = $doctor->department ? 
                            ' [' . ($doctor->department->start_time ?? '?') . ' - ' . ($doctor->department->end_time ?? '?') . ']' : '';
                        
                        return $doctor->dr_id . ' - Dr. ' . 
                               $doctor->first_name . ' ' . $doctor->last_name . 
                               ' (' . ($doctor->specialization ?? 'General') . ')' .
                               ' - ' . $dept . $deptHours;
                    }
                ),
                [
                    'prompt' => 'Select Doctor',
                    'id' => 'schedule-dr-id',
                    'onchange' => 'loadDepartmentSchedule()'
                ]
            ) ?>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Department Schedule Info</label>
                <input type="text" class="form-control" id="dept-schedule-info" 
                       value="Select doctor to view department schedule" readonly>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'slot_date')->input('date', [
                'min' => date('Y-m-d'),
                'id' => 'slot-date'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'start_time')->input('time', [
                'id' => 'slot-start-time',
                'onchange' => 'validateTimeRange()'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'end_time')->input('time', [
                'id' => 'slot-end-time',
                'onchange' => 'validateTimeRange()'
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'is_available')->checkbox([
        'label' => 'Mark this slot as available for booking',
        'checked' => true,
        'id' => 'slot-available'
    ]) ?>

    <div class="alert alert-info" id="slot-validation-message" style="display: none;">
        <i class="fa fa-info-circle"></i> <span id="validation-text"></span>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-clock"></i> Save Schedule Slot', [
            'class' => 'btn btn-success btn-lg'
        ]) ?>
        <?= Html::button('<i class="fa fa-list"></i> Generate Weekly Slots', [
            'class' => 'btn btn-primary',
            'id' => 'generate-weekly-slots',
            'onclick' => 'showBulkGenerator()'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Bulk Slot Generator Modal -->
<div class="modal fade" id="bulk-slot-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generate Weekly Schedule Slots</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Doctor</label>
                    <select class="form-control" id="bulk-dr-id">
                        <option value="">Select Doctor</option>
                        <?php
                        $doctors = TblDoctor::find()->with('department')->all();
                        foreach ($doctors as $doctor) {
                            echo '<option value="' . $doctor->dr_id . '">' . 
                                 $doctor->dr_id . ' - Dr. ' . $doctor->first_name . ' ' . $doctor->last_name . 
                                 ' (' . ($doctor->specialization ?? 'General') . ')</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-control" id="bulk-start-date" 
                           min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>Number of Weeks</label>
                    <input type="number" class="form-control" id="bulk-weeks" 
                           min="1" max="12" value="4">
                </div>
                <div class="form-group">
                    <label>Slot Duration (minutes)</label>
                    <select class="form-control" id="bulk-duration">
                        <option value="30">30 minutes</option>
                        <option value="60" selected>1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                <div id="bulk-preview"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateBulkSlots()">
                    Generate Slots
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Get doctor and department data for JavaScript
$doctorsData = TblDoctor::find()
    ->with('department')
    ->select(['dr_id', 'first_name', 'last_name', 'specialization', 'dept_id'])
    ->asArray()
    ->all();

$doctorScheduleMap = [];
foreach ($doctorsData as $doc) {
    $dept = TblDepartment::find()
        ->select(['dept_id', 'dept_name', 'start_time', 'end_time', 'operating_days'])
        ->where(['dept_id' => $doc['dept_id']])
        ->asArray()
        ->one();
    
    $doctorScheduleMap[$doc['dr_id']] = [
        'name' => 'Dr. ' . $doc['first_name'] . ' ' . $doc['last_name'],
        'dept_name' => $dept ? $dept['dept_name'] : 'No Department',
        'start_time' => $dept ? $dept['start_time'] : '08:00:00',
        'end_time' => $dept ? $dept['end_time'] : '17:00:00',
        'operating_days' => $dept ? $dept['operating_days'] : 'Mon-Fri'
    ];
}

$doctorScheduleJson = json_encode($doctorScheduleMap);

// Get existing slots for conflict checking
$existingSlots = TblScheduleSlot::find()
    ->select(['dr_id', 'slot_date', 'start_time', 'end_time'])
    ->where(['>=', 'slot_date', date('Y-m-d')])
    ->asArray()
    ->all();
$existingSlotsJson = json_encode($existingSlots);

$this->registerJs("
    var doctorScheduleMap = {$doctorScheduleJson};
    var existingSlots = {$existingSlotsJson};
    
    function loadDepartmentSchedule() {
        var drId = $('#schedule-dr-id').val();
        if (drId && doctorScheduleMap[drId]) {
            var schedule = doctorScheduleMap[drId];
            $('#dept-schedule-info').val(
                schedule.dept_name + ' | Hours: ' + 
                schedule.start_time.substring(0, 5) + ' - ' + 
                schedule.end_time.substring(0, 5) + 
                ' | Days: ' + schedule.operating_days
            );
            // Auto-set time range based on department
            $('#slot-start-time').val(schedule.start_time.substring(0, 5));
            $('#slot-end-time').val(schedule.end_time.substring(0, 5));
        } else {
            $('#dept-schedule-info').val('Select doctor to view department schedule');
        }
        validateTimeRange();
    }
    
    function validateTimeRange() {
        var startTime = $('#slot-start-time').val();
        var endTime = $('#slot-end-time').val();
        var slotDate = $('#slot-date').val();
        var drId = $('#schedule-dr-id').val();
        
        if (startTime && endTime && drId && doctorScheduleMap[drId]) {
            var schedule = doctorScheduleMap[drId];
            var deptStart = schedule.start_time.substring(0, 5);
            var deptEnd = schedule.end_time.substring(0, 5);
            
            var message = '';
            var hasError = false;
            
            if (startTime < deptStart || startTime > deptEnd) {
                message += 'Start time is outside department hours (' + deptStart + ' - ' + deptEnd + '). ';
                hasError = true;
            }
            
            if (endTime < deptStart || endTime > deptEnd || endTime <= startTime) {
                message += 'End time must be within department hours and after start time. ';
                hasError = true;
            }
            
            // Check for conflicts
            var conflict = false;
            for (var i = 0; i < existingSlots.length; i++) {
                var slot = existingSlots[i];
                if (slot.dr_id == drId && slot.slot_date == slotDate && slot.is_available == 1) {
                    if ((startTime >= slot.start_time && startTime < slot.end_time) ||
                        (endTime > slot.start_time && endTime <= slot.end_time) ||
                        (startTime <= slot.start_time && endTime >= slot.end_time)) {
                        conflict = true;
                        break;
                    }
                }
            }
            
            if (conflict) {
                message += 'Warning: This time slot overlaps with an existing slot! ';
                hasError = true;
            }
            
            if (message) {
                $('#slot-validation-message').show();
                $('#validation-text').text(message);
                $('#slot-validation-message')
                    .removeClass('alert-info alert-success')
                    .addClass(hasError ? 'alert-warning' : 'alert-success');
            } else {
                $('#slot-validation-message').show();
                $('#validation-text').text('Time slot is valid and available.');
                $('#slot-validation-message')
                    .removeClass('alert-warning alert-info')
                    .addClass('alert-success');
            }
        }
    }
    
    function showBulkGenerator() {
        $('#bulk-slot-modal').modal('show');
    }
    
    function generateBulkSlots() {
        var drId = $('#bulk-dr-id').val();
        var startDate = $('#bulk-start-date').val();
        var weeks = parseInt($('#bulk-weeks').val()) || 4;
        var duration = parseInt($('#bulk-duration').val()) || 60;
        
        if (!drId || !startDate) {
            alert('Please select doctor and start date');
            return;
        }
        
        if (!doctorScheduleMap[drId]) {
            alert('Doctor schedule not found');
            return;
        }
        
        var schedule = doctorScheduleMap[drId];
        var deptStart = schedule.start_time.substring(0, 5);
        var deptEnd = schedule.end_time.substring(0, 5);
        
        // Parse operating days
        var operatingDays = schedule.operating_days.toLowerCase();
        var daysOfWeek = [];
        if (operatingDays.includes('mon')) daysOfWeek.push(1);
        if (operatingDays.includes('tue')) daysOfWeek.push(2);
        if (operatingDays.includes('wed')) daysOfWeek.push(3);
        if (operatingDays.includes('thu')) daysOfWeek.push(4);
        if (operatingDays.includes('fri')) daysOfWeek.push(5);
        if (operatingDays.includes('sat')) daysOfWeek.push(6);
        if (operatingDays.includes('sun')) daysOfWeek.push(0);
        
        if (daysOfWeek.length === 0) daysOfWeek = [1, 2, 3, 4, 5]; // Default Mon-Fri
        
        // Calculate slots
        var startDateTime = new Date(startDate + 'T' + deptStart);
        var endDateTime = new Date(startDate + 'T' + deptEnd);
        var totalDays = weeks * 7;
        var slots = [];
        
        for (var d = 0; d < totalDays; d++) {
            var currentDate = new Date(startDateTime);
            currentDate.setDate(currentDate.getDate() + d);
            
            if (daysOfWeek.includes(currentDate.getDay())) {
                var slotStart = new Date(currentDate.toDateString() + ' ' + deptStart);
                var slotEnd = new Date(slotStart.getTime() + duration * 60000);
                var dayEnd = new Date(currentDate.toDateString() + ' ' + deptEnd);
                
                while (slotEnd <= dayEnd) {
                    slots.push({
                        date: currentDate.toISOString().split('T')[0],
                        start: slotStart.toTimeString().substring(0, 5),
                        end: slotEnd.toTimeString().substring(0, 5)
                    });
                    slotStart = new Date(slotEnd);
                    slotEnd = new Date(slotStart.getTime() + duration * 60000);
                }
            }
        }
        
        var preview = '<h5>Preview (' + slots.length + ' slots to be generated):</h5>';
        preview += '<div style=\"max-height: 200px; overflow-y: auto;\"><table class=\"table table-bordered table-sm\">';
        preview += '<thead><tr><th>Date</th><th>Time</th></tr></thead><tbody>';
        
        var previewSlots = slots.slice(0, 10);
        for (var i = 0; i < previewSlots.length; i++) {
            preview += '<tr><td>' + previewSlots[i].date + '</td><td>' + 
                      previewSlots[i].start + ' - ' + previewSlots[i].end + '</td></tr>';
        }
        
        if (slots.length > 10) {
            preview += '<tr><td colspan=\"2\" class=\"text-center\">... and ' + 
                      (slots.length - 10) + ' more slots</td></tr>';
        }
        
        preview += '</tbody></table></div>';
        preview += '<button class=\"btn btn-success\" onclick=\"saveBulkSlots(' + 
                   JSON.stringify(slots).replace(/\"/g, '&quot;') + 
                   ', ' + drId + ')\">Confirm & Save All Slots</button>';
        
        $('#bulk-preview').html(preview);
    }
    
    function saveBulkSlots(slotsData, drId) {
        // This would typically be an AJAX call to save all slots
        // For now, we'll show the count
        if (confirm('Generate ' + slotsData.length + ' schedule slots for this doctor?')) {
            // AJAX call to backend
            $.ajax({
                url: '" . \yii\helpers\Url::to(['schedule-slot/bulk-create']) . "',
                type: 'POST',
                data: {
                    dr_id: drId,
                    slots: slotsData,
                    '_csrf': yii.getCsrfToken()
                },
                success: function(response) {
                    if (response.success) {
                        alert('Successfully generated ' + response.count + ' schedule slots!');
                        $('#bulk-slot-modal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error generating slots. Please try again.');
                }
            });
        }
    }
    
    // Initialize on page load
    $(document).ready(function() {
        loadDepartmentSchedule();
        
        $('#slot-date, #slot-start-time, #slot-end-time').on('change', function() {
            validateTimeRange();
        });
        
        // Auto-calculate end time based on duration
        $('#slot-start-time').on('change', function() {
            var startTime = $(this).val();
            if (startTime) {
                var startParts = startTime.split(':');
                var startDate = new Date();
                startDate.setHours(startParts[0], startParts[1], 0);
                startDate.setMinutes(startDate.getMinutes() + 60); // Default 1 hour
                var endTime = startDate.toTimeString().substring(0, 5);
                $('#slot-end-time').val(endTime);
                validateTimeRange();
            }
        });
    });
");
?>