<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblMedicine $model */
/** @var yii\widgets\ActiveForm $form */

// Common medicine names for autocomplete suggestions
$medicineSuggestions = [
    'Amoxicillin',
    'Amlodipine',
    'Aspirin',
    'Atorvastatin',
    'Azithromycin',
    'Biogesic',
    'Bioflu',
    'Captopril',
    'Carbocisteine',
    'Cefalexin',
    'Cefixime',
    'Ceftriaxone',
    'Celecoxib',
    'Cetirizine',
    'Ciprofloxacin',
    'Clarithromycin',
    'Clindamycin',
    'Clonidine',
    'Clopidogrel',
    'Co-Amoxiclav',
    'Cotrimoxazole',
    'Decolgen',
    'Dextromethorphan',
    'Diazepam',
    'Diclofenac',
    'Digoxin',
    'Diphenhydramine',
    'Domperidone',
    'Doxycycline',
    'Enalapril',
    'Erythromycin',
    'Furosemide',
    'Gabapentin',
    'Gaviscon',
    'Glibenclamide',
    'Gliclazide',
    'Ibuprofen',
    'Isosorbide',
    'Isotretinoin',
    'Ketorolac',
    'Lactulose',
    'Lisinopril',
    'Loperamide',
    'Loratadine',
    'Losartan',
    'Lovastatin',
    'Mefenamic Acid',
    'Metformin',
    'Methotrexate',
    'Methylprednisolone',
    'Metoclopramide',
    'Metoprolol',
    'Metronidazole',
    'Montelukast',
    'Mucosolvan',
    'Multivitamins',
    'Naproxen',
    'Neozep',
    'Nifedipine',
    'Nitroglycerin',
    'Norethisterone',
    'Ofloxacin',
    'Omeprazole',
    'Ondansetron',
    'Oseltamivir',
    'Paracetamol',
    'Penicillin',
    'Phenylephrine',
    'Phenytoin',
    'Potassium Citrate',
    'Prednisone',
    'Pregabalin',
    'Ranitidine',
    'Rifampicin',
    'Risedronate',
    'Rosuvastatin',
    'Salbutamol',
    'Sertraline',
    'Simvastatin',
    'Solmux',
    'Strepsils',
    'Tetracycline',
    'Tramadol',
    'Tranexamic Acid',
    'Valproic Acid',
    'Valsartan',
    'Vitamin B Complex',
    'Vitamin C',
    'Vitamin D3',
    'Warfarin',
    'Zinc Sulfate',
];

// Common strength/dosage values for autocomplete
$strengthSuggestions = [
    '10mg',
    '20mg',
    '25mg',
    '40mg',
    '50mg',
    '75mg',
    '80mg',
    '100mg',
    '125mg',
    '150mg',
    '200mg',
    '250mg',
    '300mg',
    '400mg',
    '500mg',
    '600mg',
    '750mg',
    '800mg',
    '1000mg',
    '1mg/mL',
    '2mg/mL',
    '5mg/mL',
    '10mg/mL',
    '15mg/mL',
    '20mg/mL',
    '25mg/mL',
    '50mg/mL',
    '100mg/mL',
    '125mg/5mL',
    '250mg/5mL',
    '500mg/5mL',
    '5mcg',
    '10mcg',
    '25mcg',
    '50mcg',
    '100mcg',
    '200mcg',
    '500mcg',
    '1000mcg',
    '1%',
    '2%',
    '5%',
    '10%',
    '0.05%',
    '0.1%',
    '0.5%',
    '100IU',
    '200IU',
    '400IU',
    '1000IU',
    '5000IU',
];
?>

