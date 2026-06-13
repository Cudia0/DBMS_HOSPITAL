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
 * @property string $appointment_date
 * @property string $appointment_time
 * @property string $status
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
            [['appointment_date', 'appointment_time'], 'required'],
            [['symptoms_list'], 'string'],
            [['appointment_date', 'appointment_time', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => ['scheduled', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show']],
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
            'symptoms_list' => 'Symptoms',
            'appointment_date' => 'Appointment Date',
            'appointment_time' => 'Appointment Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Doctor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(TblDoctor::class, ['dr_id' => 'dr_id']);
    }

    /**
     * Gets query for [[Patient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(TblPatient::class, ['patient_id' => 'patient_id']);
    }

    /**
     * Gets query for [[Receptionist]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceptionist()
    {
        return $this->hasOne(TblReceptionist::class, ['recep_id' => 'recep_id']);
    }

    /**
     * Gets query for [[Bills]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBills()
    {
        return $this->hasMany(TblBill::class, ['appt_id' => 'appt_id']);
    }

    /**
     * Gets query for [[LabTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLabTests()
    {
        return $this->hasMany(TblLabTest::class, ['appt_id' => 'appt_id']);
    }

    /**
     * Gets query for [[MedicalRecords]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMedicalRecords()
    {
        return $this->hasMany(TblMedicalRecord::class, ['appt_id' => 'appt_id']);
    }

    /**
     * Gets query for [[Prescriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrescriptions()
    {
        return $this->hasMany(TblPrescription::class, ['appt_id' => 'appt_id']);
    }
}