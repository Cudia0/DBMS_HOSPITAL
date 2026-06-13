<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_doctor".
 *
 * @property int $dr_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $license_number
 * @property float|null $dr_fee
 * @property int|null $dept_id
 * @property string|null $specialization
 * @property string|null $certification
 * @property string|null $email
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblDepartment $dept
 * @property TblAppointment[] $tblAppointments
 * @property TblPrescription[] $tblPrescriptions
 */
class TblDoctor extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_doctor';
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['dr_fee'], 'number', 'min' => 0],
            [['dept_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['license_number', 'specialization'], 'string', 'max' => 50],
            [['certification'], 'string', 'max' => 150],
            [['email'], 'string', 'max' => 150],
            [['email'], 'email', 'message' => 'Please enter a valid email address.'],
            [['email'], 'unique', 'message' => 'This email is already registered to another doctor.'],
            [['dept_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblDepartment::class, 'targetAttribute' => ['dept_id' => 'dept_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dr_id' => 'Doctor ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'license_number' => 'License Number',
            'dr_fee' => 'Consultation Fee',
            'dept_id' => 'Department',
            'specialization' => 'Specialization',
            'certification' => 'Certification',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getDept()
    {
        return $this->hasOne(TblDepartment::class, ['dept_id' => 'dept_id']);
    }

    public function getTblAppointments()
    {
        return $this->hasMany(TblAppointment::class, ['dr_id' => 'dr_id']);
    }

    public function getFullName()
    {
        return 'Dr. ' . $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedFee()
    {
        return '₱' . number_format($this->dr_fee, 2);
    }
}