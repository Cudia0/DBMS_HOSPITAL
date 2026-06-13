<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_receptionist".
 *
 * @property int $recep_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $phone_num
 * @property string|null $country_code
 * @property int|null $director_id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblDirector $director
 * @property TblAppointment[] $tblAppointments
 */
class TblReceptionist extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_receptionist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['middle_name', 'email', 'phone_num', 'country_code', 'director_id'], 'default', 'value' => null],
            [['first_name', 'last_name'], 'required'],
            [['director_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 150],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
            [['director_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblDirector::class, 'targetAttribute' => ['director_id' => 'director_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'recep_id' => 'Recep ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone_num' => 'Phone Num',
            'country_code' => 'Country Code',
            'director_id' => 'Director ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Director]].
     *
     * @return \yii\db\ActiveQuery|TblDirectorQuery
     */
    public function getDirector()
    {
        return $this->hasOne(TblDirector::class, ['director_id' => 'director_id']);
    }

    /**
     * Gets query for [[TblAppointments]].
     *
     * @return \yii\db\ActiveQuery|TblAppointmentQuery
     */
    public function getTblAppointments()
    {
        return $this->hasMany(TblAppointment::class, ['recep_id' => 'recep_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblReceptionistQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblReceptionistQuery(get_called_class());
    }

}
