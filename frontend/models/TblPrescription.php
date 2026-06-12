<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_prescription".
 *
 * @property int $prescription_id
 * @property int|null $appt_id
 * @property int|null $med_id
 * @property int|null $dr_id
 * @property int|null $qty
 *
 * @property TblAppointment $appt
 * @property TblDoctor $dr
 * @property TblMedicine $med
 */
class TblPrescription extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_prescription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appt_id', 'med_id', 'dr_id', 'qty'], 'default', 'value' => null],
            [['appt_id', 'med_id', 'dr_id', 'qty'], 'integer'],
            [['appt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblAppointment::class, 'targetAttribute' => ['appt_id' => 'appt_id']],
            [['med_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblMedicine::class, 'targetAttribute' => ['med_id' => 'med_id']],
            [['dr_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblDoctor::class, 'targetAttribute' => ['dr_id' => 'dr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'prescription_id' => 'Prescription ID',
            'appt_id' => 'Appt ID',
            'med_id' => 'Med ID',
            'dr_id' => 'Dr ID',
            'qty' => 'Qty',
        ];
    }

    /**
     * Gets query for [[Appt]].
     *
     * @return \yii\db\ActiveQuery|TblAppointmentQuery
     */
    public function getAppt()
    {
        return $this->hasOne(TblAppointment::class, ['appt_id' => 'appt_id']);
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
     * Gets query for [[Med]].
     *
     * @return \yii\db\ActiveQuery|TblMedicineQuery
     */
    public function getMed()
    {
        return $this->hasOne(TblMedicine::class, ['med_id' => 'med_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblPrescriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblPrescriptionQuery(get_called_class());
    }

}
