<?php

declare(strict_types=1);

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\TblPatient;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    
    // Patient information fields
    public $first_name;
    public $middle_name;
    public $last_name;
    public $sex;
    public $date_of_birth;
    public $phone_num;
    public $country_code;
    public $address;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            // User fields
            ['username', 'trim'],
            ['username', 'required', 'message' => 'Username cannot be blank.'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 3, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required', 'message' => 'Email cannot be blank.'],
            ['email', 'email', 'message' => 'Please enter a valid email address.'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            ['email', 'match', 'pattern' => '/^[a-zA-Z0-9._%+-]+@gmail\.com$/', 'message' => 'Please use a valid Gmail address (example@gmail.com).'],

            ['password', 'required', 'message' => 'Password cannot be blank.'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength'] ?? 6, 'message' => 'Password must be at least 6 characters.'],
            
            // Patient fields
            [['first_name', 'last_name'], 'required', 'message' => 'This field cannot be blank.'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['sex'], 'string'],
            [['sex'], 'in', 'range' => ['Male', 'Female'], 'message' => 'Please select a valid sex.'],
            [['date_of_birth'], 'date', 'format' => 'php:Y-m-d', 'max' => date('Y-m-d'), 'message' => 'Date of birth cannot be in the future.'],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
            [['address'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'sex' => 'Sex',
            'date_of_birth' => 'Date of Birth',
            'phone_num' => 'Phone Number',
            'country_code' => 'Country Code',
            'address' => 'Address',
        ];
    }

    /**
     * Signs user up (creates INACTIVE user + patient record).
     * User must verify email before they can login.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup(): User|null
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // 1. Create User account as INACTIVE
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = User::STATUS_INACTIVE; // INACTIVE until email verified
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();
            
            if (!$user->save()) {
                $transaction->rollBack();
                Yii::error('Failed to save user: ' . json_encode($user->errors));
                return null;
            }
            
            // 2. Create Patient record linked to this user (also inactive until verified)
            $patient = new TblPatient();
            $patient->first_name = $this->first_name;
            $patient->middle_name = $this->middle_name;
            $patient->last_name = $this->last_name;
            $patient->sex = $this->sex;
            $patient->date_of_birth = $this->date_of_birth;
            $patient->phone_num = $this->phone_num;
            $patient->country_code = $this->country_code;
            $patient->email = $this->email;
            $patient->address = $this->address;
            
            if (!$patient->save()) {
                $transaction->rollBack();
                $user->delete();
                Yii::error('Failed to save patient: ' . json_encode($patient->errors));
                return null;
            }
            
            $transaction->commit();
            
            Yii::$app->session->set('pending_patient_id', $patient->patient_id);
            
            return $user;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Signup failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sends verification email to user.
     *
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    public function sendEmail(User $user): bool
    {
        if (!$user->verification_token) {
            return false;
        }

        // Try to send email
        try {
            return Yii::$app->mailer->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Verify your account at ' . Yii::$app->name)
            ->send();
        } catch (\Exception $e) {
            Yii::error('Failed to send verification email: ' . $e->getMessage());
            return false;
        }
    }
}