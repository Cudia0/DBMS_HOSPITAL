<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_doctor".
 *
 * @property int $dr_id
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property float|null $dr_fee
 * @property int|null $dept_id
 * @property string|null $specialization
 * @property string|null $certification
 *
 * @property TblDepartment $dept
 * @property TblAppointment[] $tblAppointments
 * @property TblPrescription[] $tblPrescriptions
 */
class TblDoctor extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_doctor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'last_name', 'dr_fee', 'dept_id', 'specialization', 'certification'], 'default', 'value' => null],
            [['dr_fee'], 'number'],
            [['dept_id'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'specialization'], 'string', 'max' => 100],
            [['certification'], 'string', 'max' => 150],
            [['dept_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblDepartment::class, 'targetAttribute' => ['dept_id' => 'dept_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dr_id' => 'Dr ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'dr_fee' => 'Dr Fee',
            'dept_id' => 'Dept ID',
            'specialization' => 'Specialization',
            'certification' => 'Certification',
        ];
    }

    /**
     * Gets query for [[Dept]].
     *
     * @return \yii\db\ActiveQuery|TblDepartmentQuery
     */
    public function getDept()
    {
        return $this->hasOne(TblDepartment::class, ['dept_id' => 'dept_id']);
    }

    /**
     * Gets query for [[TblAppointments]].
     *
     * @return \yii\db\ActiveQuery|TblAppointmentQuery
     */
    public function getTblAppointments()
    {
        return $this->hasMany(TblAppointment::class, ['dr_id' => 'dr_id']);
    }

    /**
     * Gets query for [[TblPrescriptions]].
     *
     * @return \yii\db\ActiveQuery|TblPrescriptionQuery
     */
    public function getTblPrescriptions()
    {
        return $this->hasMany(TblPrescription::class, ['dr_id' => 'dr_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblDoctorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblDoctorQuery(get_called_class());
    }

}
