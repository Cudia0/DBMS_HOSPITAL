<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblLabTest $model */
/** @var yii\widgets\ActiveForm $form */

$user = Yii::$app->user->identity;
$isDoctor = $user && $user->isDoctor();
$doctorId = $isDoctor ? $user->doctor_id : null;

$appointmentQuery = TblAppointment::find()
    ->where(['status' => ['checked_in', 'in_progress']])
    ->orderBy(['appointment_date' => SORT_DESC, 'appointment_time' => SORT_DESC]);

if ($isDoctor) {
    $appointmentQuery->andWhere(['dr_id' => $doctorId]);
}

$testNameSuggestions = [
    'Complete Blood Count (CBC)',
    'Lipid Profile',
    'Fasting Blood Sugar (FBS)',
    'HbA1c',
    'Liver Function Test (LFT)',
    'Kidney Function Test (KFT)',
    'Thyroid Function Test (TFT)',
    'Urinalysis',
    'Chest X-Ray',
    'Electrocardiogram (ECG)',
    'Echocardiogram',
    'Ultrasound',
    'CT Scan',
    'MRI',
    'Prothrombin Time (PT)',
    'Partial Thromboplastin Time (PTT)',
    'Blood Urea Nitrogen (BUN)',
    'Serum Creatinine',
    'Uric Acid',
    'Electrolyte Panel',
    'C-Reactive Protein (CRP)',
    'Erythrocyte Sedimentation Rate (ESR)',
    'Rapid Antigen Test',
    'PCR Test',
    'Stool Exam',
    'Sputum Culture',
    'Blood Culture',
    'Drug Test',
    'Pregnancy Test',
    'PSA Test',
];
?>

<div class="tbl-lab-test-form">
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="fas fa-flask"></i> 
                <?= $model->isNewRecord ? 'Order Lab Test' : 'Update Lab Test #' . $model->test_id ?>
            </h4>
        </div>
        <div class="card-body">
            
            <?php if ($model->isNewRecord): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Lab tests are <strong>optional diagnostic tests</strong> ordered to help diagnose the patient's condition.
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'lab-test-form']]); ?>

            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'appt_id')->dropDownList(
                        ArrayHelper::map(
                            $appointmentQuery->all(),
                            'appt_id',
                            function($model) {
                                $patientName = $model->patient ? $model->patient->getFullName() : 'N/A';
                                $doctorName = $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                                return '#' . $model->appt_id . ' | ' . $patientName . ' | ' . $doctorName;
                            }
                        ),
                        [
                            'prompt' => '-- Select Appointment --',
                            'class' => 'form-control prompt-select',
                            'required' => true
                        ]
                    )->label('Appointment *') ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-vial"></i> Test Details</h5>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'test_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Type or select test name...',
                        'list' => 'test-name-list',
                        'autocomplete' => 'off',
                        'required' => true
                    ])->label('Test Name *') ?>
                    
                    <datalist id="test-name-list">
                        <?php foreach ($testNameSuggestions as $name): ?>
                            <option value="<?= Html::encode($name) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <small class="text-muted">Start typing to see common lab test suggestions</small>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'test_category')->dropDownList([ 
                        'hematology' => 'Hematology (Blood)',
                        'chemistry' => 'Chemistry (Blood Chem)',
                        'microbiology' => 'Microbiology',
                        'immunology' => 'Immunology/Serology',
                        'radiology' => 'Radiology (X-Ray, CT, MRI)',
                        'cardiology' => 'Cardiology (ECG, Echo)',
                        'endocrinology' => 'Endocrinology (Hormones)',
                        'urinalysis' => 'Urinalysis',
                        'pathology' => 'Pathology',
                        'other' => 'Other',
                    ], [
                        'prompt' => '-- Select Category --',
                        'class' => 'form-control prompt-select'
                    ])->label('Test Category') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList([ 
                        'ordered' => 'Ordered (Doctor requested)',
                        'collected' => 'Collected (Sample taken)',
                        'processing' => 'Processing (Lab analyzing)',
                        'completed' => 'Completed (Results available)',
                        'cancelled' => 'Cancelled',
                    ], [
                        'class' => 'form-control prompt-select'
                    ])->label('Status') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'is_abnormal')->checkbox([
                        'label' => 'Mark as Abnormal Result'
                    ]) ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-clipboard-check"></i> Results</h5>

            <?= $form->field($model, 'results')->textarea([
                'rows' => 4,
                'placeholder' => "Enter test results here...\n\nExample:\nWBC: 7,500/µL (Normal: 4,500-11,000)\nRBC: 4.8M/µL (Normal: 4.5-5.5M)\nHemoglobin: 14.2 g/dL (Normal: 13.5-17.5)"
            ])->label('Test Results') ?>
            <small class="text-muted">Fill this when results are available. Leave blank if test is still pending.</small>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'results_date')->input('datetime-local', [
                        'min' => date('Y-m-d\TH:i'),
                        'class' => 'form-control'
                    ])->label('Results Date') ?>
                    <small class="text-muted">Results date must be today or in the future. Cannot be set to a past date.</small>
                </div>
            </div>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 2,
                'placeholder' => 'Additional notes or instructions for the lab...'
            ])->label('Notes') ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($model->isNewRecord ? 'Order Lab Test' : 'Update Lab Test'), [
                    'class' => 'btn btn-warning btn-lg'
                ]) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-secondary btn-lg ms-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    function disablePromptOption(selectElement) {
        var selectedValue = selectElement.value;
        var promptOption = selectElement.querySelector('option[value=\"\"]');
        if (promptOption) {
            promptOption.disabled = selectedValue !== '';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.prompt-select').forEach(function(select) {
            disablePromptOption(select);
            select.addEventListener('change', function() { disablePromptOption(this); });
        });
    });
");
?>