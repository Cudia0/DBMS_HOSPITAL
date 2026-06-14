<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_appointment".
 *
 * @property int $appt_id
 * @property int|null $dr_id
 * @property int|null $patient_id
 * @property int|null $recep_id
 * @property string|null $symptoms_list
 * @property string|null $appointment_date
 * @property string|null $appointment_time
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblDoctor $doctor
 * @property TblPatient $patient
 * @property TblReceptionist $receptionist
 * @property TblBill[] $bills
 * @property TblLabTest[] $labTests
 * @property TblMedicalRecord[] $medicalRecords
 * @property TblPrescription[] $prescriptions
 */
class TblAppointment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_appointment';
    }

    public function rules()
    {
        return [
            [['dr_id', 'patient_id', 'recep_id'], 'integer'],
            [['symptoms_list'], 'string'],
            [['appointment_date', 'appointment_time', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => ['scheduled', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show']],
            // Patient booking: only dr_id and symptoms required
            [['dr_id', 'patient_id', 'symptoms_list'], 'required', 'on' => 'patient-booking', 'message' => '{attribute} cannot be blank.'],
            // Receptionist scheduling: date and time required
            [['appointment_date', 'appointment_time'], 'required', 'on' => 'receptionist-schedule', 'message' => '{attribute} cannot be blank.'],
            [['dr_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblDoctor::class, 'targetAttribute' => ['dr_id' => 'dr_id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblPatient::class, 'targetAttribute' => ['patient_id' => 'patient_id']],
            [['recep_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblReceptionist::class, 'targetAttribute' => ['recep_id' => 'recep_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'appt_id' => 'Appointment ID',
            'dr_id' => 'Doctor',
            'patient_id' => 'Patient',
            'recep_id' => 'Receptionist',
            'symptoms_list' => 'Symptoms / Reason for Visit',
            'appointment_date' => 'Appointment Date',
            'appointment_time' => 'Appointment Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getDoctor()
    {
        return $this->hasOne(TblDoctor::class, ['dr_id' => 'dr_id']);
    }

    public function getPatient()
    {
        return $this->hasOne(TblPatient::class, ['patient_id' => 'patient_id']);
    }

    public function getReceptionist()
    {
        return $this->hasOne(TblReceptionist::class, ['recep_id' => 'recep_id']);
    }

    public function getBills()
    {
        return $this->hasMany(TblBill::class, ['appt_id' => 'appt_id']);
    }

    public function getLabTests()
    {
        return $this->hasMany(TblLabTest::class, ['appt_id' => 'appt_id']);
    }

    public function getMedicalRecords()
    {
        return $this->hasMany(TblMedicalRecord::class, ['appt_id' => 'appt_id']);
    }

    public function getPrescriptions()
    {
        return $this->hasMany(TblPrescription::class, ['appt_id' => 'appt_id']);
    }

    /**
     * Get status label for display
     */
    public function getStatusLabel()
    {
        if ($this->status === null || $this->status === '') {
            return '<span class="badge bg-secondary">Pending Acceptance</span>';
        }
        $labels = [
            'scheduled' => '<span class="badge bg-warning">Scheduled</span>',
            'checked_in' => '<span class="badge bg-info">Checked In</span>',
            'in_progress' => '<span class="badge bg-primary">In Progress</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'no_show' => '<span class="badge bg-dark">No Show</span>',
        ];
        return $labels[$this->status] ?? $this->status;
    }
}