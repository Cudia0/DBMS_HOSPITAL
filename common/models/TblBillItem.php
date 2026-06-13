<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_bill_item".
 *
 * @property int $bill_item_id
 * @property int $bill_id
 * @property string $item_type
 * @property string $description
 * @property int|null $reference_id
 * @property int $quantity
 * @property float $unit_price
 * @property float $total_price
 * @property string|null $created_at
 *
 * @property TblBill $bill
 */
class TblBillItem extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ITEM_TYPE_CONSULTATION = 'consultation';
    const ITEM_TYPE_MEDICINE = 'medicine';
    const ITEM_TYPE_LAB_TEST = 'lab_test';
    const ITEM_TYPE_PROCEDURE = 'procedure';
    const ITEM_TYPE_OTHER = 'other';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_bill_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reference_id'], 'default', 'value' => null],
            [['quantity'], 'default', 'value' => 1],
            [['bill_id', 'item_type', 'description', 'unit_price', 'total_price'], 'required'],
            [['bill_id', 'reference_id', 'quantity'], 'integer'],
            [['item_type'], 'string'],
            [['unit_price', 'total_price'], 'number'],
            [['created_at'], 'safe'],
            [['description'], 'string', 'max' => 255],
            ['item_type', 'in', 'range' => array_keys(self::optsItemType())],
            [['bill_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblBill::class, 'targetAttribute' => ['bill_id' => 'bill_id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_item_id' => 'Bill Item ID',
            'bill_id' => 'Bill ID',
            'item_type' => 'Item Type',
            'description' => 'Description',
            'reference_id' => 'Reference ID',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Bill]].
     *
     * @return \yii\db\ActiveQuery|TblBillQuery
     */
    public function getBill()
    {
        return $this->hasOne(TblBill::class, ['bill_id' => 'bill_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblBillItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblBillItemQuery(get_called_class());
    }


    /**
     * column item_type ENUM value labels
     * @return string[]
     */
    public static function optsItemType()
    {
        return [
            self::ITEM_TYPE_CONSULTATION => 'consultation',
            self::ITEM_TYPE_MEDICINE => 'medicine',
            self::ITEM_TYPE_LAB_TEST => 'lab_test',
            self::ITEM_TYPE_PROCEDURE => 'procedure',
            self::ITEM_TYPE_OTHER => 'other',
        ];
    }

    /**
     * @return string
     */
    public function displayItemType()
    {
        return self::optsItemType()[$this->item_type];
    }

    /**
     * @return bool
     */
    public function isItemTypeConsultation()
    {
        return $this->item_type === self::ITEM_TYPE_CONSULTATION;
    }

    public function setItemTypeToConsultation()
    {
        $this->item_type = self::ITEM_TYPE_CONSULTATION;
    }

    /**
     * @return bool
     */
    public function isItemTypeMedicine()
    {
        return $this->item_type === self::ITEM_TYPE_MEDICINE;
    }

    public function setItemTypeToMedicine()
    {
        $this->item_type = self::ITEM_TYPE_MEDICINE;
    }

    /**
     * @return bool
     */
    public function isItemTypeLabtest()
    {
        return $this->item_type === self::ITEM_TYPE_LAB_TEST;
    }

    public function setItemTypeToLabtest()
    {
        $this->item_type = self::ITEM_TYPE_LAB_TEST;
    }

    /**
     * @return bool
     */
    public function isItemTypeProcedure()
    {
        return $this->item_type === self::ITEM_TYPE_PROCEDURE;
    }

    public function setItemTypeToProcedure()
    {
        $this->item_type = self::ITEM_TYPE_PROCEDURE;
    }

    /**
     * @return bool
     */
    public function isItemTypeOther()
    {
        return $this->item_type === self::ITEM_TYPE_OTHER;
    }

    public function setItemTypeToOther()
    {
        $this->item_type = self::ITEM_TYPE_OTHER;
    }
}
