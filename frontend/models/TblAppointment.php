<?php

namespace app\models;

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
 *
 * @property TblDoctor $dr
 * @property TblPatient $patient
 * @property TblBill[] $tblBills
 * @property TblPrescription[] $tblPrescriptions
 */
class TblAppointment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_appointment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dr_id', 'patient_id', 'recep_id', 'symptoms_list', 'appointment_date'], 'default', 'value' => null],
            [['dr_id', 'patient_id', 'recep_id'], 'integer'],
            [['symptoms_list'], 'string'],
            [['appointment_date'], 'safe'],
            [['dr_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblDoctor::class, 'targetAttribute' => ['dr_id' => 'dr_id']],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblPatient::class, 'targetAttribute' => ['patient_id' => 'patient_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'appt_id' => 'Appt ID',
            'dr_id' => 'Dr ID',
            'patient_id' => 'Patient ID',
            'recep_id' => 'Recep ID',
            'symptoms_list' => 'Symptoms List',
            'appointment_date' => 'Appointment Date',
        ];
    }

    /**
     * Gets query for [[Dr]].
     *
     * @return \yii\db\ActiveQuery|TblDoctorQuery
     */
    public function getDr()
    {
        return $this->hasOne(TblDoctor::class, ['dr_id' => 'dr_id']);
    }

    /**
     * Gets query for [[Patient]].
     *
     * @return \yii\db\ActiveQuery|TblPatientQuery
     */
    public function getPatient()
    {
        return $this->hasOne(TblPatient::class, ['patient_id' => 'patient_id']);
    }

    /**
     * Gets query for [[TblBills]].
     *
     * @return \yii\db\ActiveQuery|TblBillQuery
     */
    public function getTblBills()
    {
        return $this->hasMany(TblBill::class, ['appt_id' => 'appt_id']);
    }

    /**
     * Gets query for [[TblPrescriptions]].
     *
     * @return \yii\db\ActiveQuery|TblPrescriptionQuery
     */
    public function getTblPrescriptions()
    {
        return $this->hasMany(TblPrescription::class, ['appt_id' => 'appt_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblAppointmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblAppointmentQuery(get_called_class());
    }

}
