<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_medical_record".
 *
 * @property int $record_id
 * @property int $appt_id
 * @property string|null $diagnosis
 * @property string|null $treatment_plan
 * @property string|null $vital_signs
 * @property string|null $notes
 * @property string|null $record_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblAppointment $appointment
 */
class TblMedicalRecord extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_medical_record';
    }

    public function rules()
    {
        return [
            [['appt_id'], 'required'],
            [['appt_id'], 'integer'],
            [['diagnosis', 'treatment_plan', 'notes'], 'string'],
            [['record_date', 'created_at', 'updated_at'], 'safe'],
            [['vital_signs'], 'string', 'max' => 255],
            [['appt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblAppointment::class, 'targetAttribute' => ['appt_id' => 'appt_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'appt_id' => 'Appointment',
            'diagnosis' => 'Diagnosis',
            'treatment_plan' => 'Treatment Plan',
            'vital_signs' => 'Vital Signs',
            'notes' => 'Notes',
            'record_date' => 'Record Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAppointment()
    {
        return $this->hasOne(TblAppointment::class, ['appt_id' => 'appt_id']);
    }

    // Get patient through appointment
    public function getPatient()
    {
        return $this->appointment ? $this->appointment->patient : null;
    }

    // Get doctor through appointment
    public function getDoctor()
    {
        return $this->appointment ? $this->appointment->doctor : null;
    }
}