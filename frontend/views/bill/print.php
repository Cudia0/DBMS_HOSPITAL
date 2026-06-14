<?php

/** @var common\models\TblBill $model */
/** @var array $billItems */
/** @var common\models\TblPatient $patient */
/** @var common\models\TblDoctor $doctor */
/** @var common\models\TblAppointment $appointment */
/** @var common\models\TblPrescription $prescription */
/** @var array $labTests */

use yii\helpers\Html;

// ============================================
// CONFIGURABLE SETTINGS - MODIFY HERE
// ============================================
$hospitalName = 'MediSync';
$hospitalTagline = 'HOSPITAL MANAGEMENT SYSTEM';
$hospitalAddress = '123 Health Street, Medical City, Philippines';
$hospitalPhone = '(02) 8123-4567';
$hospitalEmail = 'info@medisync.com';
$hospitalWebsite = 'www.medisync.com';
$receiptTitle = 'OFFICIAL RECEIPT';
$watermarkText = 'MediSync';
$watermarkOpacity = '0.01'; // Lower = more transparent (0.01-0.10)
$footerText = 'This is a computer-generated receipt. No physical signature required.';
$thankYouText = 'Thank you for choosing MediSync! Your health is our priority.';

// Colors
$primaryColor = '#0d6efd';
$secondaryColor = '#00b4d8';
$headerBorderColor = '#0d6efd';
$tableHeaderBg = '#0d6efd';
$totalBg = '#f0f9ff';

