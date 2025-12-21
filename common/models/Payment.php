<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * Payment Model - การชำระเงิน
 *
 * @property int $id
 * @property int $order_id
 * @property string $payment_date
 * @property float $amount
 * @property string $payment_method
 * @property string|null $reference_number
 * @property string|null $bank_name
 * @property string|null $slip_image
 * @property string|null $notes
 * @property int|null $verified_by
 * @property int|null $verified_at
 * @property string $status
 * @property int|null $created_by
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Order $order
 * @property User $verifiedByUser
 * @property User $createdByUser
 */
class Payment extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    public static function tableName()
    {
        return '{{%payment}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['order_id', 'payment_date', 'amount', 'payment_method'], 'required'],
            [['order_id', 'verified_by', 'verified_at', 'created_by'], 'integer'],
            [['payment_date'], 'safe'],
            [['amount'], 'number'],
            [['notes'], 'string'],
            [['payment_method', 'status'], 'string', 'max' => 50],
            [['reference_number', 'bank_name'], 'string', 'max' => 100],
            [['slip_image'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'ออเดอร์',
            'payment_date' => 'วันที่ชำระ',
            'amount' => 'จำนวนเงิน',
            'payment_method' => 'วิธีชำระ',
            'reference_number' => 'เลขอ้างอิง',
            'bank_name' => 'ธนาคาร',
            'slip_image' => 'สลิป',
            'notes' => 'หมายเหตุ',
            'verified_by' => 'ตรวจสอบโดย',
            'verified_at' => 'วันที่ตรวจสอบ',
            'status' => 'สถานะ',
            'created_by' => 'บันทึกโดย',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'รอตรวจสอบ',
            self::STATUS_VERIFIED => 'ตรวจสอบแล้ว',
            self::STATUS_REJECTED => 'ปฏิเสธ',
        ];
    }

    public function getStatusLabel()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadge()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">รอตรวจสอบ</span>',
            self::STATUS_VERIFIED => '<span class="badge bg-success">ตรวจสอบแล้ว</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">ปฏิเสธ</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getVerifiedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'verified_by']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function verify($userId)
    {
        $this->status = self::STATUS_VERIFIED;
        $this->verified_by = $userId;
        $this->verified_at = time();
        
        if ($this->save(false)) {
            $this->order->updatePaymentStatus();
            return true;
        }
        return false;
    }

    public function reject($userId, $reason = null)
    {
        $this->status = self::STATUS_REJECTED;
        $this->verified_by = $userId;
        $this->verified_at = time();
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . 'ปฏิเสธ: ' . $reason;
        }
        return $this->save(false);
    }

    /**
     * Get payment methods list
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            'bank_transfer' => 'โอนเงินผ่านธนาคาร',
            'cash' => 'เงินสด',
            'credit_card' => 'บัตรเครดิต',
            'promptpay' => 'พร้อมเพย์',
            'cod' => 'เก็บเงินปลายทาง',
        ];
    }
}
