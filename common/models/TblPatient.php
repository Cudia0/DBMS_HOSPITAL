<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_patient".
 *
 * @property int $patient_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $sex
 * @property string|null $date_of_birth
 * @property string|null $phone_num
 * @property string|null $country_code
 * @property string|null $email
 * @property string|null $address
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblAppointment[] $tblAppointments
 */
class TblPatient extends \yii\db\ActiveRecord
{
    const SEX_MALE = 'Male';
    const SEX_FEMALE = 'Female';

    public static function tableName()
    {
        return 'tbl_patient';
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['sex', 'address'], 'string'],
            [['date_of_birth', 'created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 150],
            ['sex', 'in', 'range' => array_keys(self::optsSex())],
            [['email'], 'email', 'message' => 'Please enter a valid email address.'],
            [
                ['email'], 
                'match', 
                'pattern' => '/^[a-zA-Z0-9._%+-]+@gmail\.com$|^N\/A$|^n\/a$/', 
                'message' => 'Email must be a valid Gmail address or N/A.'
            ],
            [['date_of_birth'], 'date', 'format' => 'php:Y-m-d', 'max' => date('Y-m-d'), 'message' => 'Date of birth cannot be in the future.'],
            [['first_name', 'last_name', 'date_of_birth'], 'validateDuplicatePatient', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Custom validation to check for duplicate patients
     */
    public function validateDuplicatePatient($attribute, $params)
    {
        if ($this->first_name && $this->last_name && $this->date_of_birth) {
            $existingPatient = self::find()
                ->where([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'date_of_birth' => $this->date_of_birth,
                ])
                ->andFilterWhere(['!=', 'patient_id', $this->patient_id])
                ->one();
            
            if ($existingPatient) {
                $this->addError('first_name', '⚠️ A patient with this name and date of birth already exists (Patient ID: ' . $existingPatient->patient_id . ' - ' . $existingPatient->getFullName() . '). This may be a duplicate record.');
            }
        }
        
        if ($this->phone_num && $this->country_code) {
            $existingPhone = self::find()
                ->where([
                    'phone_num' => $this->phone_num,
                    'country_code' => $this->country_code,
                ])
                ->andFilterWhere(['!=', 'patient_id', $this->patient_id])
                ->one();
            
            if ($existingPhone) {
                $this->addError('phone_num', '⚠️ This phone number (' . $this->country_code . ' ' . $this->phone_num . ') is already registered to Patient ID: ' . $existingPhone->patient_id . ' (' . $existingPhone->getFullName() . ').');
            }
        }
        
        if ($this->email && $this->email !== 'N/A' && $this->email !== 'n/a' && $this->email !== '') {
            $existingEmail = self::find()
                ->where(['email' => $this->email])
                ->andFilterWhere(['!=', 'patient_id', $this->patient_id])
                ->one();
            
            if ($existingEmail) {
                $this->addError('email', '⚠️ This email (' . $this->email . ') is already registered to Patient ID: ' . $existingEmail->patient_id . ' (' . $existingEmail->getFullName() . ').');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'patient_id' => 'Patient ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'sex' => 'Sex',
            'date_of_birth' => 'Date Of Birth',
            'phone_num' => 'Phone Number',
            'country_code' => 'Country Code',
            'email' => 'Email',
            'address' => 'Address',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get patient's full name
     * @return string
     */
    public function getFullName()
    {
        return $this->last_name . ', ' . $this->first_name . ($this->middle_name ? ' ' . $this->middle_name : '');
    }

    /**
     * Calculate age from date of birth (computed, not stored)
     * @return int|null
     */
    public function getAge()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        $dob = new \DateTime($this->date_of_birth);
        $now = new \DateTime();
        $interval = $now->diff($dob);
        
        return $interval->y;
    }

    /**
     * Get formatted age display
     * @return string
     */
    public function getAgeDisplay()
    {
        $age = $this->getAge();
        if ($age === null) {
            return 'N/A';
        }
        return $age . ' years old';
    }

    /**
     * Gets query for [[TblAppointments]].
     */
    public function getTblAppointments()
    {
        return $this->hasMany(TblAppointment::class, ['patient_id' => 'patient_id']);
    }

    public static function optsSex()
    {
        return [
            self::SEX_MALE => 'Male',
            self::SEX_FEMALE => 'Female',
        ];
    }

    public function displaySex()
    {
        return self::optsSex()[$this->sex] ?? '';
    }

    public function isSexMale()
    {
        return $this->sex === self::SEX_MALE;
    }

    public function isSexFemale()
    {
        return $this->sex === self::SEX_FEMALE;
    }
}