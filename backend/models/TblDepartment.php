<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_department".
 *
 * @property int $dept_id
 * @property string|null $office_hours
 * @property string|null $start_time
 * @property string|null $end_time
 *
 * @property TblDoctor[] $tblDoctors
 */
class TblDepartment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['office_hours', 'start_time', 'end_time'], 'default', 'value' => null],
            [['start_time', 'end_time'], 'safe'],
            [['office_hours'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dept_id' => 'Dept ID',
            'office_hours' => 'Office Hours',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
        ];
    }

    /**
     * Gets query for [[TblDoctors]].
     *
     * @return \yii\db\ActiveQuery|TblDoctorQuery
     */
    public function getTblDoctors()
    {
        return $this->hasMany(TblDoctor::class, ['dept_id' => 'dept_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblDepartmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblDepartmentQuery(get_called_class());
    }

}