$totalItems = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid <?= $headerBorderColor ?>;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: <?= $primaryColor ?>;
            margin: 0;
            font-size: 26px;
            letter-spacing: 2px;
        }
        .header h2 {
            color: <?= $secondaryColor ?>;
            margin: 5px 0 10px 0;
            font-size: 13px;
            letter-spacing: 4px;
            font-weight: normal;
        }
        .header p {
            margin: 3px 0;
            font-size: 10px;
            color: #666;
        }
        .receipt-title {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: <?= $totalBg ?>;
            border-radius: 4px;
        }
        .receipt-title h2 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        .receipt-title p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
            border-bottom: 1px dotted #ddd;
        }
        .info-table .label {
            font-weight: bold;
            width: 100px;
            color: #555;
            font-size: 11px;
        }
        .section-title {
            color: <?= $primaryColor ?>;
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid <?= $primaryColor ?>;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: <?= $tableHeaderBg ?>;
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }
        .items-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 10px;
            padding: 10px;
            background-color: <?= $totalBg ?>;
            border-radius: 4px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 4px 10px;
            font-size: 12px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: <?= $primaryColor ?>;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .footer p {
            margin: 3px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-paid { background-color: #28a745; color: white; }
        .status-partial { background-color: #17a2b8; color: white; }
        .status-refunded { background-color: #6c757d; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .signature-section {
            margin-top: 60px;
            text-align: center;
        }
        .signature-line {
            display: inline-block;
            width: 180px;
            border-top: 1px solid #333;
            margin: 0 25px;
            padding-top: 8px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 30%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(13, 110, 253, <?= $watermarkOpacity ?>);
            pointer-events: none;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    
    <!-- HEADER -->
    <div class="header">
        <h1><?= $hospitalName ?></h1>
        <h2><?= $hospitalTagline ?></h2>
        <p><?= $hospitalAddress ?> | Tel: <?= $hospitalPhone ?></p>
        <p>Email: <?= $hospitalEmail ?> | Website: <?= $hospitalWebsite ?></p>
    </div>

    <!-- RECEIPT TITLE -->
    <div class="receipt-title">
        <h2><?= $receiptTitle ?></h2>
        <p>Bill #<?= $model->bill_id ?> | Date: <?= Yii::$app->formatter->asDatetime($model->bill_date, 'medium') ?></p>
    </div>

    <!-- PATIENT & DOCTOR INFO -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="label">Patient:</td>
                <td><strong><?= $patient ? Html::encode($patient->getFullName()) : 'N/A' ?></strong></td>
                <td class="label">Doctor:</td>
                <td><strong><?= $doctor ? 'Dr. ' . Html::encode($doctor->first_name . ' ' . $doctor->last_name) : 'N/A' ?></strong></td>
            </tr>
            <tr>
                <td class="label">Patient ID:</td>
                <td>#<?= $patient->patient_id ?? 'N/A' ?></td>
                <td class="label">Specialization:</td>
                <td><?= Html::encode($doctor->specialization ?? 'General') ?></td>
            </tr>
            <tr>
                <td class="label">Sex/Age:</td>
                <td><?= Html::encode($patient->sex ?? 'N/A') ?> / <?= $patient ? $patient->getAgeDisplay() : 'N/A' ?></td>
                <td class="label">Appointment:</td>
                <td>#<?= $model->appt_id ?> | <?= $appointment ? Yii::$app->formatter->asDate($appointment->appointment_date, 'long') : 'N/A' ?></td>
            </tr>
            <tr>
                <td class="label">Contact:</td>
                <td><?= Html::encode(($patient->country_code ? $patient->country_code . ' ' : '') . ($patient->phone_num ?? 'N/A')) ?></td>
                <td class="label">Status:</td>
                <td>
                    <span class="status-badge status-<?= $model->payment_status ?>">
                        <?= strtoupper($model->payment_status) ?>
                    </span>
                    <?php if ($model->payment_method): ?>
                        <br><small>via <?= ucfirst($model->payment_method) ?></small>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- BILL CHARGES -->
    <div class="section-title">CHARGES BREAKDOWN</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">Type</th>
                <th width="40%">Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($billItems)): ?>
                <?php foreach ($billItems as $index => $item): ?>
                <?php $totalItems += $item->total_price; ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= ucfirst($item->item_type) ?></td>
                    <td><?= Html::encode($item->description) ?></td>
                    <td class="text-center"><?= $item->quantity ?></td>
                    <td class="text-right">₱<?= number_format($item->unit_price, 2) ?></td>
                    <td class="text-right">₱<?= number_format($item->total_price, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; padding: 20px;">No charges recorded</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="total-section">
        <table class="total-table">
            <tr>
                <td style="text-align: right;">Consultation Fee:</td>
                <td width="120" style="text-align: right;">₱<?= number_format($model->dr_fee, 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right;">Medicine Total:</td>
                <td width="120" style="text-align: right;">₱<?= number_format($model->totalm_price, 2) ?></td>
            </tr>
            <tr class="grand-total">
                <td style="text-align: right; padding-top: 10px; border-top: 2px solid #333;">GRAND TOTAL:</td>
                <td width="120" style="text-align: right; padding-top: 10px; border-top: 2px solid #333;">₱<?= number_format($model->total_amount, 2) ?></td>
            </tr>
        </table>
    </div>

    <!-- PRESCRIPTION DETAILS -->
    <?php if ($prescription): ?>
    <div class="section-title">PRESCRIPTION DETAILS</div>
    <table class="info-table">
        <tr>
            <td class="label">Prescription #:</td>
            <td><?= $prescription->prescription_id ?></td>
            <td class="label">Duration:</td>
            <td><?= $prescription->duration_days ? $prescription->duration_days . ' days' : 'N/A' ?></td>
        </tr>
        <?php if ($prescription->dosage_instructions): ?>
        <tr>
            <td class="label">Instructions:</td>
            <td colspan="3"><?= Html::encode($prescription->dosage_instructions) ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($prescription->notes): ?>
        <tr>
            <td class="label">Notes:</td>
            <td colspan="3"><?= Html::encode($prescription->notes) ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <!-- LAB TESTS -->
    <?php if (!empty($labTests)): ?>
    <div class="section-title">LABORATORY TESTS</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Test Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Result Date</th>
                <th>Abnormal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($labTests as $test): ?>
            <tr>
                <td><?= Html::encode($test->test_name) ?></td>
                <td><?= Html::encode(ucfirst($test->test_category ?? 'N/A')) ?></td>
                <td><?= ucfirst($test->status) ?></td>
                <td><?= $test->results_date ? Yii::$app->formatter->asDate($test->results_date) : 'Pending' ?></td>
                <td><?= $test->is_abnormal ? '⚠️ Yes' : 'No' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- SIGNATURES -->
    <div class="signature-section">
        <span class="signature-line">Patient Signature</span>
        <span class="signature-line">Receptionist Signature</span>
        <span class="signature-line">Doctor Signature</span>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p><?= $footerText ?></p>
        <p>Printed on: <?= date('F j, Y \a\t h:i A') ?> | Generated by <?= $hospitalName ?> Hospital Management System</p>
        <p><?= $thankYouText ?></p>
    </div>

</body>
</html>