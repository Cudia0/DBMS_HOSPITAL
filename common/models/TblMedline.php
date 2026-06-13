<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_medline".
 *
 * @property int $medline_id
 * @property int $prescription_id
 * @property int $med_id
 * @property int $qty
 * @property string|null $dosage_per_intake
 * @property string|null $frequency
 * @property string|null $created_at
 *
 * @property TblMedicine $med
 * @property TblPrescription $prescription
 */
class TblMedline extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_medline';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dosage_per_intake', 'frequency'], 'default', 'value' => null],
            [['qty'], 'default', 'value' => 1],
            [['prescription_id', 'med_id'], 'required'],
            [['prescription_id', 'med_id', 'qty'], 'integer'],
            [['created_at'], 'safe'],
            [['dosage_per_intake'], 'string', 'max' => 100],
            [['frequency'], 'string', 'max' => 50],
            [['med_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblMedicine::class, 'targetAttribute' => ['med_id' => 'med_id']],
            [['prescription_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblPrescription::class, 'targetAttribute' => ['prescription_id' => 'prescription_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'medline_id' => 'Medline ID',
            'prescription_id' => 'Prescription ID',
            'med_id' => 'Med ID',
            'qty' => 'Qty',
            'dosage_per_intake' => 'Dosage Per Intake',
            'frequency' => 'Frequency',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Med]].
     *
     * @return \yii\db\ActiveQuery|TblMedicineQuery
     */
    public function getMed()
    {
        return $this->hasOne(TblMedicine::class, ['med_id' => 'med_id']);
    }

    /**
     * Gets query for [[Prescription]].
     *
     * @return \yii\db\ActiveQuery|TblPrescriptionQuery
     */
    public function getPrescription()
    {
        return $this->hasOne(TblPrescription::class, ['prescription_id' => 'prescription_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblMedlineQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblMedlineQuery(get_called_class());
    }

}
