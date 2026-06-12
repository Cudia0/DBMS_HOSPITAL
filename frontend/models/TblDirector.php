<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_director".
 *
 * @property int $director_id
 * @property string|null $full_name
 * @property string|null $phone_num
 * @property string|null $country_code
 * @property string|null $email
 * @property int|null $recep_id
 *
 * @property TblReceptionist $recep
 */
class TblDirector extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_director';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'phone_num', 'country_code', 'email', 'recep_id'], 'default', 'value' => null],
            [['recep_id'], 'integer'],
            [['full_name', 'email'], 'string', 'max' => 150],
            [['phone_num'], 'string', 'max' => 20],
            [['country_code'], 'string', 'max' => 10],
            [['recep_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblReceptionist::class, 'targetAttribute' => ['recep_id' => 'recep_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'director_id' => 'Director ID',
            'full_name' => 'Full Name',
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
     * {@inheritdoc}
     * @return TblDirectorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblDirectorQuery(get_called_class());
    }

}
