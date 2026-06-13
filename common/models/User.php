<?php

declare(strict_types=1);

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\TblPatient;
use common\models\TblDoctor;
use common\models\TblReceptionist;
use common\models\TblDirector;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    
    // Role constants
    const ROLE_PATIENT = 'patient';
    const ROLE_DOCTOR = 'doctor';
    const ROLE_RECEPTIONIST = 'receptionist';
    const ROLE_DIRECTOR = 'director';
    
    // Dynamic role properties (not stored in DB)
    public $role = null;
    public $patient_id = null;
    public $doctor_id = null;
    public $receptionist_id = null;
    public $director_id = null;

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    public static function findIdentity($id): User|null
    {
        $user = static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
        if ($user) {
            $user->detectRole();
        }
        return $user;
    }

    public static function findIdentityByAccessToken($token, $type = null): never
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername(string $username): User|null
    {
        $user = static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        if ($user) {
            $user->detectRole();
        }
        return $user;
    }

    public static function findByPasswordResetToken(string $token): User|null
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function findByVerificationToken(string $token): User|null
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid(string|null $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }
    
    /**
     * Auto-detect role by checking related tables via email
     */
    public function detectRole(): void
    {
        // Check Director table
        $director = TblDirector::find()->where(['email' => $this->email])->one();
        if ($director) {
            $this->role = self::ROLE_DIRECTOR;
            $this->director_id = $director->director_id;
            return;
        }
        
        // Check Doctor table
        $doctor = TblDoctor::find()->where(['email' => $this->email])->one();
        if ($doctor) {
            $this->role = self::ROLE_DOCTOR;
            $this->doctor_id = $doctor->dr_id;
            return;
        }
        
        // Check Receptionist table
        $receptionist = TblReceptionist::find()->where(['email' => $this->email])->one();
        if ($receptionist) {
            $this->role = self::ROLE_RECEPTIONIST;
            $this->receptionist_id = $receptionist->recep_id;
            return;
        }
        
        // Check Patient table
        $patient = TblPatient::find()->where(['email' => $this->email])->one();
        if ($patient) {
            $this->role = self::ROLE_PATIENT;
            $this->patient_id = $patient->patient_id;
            return;
        }
        
        // Default to patient
        $this->role = self::ROLE_PATIENT;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }
    
    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }
    
    public function isReceptionist(): bool
    {
        return $this->role === self::ROLE_RECEPTIONIST;
    }
    
    public function isDirector(): bool
    {
        return $this->role === self::ROLE_DIRECTOR;
    }
    
    public function canAccessBackend(): bool
    {
        return in_array($this->role, [self::ROLE_DIRECTOR, self::ROLE_RECEPTIONIST, self::ROLE_DOCTOR]);
    }
    
    public function getFullName(): string
    {
        switch ($this->role) {
            case self::ROLE_PATIENT:
                $patient = TblPatient::findOne($this->patient_id);
                return $patient ? $patient->getFullName() : $this->username;
            case self::ROLE_DOCTOR:
                $doctor = TblDoctor::findOne($this->doctor_id);
                return $doctor ? 'Dr. ' . $doctor->first_name . ' ' . $doctor->last_name : $this->username;
            case self::ROLE_RECEPTIONIST:
                $recep = TblReceptionist::findOne($this->receptionist_id);
                return $recep ? $recep->first_name . ' ' . $recep->last_name : $this->username;
            case self::ROLE_DIRECTOR:
                $director = TblDirector::findOne($this->director_id);
                return $director ? $director->first_name . ' ' . $director->last_name : $this->username;
        }
        return $this->username;
    }
    
    public function getRoleLabel(): string
    {
        return ucfirst($this->role);
    }
}