<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_director".
 *
 * @property int $director_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $phone_num
 * @property string|null $country_code
 * @property string $email
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblReceptionist[] $tblReceptionists
 */
class TblDirector extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_director';
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email'], 'required', 'message' => 'This field cannot be blank.'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 150],
            [['email'], 'email', 'message' => 'Please enter a valid email address.'],
            [['email'], 'unique', 'message' => 'This email is already registered to another director.'],
            [['phone_num', 'email'], 'validateDuplicateDirector', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Custom validation to check for duplicate directors
     */
    public function validateDuplicateDirector($attribute, $params)
    {
        if ($this->phone_num && $this->country_code) {
            $existingPhone = self::find()
                ->where([
                    'phone_num' => $this->phone_num,
                    'country_code' => $this->country_code,
                ])
                ->andFilterWhere(['!=', 'director_id', $this->director_id])
                ->one();
            
            if ($existingPhone) {
                $this->addError('phone_num', '⚠️ This phone number is already registered to Director ID: ' . $existingPhone->director_id . ' (' . $existingPhone->first_name . ' ' . $existingPhone->last_name . ').');
            }
        }
        
        if ($this->email) {
            $existingEmail = self::find()
                ->where(['email' => $this->email])
                ->andFilterWhere(['!=', 'director_id', $this->director_id])
                ->one();
            
            if ($existingEmail) {
                $this->addError('email', '⚠️ This email is already registered to Director ID: ' . $existingEmail->director_id . ' (' . $existingEmail->first_name . ' ' . $existingEmail->last_name . ').');
            }
        }
        
        if ($this->first_name && $this->last_name) {
            $existingDirector = self::find()
                ->where([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                ])
                ->andFilterWhere(['middle_name' => $this->middle_name])
                ->andFilterWhere(['!=', 'director_id', $this->director_id])
                ->one();
            
            if ($existingDirector) {
                $this->addError('first_name', '⚠️ A director with this name already exists (Director ID: ' . $existingDirector->director_id . ').');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'director_id' => 'Director ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'phone_num' => 'Phone Number',
            'country_code' => 'Country Code',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }

    public function getTblReceptionists()
    {
        return $this->hasMany(TblReceptionist::class, ['director_id' => 'director_id']);
    }
}