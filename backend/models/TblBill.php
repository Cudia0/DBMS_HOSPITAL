<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_bill".
 *
 * @property int $bill_id
 * @property int|null $appt_id
 * @property string|null $payment_status
 * @property int|null $qty
 * @property float|null $dr_fee
 * @property float|null $totalm_price
 * @property float|null $total_amount
 *
 * @property TblAppointment $appt
 */
class TblBill extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_bill';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appt_id', 'payment_status', 'qty', 'dr_fee', 'totalm_price', 'total_amount'], 'default', 'value' => null],
            [['appt_id', 'qty'], 'integer'],
            [['dr_fee', 'totalm_price', 'total_amount'], 'number'],
            [['payment_status'], 'string', 'max' => 50],
            [['appt_id'], 'exist', 'skipOnError' => true, 'targetClass' => TblAppointment::class, 'targetAttribute' => ['appt_id' => 'appt_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bill_id' => 'Bill ID',
            'appt_id' => 'Appt ID',
            'payment_status' => 'Payment Status',
            'qty' => 'Qty',
            'dr_fee' => 'Dr Fee',
            'totalm_price' => 'Totalm Price',
            'total_amount' => 'Total Amount',
        ];
    }

    /**
     * Gets query for [[Appt]].
     *
     * @return \yii\db\ActiveQuery|TblAppointmentQuery
     */
    public function getAppt()
    {
        return $this->hasOne(TblAppointment::class, ['appt_id' => 'appt_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblBillQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblBillQuery(get_called_class());
    }

}
