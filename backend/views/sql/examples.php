<?php

use yii\helpers\Html;

$this->title = 'SQL Query Examples';
$this->params['breadcrumbs'][] = ['label' => 'SQL Monitor', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$sqlExamples = [
    [
        'category' => 'LEVEL 0: Independent Tables (No Foreign Keys)',
        'queries' => [
            ['desc' => 'Select all departments', 'sql' => 'SELECT * FROM tbl_department ORDER BY dept_name'],
            ['desc' => 'Create new department', 'sql' => "INSERT INTO tbl_department (dept_name, operating_days, office_hours) VALUES ('Cardiology', 'Monday-Friday', '8:00 AM - 5:00 PM')"],
            ['desc' => 'Select all patients', 'sql' => 'SELECT * FROM tbl_patient ORDER BY last_name, first_name'],
            ['desc' => 'Register new patient', 'sql' => "INSERT INTO tbl_patient (first_name, last_name, sex, date_of_birth, phone_num, country_code, email, address) VALUES ('Juan', 'Cruz', 'Male', '1991-03-15', '09192345678', '+63', 'juan.cruz@gmail.com', '123 Rizal Street, Manila')"],
            ['desc' => 'Update patient', 'sql' => 'UPDATE tbl_patient SET phone_num = :phone WHERE patient_id = :id'],
            ['desc' => 'Delete patient', 'sql' => 'DELETE FROM tbl_patient WHERE patient_id = :id'],
            ['desc' => 'Select all medicines', 'sql' => 'SELECT * FROM tbl_medicine ORDER BY med_name'],
            ['desc' => 'Add new medicine', 'sql' => "INSERT INTO tbl_medicine (med_name, dosage_form, strength, med_price) VALUES ('Paracetamol', 'Tablet', '500mg', 5.00)"],
        ],
    ],
    [
        'category' => 'LEVEL 1: Staff Tables',
        'queries' => [
            ['desc' => 'Select doctors with department (JOIN)', 'sql' => 'SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id ORDER BY d.last_name'],
            ['desc' => 'Create doctor', 'sql' => "INSERT INTO tbl_doctor (first_name, last_name, license_number, dr_fee, dept_id, specialization, email) VALUES ('Jose', 'Rizal', 'MED-001', 800.00, 1, 'Cardiology', 'jose.rizal@hospital.com')"],
            ['desc' => 'Create user account for doctor', 'sql' => "INSERT INTO user (username, email, password_hash, auth_key, status, created_at, updated_at) VALUES ('dr.jose.rizal', 'jose.rizal@hospital.com', '[hashed]', '[authkey]', 10, [timestamp], [timestamp])"],
            ['desc' => 'Select receptionists with director (JOIN)', 'sql' => 'SELECT r.*, d.first_name, d.last_name FROM tbl_receptionist r LEFT JOIN tbl_director d ON r.director_id = d.director_id'],
        ],
    ],
    [
        'category' => 'LEVEL 2: Appointments (Core Transaction)',
        'queries' => [
            ['desc' => 'Book appointment (patient)', 'sql' => "INSERT INTO tbl_appointment (patient_id, dr_id, symptoms_list, status) VALUES (1, 2, 'Chest pain and shortness of breath', NULL)"],
            ['desc' => 'Accept appointment (receptionist)', 'sql' => "UPDATE tbl_appointment SET appointment_date = '2026-06-15', appointment_time = '09:00:00', status = 'scheduled', recep_id = 1 WHERE appt_id = 1"],
            ['desc' => 'Check-in patient', 'sql' => "UPDATE tbl_appointment SET status = 'checked_in' WHERE appt_id = 1"],
            ['desc' => 'Get today\'s appointments (3-table JOIN)', 'sql' => "SELECT a.*, p.last_name, p.first_name, d.last_name AS doctor_lname FROM tbl_appointment a JOIN tbl_patient p ON a.patient_id = p.patient_id JOIN tbl_doctor d ON a.dr_id = d.dr_id WHERE a.appointment_date = CURDATE() ORDER BY a.appointment_time"],
            ['desc' => 'Doctor sees only own appointments', 'sql' => 'SELECT * FROM tbl_appointment WHERE dr_id = :doctor_id ORDER BY appointment_date DESC'],
            ['desc' => 'Reject/cancel appointment', 'sql' => "UPDATE tbl_appointment SET status = 'cancelled' WHERE appt_id = :id"],
        ],
    ],
    [
        'category' => 'LEVEL 3: Medical Records & Prescriptions',
        'queries' => [
            ['desc' => 'Create medical record', 'sql' => "INSERT INTO tbl_medical_record (appt_id, diagnosis, treatment_plan, vital_signs, notes, record_date) VALUES (1, 'Essential Hypertension Stage 1', 'Prescribed Losartan 50mg daily', 'BP: 145/92, HR: 78, Temp: 36.8°C', 'Monitor BP', NOW())"],
            ['desc' => 'Check existing medical record', 'sql' => 'SELECT * FROM tbl_medical_record WHERE appt_id = :appt_id'],
            ['desc' => 'Create prescription', 'sql' => "INSERT INTO tbl_prescription (appt_id, dosage_instructions, duration_days, notes, prescription_date) VALUES (1, 'Take 1 tablet once daily with food', 30, 'Check BP after 2 weeks', NOW())"],
            ['desc' => 'Add medicine to prescription (medline)', 'sql' => "INSERT INTO tbl_medline (prescription_id, med_id, qty, dosage_per_intake, frequency) VALUES (1, 2, 30, '1 tablet', 'once_daily')"],
            ['desc' => 'Order lab test', 'sql' => "INSERT INTO tbl_lab_test (appt_id, test_name, test_category, status, ordered_date) VALUES (1, 'Complete Blood Count (CBC)', 'Hematology', 'ordered', NOW())"],
            ['desc' => 'Get medicines for prescription (JOIN)', 'sql' => 'SELECT ml.*, m.med_name, m.strength, m.med_price FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.prescription_id = :id'],
        ],
    ],
    [
        'category' => 'LEVEL 3 & 4: Billing',
        'queries' => [
            ['desc' => 'Auto-generate bill', 'sql' => "INSERT INTO tbl_bill (appt_id, dr_fee, totalm_price, total_amount, payment_status, bill_date) VALUES (1, 0, 0, 0, 'pending', NOW())"],
            ['desc' => 'Add consultation fee (bill item)', 'sql' => "INSERT INTO tbl_bill_item (bill_id, item_type, description, quantity, unit_price, total_price) VALUES (1, 'consultation', 'Doctor Consultation Fee - Dr. Rizal', 1, 800.00, 800.00)"],
            ['desc' => 'Add medicine charge (bill item)', 'sql' => "INSERT INTO tbl_bill_item (bill_id, item_type, description, reference_id, quantity, unit_price, total_price) VALUES (1, 'medicine', 'Losartan 50mg x30', 2, 30, 18.75, 562.50)"],
            ['desc' => 'Recalculate bill totals from items (SUBQUERY)', 'sql' => 'UPDATE tbl_bill SET dr_fee = (SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :id AND item_type = \'consultation\'), totalm_price = (SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :id AND item_type = \'medicine\'), total_amount = (SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :id) WHERE bill_id = :id'],
            ['desc' => 'Mark bill as paid', 'sql' => "UPDATE tbl_bill SET payment_status = 'paid', payment_method = 'cash' WHERE bill_id = :id"],
            ['desc' => 'Complete appointment after payment', 'sql' => "UPDATE tbl_appointment SET status = 'completed' WHERE appt_id = :id"],
        ],
    ],
    [
        'category' => 'Authentication: Role Detection',
        'queries' => [
            ['desc' => 'Login - Find user by username', 'sql' => 'SELECT * FROM user WHERE username = :username AND status = 10'],
            ['desc' => 'Check if Director', 'sql' => 'SELECT director_id FROM tbl_director WHERE email = :email LIMIT 1'],
            ['desc' => 'Check if Doctor', 'sql' => 'SELECT dr_id FROM tbl_doctor WHERE email = :email LIMIT 1'],
            ['desc' => 'Check if Receptionist', 'sql' => 'SELECT recep_id FROM tbl_receptionist WHERE email = :email LIMIT 1'],
            ['desc' => 'Check if Patient', 'sql' => 'SELECT patient_id FROM tbl_patient WHERE email = :email LIMIT 1'],
        ],
    ],
    [
        'category' => 'Statistics & Reports (Director Dashboard)',
        'queries' => [
            ['desc' => 'Count total patients', 'sql' => 'SELECT COUNT(*) FROM tbl_patient'],
            ['desc' => 'Count total doctors', 'sql' => 'SELECT COUNT(*) FROM tbl_doctor'],
            ['desc' => 'Count today\'s appointments', 'sql' => "SELECT COUNT(*) FROM tbl_appointment WHERE appointment_date = CURDATE() AND status IS NOT NULL"],
            ['desc' => 'Count pending appointments', 'sql' => "SELECT * FROM tbl_appointment WHERE (status IS NULL OR status = '')"],
            ['desc' => 'Total revenue (paid bills)', 'sql' => "SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid'"],
            ['desc' => 'Monthly revenue', 'sql' => "SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid' AND bill_date >= :start_date"],
        ],
    ],
];
?>
<div class="sql-examples">

    <h1><i class="fas fa-book"></i> <?= Html::encode($this->title) ?></h1>

    <div class="alert alert-info">
        <strong><i class="fas fa-info-circle"></i> Documentation:</strong> 
        These are the SQL queries used by MediSync to communicate with the database.<br>
        All queries are executed through <code>yii\db\Command</code> class in the repository layer.
    </div>

    <?php foreach ($sqlExamples as $section): ?>
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><?= $section['category'] ?></h5>
        </div>
        <div class="card-body">
            <?php foreach ($section['queries'] as $i => $query): ?>
            <div class="mb-3">
                <h6 class="text-primary"><?= $i + 1 ?>. <?= Html::encode($query['desc']) ?></h6>
                <div class=" p-3 rounded border">
                    <code style="font-size: 12px; word-break: break-all;"><?= Html::encode($query['sql']) ?></code>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="alert alert-success">
        <strong><i class="fas fa-check-circle"></i> Total SQL Operations:</strong>
        <ul class="mb-0 mt-2">
            <li><strong>SELECT</strong> - Used for retrieving data (findAll, findById, search, count)</li>
            <li><strong>INSERT</strong> - Used for creating new records (create)</li>
            <li><strong>UPDATE</strong> - Used for modifying existing records (update, updateStatus, markAsPaid)</li>
            <li><strong>DELETE</strong> - Used for removing records (delete)</li>
        </ul>
    </div>

</div>