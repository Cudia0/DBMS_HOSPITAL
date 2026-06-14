<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tbl_bill".
 *
 * @property int $bill_id
 * @property int|null $appt_id
 * @property string|null $payment_status
 * @property string|null $payment_method
 * @property float|null $dr_fee
 * @property float|null $totalm_price
 * @property float|null $total_amount
 * @property string|null $bill_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property TblAppointment $appointment
 * @property TblBillItem[] $billItems
 */
class TblBill extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_bill';
    }

    public function rules()
    {
        return [
            [['appt_id'], 'integer'],
            [['dr_fee', 'totalm_price', 'total_amount'], 'number'],
            [['bill_date', 'created_at', 'updated_at'], 'safe'],
            [['payment_status'], 'string'],
            [['payment_method'], 'string', 'max' => 50],
            [['payment_status'], 'in', 'range' => ['pending', 'partial', 'paid', 'refunded', 'cancelled']],
            [['appt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblAppointment::class, 'targetAttribute' => ['appt_id' => 'appt_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'bill_id' => 'Bill ID',
            'appt_id' => 'Appointment',
            'payment_status' => 'Payment Status',
            'payment_method' => 'Payment Method',
            'dr_fee' => 'Doctor Fee',
            'totalm_price' => 'Medicine Total',
            'total_amount' => 'Total Amount',
            'bill_date' => 'Bill Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Appointment]].
     */
    public function getAppointment()
    {
        return $this->hasOne(TblAppointment::class, ['appt_id' => 'appt_id']);
    }

    /**
     * Gets query for [[BillItems]].
     */
    public function getBillItems()
    {
        return $this->hasMany(TblBillItem::class, ['bill_id' => 'bill_id']);
    }
}