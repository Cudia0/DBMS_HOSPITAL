<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_prescription".
 *
 * @property int $prescription_id
 * @property int|null $appt_id
 * @property string|null $prescription_date
 * @property string|null $dosage_instructions
 * @property int|null $duration_days
 * @property string|null $notes
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblAppointment $appointment
 * @property TblMedline[] $tblMedlines
 */
class TblPrescription extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_prescription';
    }

    public function rules()
    {
        return [
            [['appt_id', 'duration_days'], 'integer'],
            [['prescription_date', 'created_at', 'updated_at'], 'safe'],
            [['dosage_instructions', 'notes'], 'string'],
            [['appt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblAppointment::class, 'targetAttribute' => ['appt_id' => 'appt_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'prescription_id' => 'Prescription ID',
            'appt_id' => 'Appointment',
            'prescription_date' => 'Prescription Date',
            'dosage_instructions' => 'Dosage Instructions',
            'duration_days' => 'Duration (Days)',
            'notes' => 'Notes',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAppointment()
    {
        return $this->hasOne(TblAppointment::class, ['appt_id' => 'appt_id']);
    }

    public function getDoctor()
    {
        return $this->appointment ? $this->appointment->doctor : null;
    }

    public function getPatient()
    {
        return $this->appointment ? $this->appointment->patient : null;
    }

    public function getTblMedlines()
    {
        return $this->hasMany(TblMedline::class, ['prescription_id' => 'prescription_id']);
    }
}