<div class="tbl-medicine-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-pills"></i> Medicine Information</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'medicine-form']]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'med_name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Type or select medicine name...',
                        'id' => 'med-name',
                        'list' => 'medicine-list',
                        'autocomplete' => 'off',
                        'required' => true
                    ])->label('Medicine Name *') ?>
                    
                    <datalist id="medicine-list">
                        <?php foreach ($medicineSuggestions as $med): ?>
                            <option value="<?= Html::encode($med) ?>"><?= Html::encode($med) ?></option>
                        <?php endforeach; ?>
                    </datalist>
                    
                    <small class="text-muted">Start typing to see medicine name suggestions</small>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'dosage_form')->dropDownList([ 
                        'tablet' => 'Tablet',
                        'capsule' => 'Capsule', 
                        'syrup' => 'Syrup', 
                        'suspension' => 'Suspension',
                        'solution' => 'Solution',
                        'injection' => 'Injection', 
                        'ointment' => 'Ointment', 
                        'cream' => 'Cream',
                        'gel' => 'Gel',
                        'drops' => 'Drops', 
                        'inhaler' => 'Inhaler',
                        'suppository' => 'Suppository',
                        'patch' => 'Patch',
                        'powder' => 'Powder',
                        'lotion' => 'Lotion',
                        'spray' => 'Spray',
                        'lozenge' => 'Lozenge',
                        'chewable' => 'Chewable Tablet',
                        'effervescent' => 'Effervescent Tablet',
                        'sublingual' => 'Sublingual',
                        'intravenous' => 'Intravenous (IV)',
                        'intramuscular' => 'Intramuscular (IM)',
                    ], [
                        'prompt' => '-- Select Dosage Form --',
                        'id' => 'med-dosage_form',
                        'class' => 'form-control prompt-select',
                        'required' => true
                    ])->label('Dosage Form *') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'strength')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'e.g., 500mg, 10mg/mL, 5%',
                        'id' => 'med-strength',
                        'list' => 'strength-list',
                        'autocomplete' => 'off'
                    ])->label('Strength / Dosage') ?>
                    
                    <datalist id="strength-list">
                        <?php foreach ($strengthSuggestions as $strength): ?>
                            <option value="<?= Html::encode($strength) ?>"><?= Html::encode($strength) ?></option>
                        <?php endforeach; ?>
                    </datalist>
                    
                    <small class="text-muted">Start typing to see common strength suggestions</small>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'med_price')->textInput([
                        'type' => 'text',
                        'placeholder' => '₱ 0.00',
                        'id' => 'med-price',
                        'class' => 'form-control text-right',
                        'required' => true,
                        'style' => 'font-size: 18px; font-weight: bold;'
                    ])->label('Medicine Price *') ?>
                    <small class="text-muted">Enter price in Philippine Peso (₱)</small>
                </div>
            </div>

            <!-- Price Preview -->
            <div class="alert alert-info mt-3" id="price-preview" style="display:none;">
                <strong><i class="fas fa-tag"></i> Price in Words:</strong>
                <span id="price-words"></span>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Medicine', [
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
    // PRICE FORMATTING (Philippine Peso)
    // ==========================================
    $('#med-price').on('input', function() {
        var value = $(this).val();
        
        // Remove all non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, '');
        
        // Ensure only one decimal point
        var parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (parts.length === 2 && parts[1].length > 2) {
            value = parts[0] + '.' + parts[1].substring(0, 2);
        }
        
        $(this).val(value);
        
        // Update price preview
        updatePricePreview(value);
    });
    
    // Format price on blur (add commas)
    $('#med-price').on('blur', function() {
        var value = parseFloat($(this).val());
        if (!isNaN(value) && value > 0) {
            $(this).val(value.toFixed(2));
            $(this).css('border-color', '#28a745');
        } else if ($(this).val() === '') {
            $(this).css('border-color', '#ced4da');
        } else {
            $(this).css('border-color', '#dc3545');
        }
    });
    
    // Remove formatting on focus for editing
    $('#med-price').on('focus', function() {
        var value = $(this).val().replace(/[^0-9.]/g, '');
        if (value && !isNaN(parseFloat(value))) {
            $(this).val(parseFloat(value).toString());
        }
    });
    
    function updatePricePreview(priceValue) {
        var price = parseFloat(priceValue);
        if (!isNaN(price) && price > 0) {
            var formattedPrice = '₱' + price.toFixed(2).replace(/\\B(?=(\\d{3})+(?!\\d))/g, ',');
            var words = numberToWords(price);
            
            $('#price-preview').show();
            $('#price-words').html('<strong>' + formattedPrice + '</strong> (' + words + ')');
        } else if (priceValue === '' || priceValue === '0') {
            $('#price-preview').hide();
        }
    }
    
    function numberToWords(num) {
        var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                    'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                    'Seventeen', 'Eighteen', 'Nineteen'];
        var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        
        // Handle pesos and centavos
        var pesos = Math.floor(num);
        var centavos = Math.round((num - pesos) * 100);
        
        var result = '';
        
        if (pesos > 0) {
            result += convertToWords(pesos) + ' Pesos';
        }
        
        if (centavos > 0) {
            if (result) result += ' and ';
            result += convertToWords(centavos) + ' Centavos';
        }
        
        if (!result) {
            result = 'Zero Pesos';
        }
        
        return result;
    }
    
    function convertToWords(num) {
        if (num < 20) {
            var ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                        'Seventeen', 'Eighteen', 'Nineteen'];
            return ones[num];
        }
        
        if (num < 100) {
            var tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            return tens[Math.floor(num / 10)] + (num % 10 > 0 ? ' ' + convertToWords(num % 10) : '');
        }
        
        if (num < 1000) {
            return convertToWords(Math.floor(num / 100)) + ' Hundred' + (num % 100 > 0 ? ' ' + convertToWords(num % 100) : '');
        }
        
        if (num < 1000000) {
            return convertToWords(Math.floor(num / 1000)) + ' Thousand' + (num % 1000 > 0 ? ' ' + convertToWords(num % 1000) : '');
        }
        
        return '';
    }
    
    // ==========================================
    // FORM SUBMISSION
    // ==========================================
    $('#medicine-form').on('beforeSubmit', function() {
        var dosageForm = $('#med-dosage_form').val();
        if (!dosageForm) {
            alert('Please select a dosage form.');
            return false;
        }
        
        var price = $('#med-price').val();
        if (!price || parseFloat(price) <= 0) {
            alert('Please enter a valid price.');
            return false;
        }
        
        // Format price before submit
        var formattedPrice = parseFloat(price).toFixed(2);
        $('#med-price').val(formattedPrice);
        
        return true;
    });
    
    // ==========================================
    // RESET FORM
    // ==========================================
    function resetForm() {
        $('#price-preview').hide();
        $('#med-price').css('border-color', '#ced4da');
        
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
        // Initialize dropdown prompts
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
        
        // Format existing price on load (for update form)
        var existingPrice = $('#med-price').val();
        if (existingPrice && !isNaN(parseFloat(existingPrice))) {
            var price = parseFloat(existingPrice);
            $('#med-price').val(price.toFixed(2));
            updatePricePreview(price);
        }
    });
");
?>

<?php
$this->registerCss("
    #med-price {
        font-size: 18px;
        font-weight: bold;
        text-align: right;
        padding-right: 15px;
    }
    
    #med-price::placeholder {
        font-weight: normal;
        font-size: 14px;
    }
    
    #price-preview {
        border-left: 4px solid #17a2b8;
    }
    
    #med-name, #med-strength {
        font-size: 15px;
    }
    
    datalist option {
        font-size: 14px;
        padding: 5px;
    }
    
    .text-right {
        text-align: right;
    }
");
?>