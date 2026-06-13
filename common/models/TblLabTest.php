<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_lab_test".
 *
 * @property int $test_id
 * @property int|null $appt_id
 * @property string $test_name
 * @property string|null $test_category
 * @property string|null $status
 * @property string|null $results
 * @property int|null $is_abnormal
 * @property string|null $ordered_date
 * @property string|null $results_date
 * @property string|null $notes
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblAppointment $appointment
 */
class TblLabTest extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_lab_test';
    }

    public function rules()
    {
        return [
            [['test_name'], 'required'],
            [['appt_id'], 'integer'],
            [['status', 'results', 'notes'], 'string'],
            [['is_abnormal'], 'boolean'],
            [['ordered_date', 'results_date', 'created_at', 'updated_at'], 'safe'],
            [['test_name'], 'string', 'max' => 100],
            [['test_category'], 'string', 'max' => 50],
            [['status'], 'in', 'range' => ['ordered', 'collected', 'processing', 'completed', 'cancelled']],
            [['appt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblAppointment::class, 'targetAttribute' => ['appt_id' => 'appt_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'test_id' => 'Test ID',
            'appt_id' => 'Appointment',
            'test_name' => 'Test Name',
            'test_category' => 'Test Category',
            'status' => 'Status',
            'results' => 'Results',
            'is_abnormal' => 'Is Abnormal',
            'ordered_date' => 'Ordered Date',
            'results_date' => 'Results Date',
            'notes' => 'Notes',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAppointment()
    {
        return $this->hasOne(TblAppointment::class, ['appt_id' => 'appt_id']);
    }

    public function getPatient()
    {
        return $this->appointment ? $this->appointment->patient : null;
    }

    public function getDoctor()
    {
        return $this->appointment ? $this->appointment->doctor : null;
    }
}