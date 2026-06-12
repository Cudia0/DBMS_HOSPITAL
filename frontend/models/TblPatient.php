<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_patient".
 *
 * @property int $patient_id
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property int|null $age
 * @property string|null $date_of_birth
 * @property string|null $phone_num
 * @property string|null $country_code
 * @property string|null $email
 * @property int|null $recep_id
 *
 * @property TblReceptionist $recep
 * @property TblAppointment[] $tblAppointments
 */
class TblPatient extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_patient';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'middle_name', 'last_name', 'age', 'date_of_birth', 'phone_num', 'country_code', 'email', 'recep_id'], 'default', 'value' => null],
            [['age', 'recep_id'], 'integer'],
            [['date_of_birth'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 150],
            [['recep_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblReceptionist::class, 'targetAttribute' => ['recep_id' => 'recep_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'patient_id' => 'Patient ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'age' => 'Age',
            'date_of_birth' => 'Date Of Birth',
            'phone_num' => 'Phone Num',
            'country_code' => 'Country Code',
            'email' => 'Email',
            'recep_id' => 'Recep ID',
        ];
    }

    /**
     * Gets query for [[Recep]].
     *
     * @return \yii\db\ActiveQuery|TblReceptionistQuery
     */
    public function getRecep()
    {
        return $this->hasOne(TblReceptionist::class, ['recep_id' => 'recep_id']);
    }

    /**
     * Gets query for [[TblAppointments]].
     *
     * @return \yii\db\ActiveQuery|TblAppointmentQuery
     */
    public function getTblAppointments()
    {
        return $this->hasMany(TblAppointment::class, ['patient_id' => 'patient_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblPatientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblPatientQuery(get_called_class());
    }

}
