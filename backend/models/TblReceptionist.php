<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_receptionist".
 *
 * @property int $recep_id
 * @property string|null $Full_Name
 * @property string|null $Email
 * @property string|null $phone_num
 * @property string|null $country_code
 *
 * @property TblDirector[] $tblDirectors
 * @property TblPatient[] $tblPatients
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
            [['Full_Name', 'Email', 'phone_num', 'country_code'], 'default', 'value' => null],
            [['Full_Name', 'Email'], 'string', 'max' => 150],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'recep_id' => 'Recep ID',
            'Full_Name' => 'Full Name',
            'Email' => 'Email',
            'phone_num' => 'Phone Num',
            'country_code' => 'Country Code',
        ];
    }

    /**
     * Gets query for [[TblDirectors]].
     *
     * @return \yii\db\ActiveQuery|TblDirectorQuery
     */
    public function getTblDirectors()
    {
        return $this->hasMany(TblDirector::class, ['recep_id' => 'recep_id']);
    }

    /**
     * Gets query for [[TblPatients]].
     *
     * @return \yii\db\ActiveQuery|TblPatientQuery
     */
    public function getTblPatients()
    {
        return $this->hasMany(TblPatient::class, ['recep_id' => 'recep_id']);
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
