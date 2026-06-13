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
 * @property TblAppointment $appt
 * @property TblBillItem[] $tblBillItems
 */
class TblBill extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_CANCELLED = 'cancelled';

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
            [['appt_id', 'payment_method', 'dr_fee', 'totalm_price', 'total_amount'], 'default', 'value' => null],
            [['payment_status'], 'default', 'value' => 'pending'],
            [['appt_id'], 'integer'],
            [['payment_status'], 'string'],
            [['dr_fee', 'totalm_price', 'total_amount'], 'number'],
            [['bill_date', 'created_at', 'updated_at'], 'safe'],
            [['payment_method'], 'string', 'max' => 50],
            ['payment_status', 'in', 'range' => array_keys(self::optsPaymentStatus())],
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
            'payment_method' => 'Payment Method',
            'dr_fee' => 'Dr Fee',
            'totalm_price' => 'Totalm Price',
            'total_amount' => 'Total Amount',
            'bill_date' => 'Bill Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[TblBillItems]].
     *
     * @return \yii\db\ActiveQuery|TblBillItemQuery
     */
    public function getTblBillItems()
    {
        return $this->hasMany(TblBillItem::class, ['bill_id' => 'bill_id']);
    }

    /**
     * {@inheritdoc}
     * @return TblBillQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TblBillQuery(get_called_class());
    }


    /**
     * column payment_status ENUM value labels
     * @return string[]
     */
    public static function optsPaymentStatus()
    {
        return [
            self::PAYMENT_STATUS_PENDING => 'pending',
            self::PAYMENT_STATUS_PARTIAL => 'partial',
            self::PAYMENT_STATUS_PAID => 'paid',
            self::PAYMENT_STATUS_REFUNDED => 'refunded',
            self::PAYMENT_STATUS_CANCELLED => 'cancelled',
        ];
    }

    /**
     * @return string
     */
    public function displayPaymentStatus()
    {
        return self::optsPaymentStatus()[$this->payment_status];
    }

    /**
     * @return bool
     */
    public function isPaymentStatusPending()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PENDING;
    }

    public function setPaymentStatusToPending()
    {
        $this->payment_status = self::PAYMENT_STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isPaymentStatusPartial()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PARTIAL;
    }

    public function setPaymentStatusToPartial()
    {
        $this->payment_status = self::PAYMENT_STATUS_PARTIAL;
    }

    /**
     * @return bool
     */
    public function isPaymentStatusPaid()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function setPaymentStatusToPaid()
    {
        $this->payment_status = self::PAYMENT_STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isPaymentStatusRefunded()
    {
        return $this->payment_status === self::PAYMENT_STATUS_REFUNDED;
    }

    public function setPaymentStatusToRefunded()
    {
        $this->payment_status = self::PAYMENT_STATUS_REFUNDED;
    }

    /**
     * @return bool
     */
    public function isPaymentStatusCancelled()
    {
        return $this->payment_status === self::PAYMENT_STATUS_CANCELLED;
    }

    public function setPaymentStatusToCancelled()
    {
        $this->payment_status = self::PAYMENT_STATUS_CANCELLED;
    }
}
