<?php

namespace common\models;

use Yii;

class TblMedicine extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_medicine';
    }

    public function rules()
    {
        return [
            [['med_name', 'dosage_form', 'med_price'], 'required'],
            [['med_price'], 'number', 'min' => 0.01, 'message' => 'Price must be greater than 0.'],
            [['created_at', 'updated_at'], 'safe'],
            [['med_name'], 'string', 'max' => 100],
            [['dosage_form', 'strength'], 'string', 'max' => 50],
            [['med_name', 'strength'], 'validateDuplicateMedicine', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Custom validation to check for duplicate medicines
     */
    public function validateDuplicateMedicine($attribute, $params)
    {
        if ($this->med_name && $this->strength) {
            $existingMedicine = self::find()
                ->where([
                    'med_name' => $this->med_name,
                    'strength' => $this->strength,
                ])
                ->andFilterWhere(['!=', 'med_id', $this->med_id])
                ->one();
            
            if ($existingMedicine) {
                $this->addError('med_name', '⚠️ This medicine with the same name and strength already exists (Medicine ID: ' . $existingMedicine->med_id . ').');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'med_id' => 'Medicine ID',
            'med_name' => 'Medicine Name',
            'dosage_form' => 'Dosage Form',
            'strength' => 'Strength',
            'med_price' => 'Price',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getFormattedPrice()
    {
        return '₱' . number_format($this->med_price, 2);
    }

    public function getFullName()
    {
        return $this->med_name . ($this->strength ? ' (' . $this->strength . ')' : '') . ' - ' . $this->dosage_form;
    }

    public function getTblMedlines()
    {
        return $this->hasMany(TblMedline::class, ['med_id' => 'med_id']);
    }
}