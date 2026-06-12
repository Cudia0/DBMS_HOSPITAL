<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_medicine".
 *
 * @property int $med_id
 * @property string|null $med_name
 * @property float|null $med_price
 *
 * @property TblPrescription[] $tblPrescriptions
 */
class TblMedicine extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_medicine';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['med_name', 'med_price'], 'default', 'value' => null],
            [['med_price'], 'number'],
            [['med_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'med_id' => 'Med ID',
            'med_name' => 'Med Name',
            'med_price' => 'Med Price',
        ];
    }

    /**
     * Gets query for [[TblPrescriptions]].
     *
     * @return \yii\db\ActiveQuery|TblPrescriptionQuery
     */
    public function getTblPrescriptions()
    {
        return $this->hasMany(TblPrescription::class, ['med_id' => 'med_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblMedicineQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblMedicineQuery(get_called_class());
    }

}
