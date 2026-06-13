<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_department".
 *
 * @property int $dept_id
 * @property string $dept_name
 * @property string|null $operating_days
 * @property string|null $office_hours
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblDoctor[] $tblDoctors
 */
class TblDepartment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_department';
    }

    public function rules()
    {
        return [
            [['dept_name'], 'required'],
            [['dept_name'], 'string', 'max' => 100],
            [['dept_name'], 'unique', 'message' => 'This department name already exists.'],
            [['operating_days', 'office_hours'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dept_id' => 'Department ID',
            'dept_name' => 'Department Name',
            'operating_days' => 'Operating Days',
            'office_hours' => 'Office Hours',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getTblDoctors()
    {
        return $this->hasMany(TblDoctor::class, ['dept_id' => 'dept_id']);
    }
}