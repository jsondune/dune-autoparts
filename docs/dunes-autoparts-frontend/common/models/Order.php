<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * Order Model - คำสั่งซื้อ
 *
 * @property int $id
 * @property string $order_number
 * @property int $customer_id
 * @property string $order_date
 * @property string $status
 * @property string $payment_status
 * @property string|null $payment_method
 * @property float $subtotal
 * @property float|null $discount_amount
 * @property string|null $discount_reason
 * @property float|null $shipping_cost
 * @property float $total_amount
 * @property string|null $shipping_method
 * @property string|null $tracking_number
 * @property string|null $shipping_name
 * @property string|null $shipping_phone
 * @property string|null $shipping_address
 * @property string|null $customer_notes
 * @property string|null $internal_notes
 * @property int|null $shipped_at
 * @property int|null $delivered_at
 * @property int|null $cancelled_at
 * @property string|null $cancel_reason
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 * @property OrderItem[] $orderItems
 * @property Payment[] $payments
 * @property User $createdBy
 * @property User $updatedBy
 */
class Order extends ActiveRecord
{
    // Order Status
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    // Payment Status
    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

    // Payment Methods
    const METHOD_TRANSFER = 'transfer';
    const METHOD_COD = 'cod';
    const METHOD_CREDIT = 'credit';
    const METHOD_CASH = 'cash';

    // Shipping Methods
    const SHIP_KERRY = 'Kerry';
    const SHIP_FLASH = 'Flash';
    const SHIP_EMS = 'EMS';
    const SHIP_JT = 'J&T';
    const SHIP_PICKUP = 'pickup';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'order_date'], 'required'],
            [['customer_id', 'shipped_at', 'delivered_at', 'cancelled_at', 'created_by', 'updated_by'], 'integer'],
            [['order_date'], 'safe'],
            [['subtotal', 'discount_amount', 'shipping_cost', 'total_amount'], 'number'],
            [['shipping_address', 'customer_notes', 'internal_notes'], 'string'],
            [['order_number'], 'string', 'max' => 20],
            [['status', 'payment_status', 'payment_method'], 'string', 'max' => 50],
            [['discount_reason', 'shipping_name', 'cancel_reason'], 'string', 'max' => 255],
            [['shipping_method'], 'string', 'max' => 50],
            [['tracking_number'], 'string', 'max' => 100],
            [['shipping_phone'], 'string', 'max' => 20],
            [['order_number'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_PENDING],
            [['payment_status'], 'default', 'value' => self::PAYMENT_UNPAID],
            [['subtotal', 'discount_amount', 'shipping_cost', 'total_amount'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => 'เลขที่ออเดอร์',
            'customer_id' => 'ลูกค้า',
            'order_date' => 'วันที่สั่งซื้อ',
            'status' => 'สถานะออเดอร์',
            'payment_status' => 'สถานะชำระเงิน',
            'payment_method' => 'วิธีชำระเงิน',
            'subtotal' => 'ยอดรวมสินค้า',
            'discount_amount' => 'ส่วนลด',
            'discount_reason' => 'เหตุผลส่วนลด',
            'shipping_cost' => 'ค่าจัดส่ง',
            'total_amount' => 'ยอดรวมทั้งหมด',
            'shipping_method' => 'วิธีจัดส่ง',
            'tracking_number' => 'เลขพัสดุ',
            'shipping_name' => 'ชื่อผู้รับ',
            'shipping_phone' => 'เบอร์โทรผู้รับ',
            'shipping_address' => 'ที่อยู่จัดส่ง',
            'customer_notes' => 'หมายเหตุจากลูกค้า',
            'internal_notes' => 'หมายเหตุภายใน',
            'shipped_at' => 'วันที่จัดส่ง',
            'delivered_at' => 'วันที่ได้รับ',
            'cancelled_at' => 'วันที่ยกเลิก',
            'cancel_reason' => 'เหตุผลยกเลิก',
            'created_by' => 'สร้างโดย',
            'updated_by' => 'แก้ไขโดย',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->order_number)) {
                $this->order_number = $this->generateOrderNumber();
            }
            if ($insert && empty($this->order_date)) {
                $this->order_date = date('Y-m-d');
            }
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Update customer statistics when delivered
        if (isset($changedAttributes['status']) && $this->status === self::STATUS_DELIVERED) {
            $this->customer->updatePurchaseStats();
        }
    }

    // =====================================================
    // Relations
    // =====================================================

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['order_id' => 'id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    // =====================================================
    // Static Lists
    // =====================================================

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'รอยืนยัน',
            self::STATUS_CONFIRMED => 'ยืนยันแล้ว',
            self::STATUS_PREPARING => 'กำลังเตรียมสินค้า',
            self::STATUS_SHIPPED => 'จัดส่งแล้ว',
            self::STATUS_DELIVERED => 'รับสินค้าแล้ว',
            self::STATUS_CANCELLED => 'ยกเลิก',
        ];
    }

    public static function getPaymentStatuses()
    {
        return [
            self::PAYMENT_UNPAID => 'ยังไม่ชำระ',
            self::PAYMENT_PARTIAL => 'ชำระบางส่วน',
            self::PAYMENT_PAID => 'ชำระแล้ว',
            self::PAYMENT_REFUNDED => 'คืนเงินแล้ว',
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            self::METHOD_TRANSFER => 'โอนเงิน',
            self::METHOD_COD => 'เก็บเงินปลายทาง',
            self::METHOD_CREDIT => 'เครดิต',
            self::METHOD_CASH => 'เงินสด',
        ];
    }

    public static function getShippingMethods()
    {
        return [
            self::SHIP_KERRY => 'Kerry Express',
            self::SHIP_FLASH => 'Flash Express',
            self::SHIP_EMS => 'ไปรษณีย์ EMS',
            self::SHIP_JT => 'J&T Express',
            self::SHIP_PICKUP => 'รับที่ร้าน',
        ];
    }

    // =====================================================
    // Helper Methods
    // =====================================================

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning">รอยืนยัน</span>',
            self::STATUS_CONFIRMED => '<span class="badge bg-info">ยืนยันแล้ว</span>',
            self::STATUS_PREPARING => '<span class="badge bg-primary">กำลังเตรียม</span>',
            self::STATUS_SHIPPED => '<span class="badge bg-success">จัดส่งแล้ว</span>',
            self::STATUS_DELIVERED => '<span class="badge bg-success">สำเร็จ</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-danger">ยกเลิก</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabel()
    {
        return self::getPaymentStatuses()[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Get payment status badge
     */
    public function getPaymentStatusBadge()
    {
        $badges = [
            self::PAYMENT_UNPAID => '<span class="badge bg-danger">ยังไม่ชำระ</span>',
            self::PAYMENT_PARTIAL => '<span class="badge bg-warning">ชำระบางส่วน</span>',
            self::PAYMENT_PAID => '<span class="badge bg-success">ชำระแล้ว</span>',
            self::PAYMENT_REFUNDED => '<span class="badge bg-secondary">คืนเงินแล้ว</span>',
        ];
        return $badges[$this->payment_status] ?? '<span class="badge bg-secondary">' . $this->payment_status . '</span>';
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabel()
    {
        return self::getPaymentMethods()[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get shipping method label
     */
    public function getShippingMethodLabel()
    {
        return self::getShippingMethods()[$this->shipping_method] ?? $this->shipping_method;
    }

    /**
     * Generate order number
     */
    protected function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('ymd');

        $lastOrder = self::find()
            ->where(['like', 'order_number', $prefix . $date])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($lastOrder) {
            preg_match('/(\d+)$/', $lastOrder->order_number, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate totals
     */
    public function calculateTotals()
    {
        $subtotal = 0;
        foreach ($this->orderItems as $item) {
            $subtotal += $item->line_total;
        }

        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal - ($this->discount_amount ?? 0) + ($this->shipping_cost ?? 0);
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaid()
    {
        return Payment::find()
            ->where(['order_id' => $this->id, 'status' => 'verified'])
            ->sum('amount') ?? 0;
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmount()
    {
        return $this->total_amount - $this->getTotalPaid();
    }

    /**
     * Update payment status based on payments
     */
    public function updatePaymentStatus()
    {
        $totalPaid = $this->getTotalPaid();

        if ($totalPaid >= $this->total_amount) {
            $this->payment_status = self::PAYMENT_PAID;
        } elseif ($totalPaid > 0) {
            $this->payment_status = self::PAYMENT_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_UNPAID;
        }

        $this->save(false, ['payment_status', 'updated_at']);
    }

    /**
     * Confirm order
     */
    public function confirm()
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->status = self::STATUS_CONFIRMED;
        return $this->save(false, ['status', 'updated_at']);
    }

    /**
     * Mark as preparing
     */
    public function markPreparing()
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return false;
        }

        $this->status = self::STATUS_PREPARING;
        return $this->save(false, ['status', 'updated_at']);
    }

    /**
     * Ship order
     */
    public function ship($trackingNumber = null, $shippingMethod = null)
    {
        if ($this->status !== self::STATUS_PREPARING) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Deduct stock
            foreach ($this->orderItems as $item) {
                $part = $item->part;
                if (!$part->updateStock(
                    -$item->quantity,
                    'out',
                    'order',
                    $this->id,
                    'ขายตามออเดอร์ ' . $this->order_number
                )) {
                    throw new \Exception('Failed to update stock for part: ' . $part->sku);
                }

                // Update sold count
                $part->updateCounters(['sold_count' => $item->quantity]);
            }

            $this->status = self::STATUS_SHIPPED;
            $this->shipped_at = time();
            if ($trackingNumber) {
                $this->tracking_number = $trackingNumber;
            }
            if ($shippingMethod) {
                $this->shipping_method = $shippingMethod;
            }

            if (!$this->save(false)) {
                throw new \Exception('Failed to save order');
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Mark as delivered
     */
    public function markDelivered()
    {
        if ($this->status !== self::STATUS_SHIPPED) {
            return false;
        }

        $this->status = self::STATUS_DELIVERED;
        $this->delivered_at = time();
        return $this->save(false, ['status', 'delivered_at', 'updated_at']);
    }

    /**
     * Cancel order
     */
    public function cancel($reason = null)
    {
        if (in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED])) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Restore stock if already shipped
            if ($this->status === self::STATUS_SHIPPED) {
                foreach ($this->orderItems as $item) {
                    $part = $item->part;
                    if (!$part->updateStock(
                        $item->quantity,
                        'return',
                        'order',
                        $this->id,
                        'คืนสต็อกจากการยกเลิกออเดอร์ ' . $this->order_number
                    )) {
                        throw new \Exception('Failed to restore stock for part: ' . $part->sku);
                    }

                    // Revert sold count
                    $part->updateCounters(['sold_count' => -$item->quantity]);
                }
            }

            $this->status = self::STATUS_CANCELLED;
            $this->cancelled_at = time();
            $this->cancel_reason = $reason;

            if (!$this->save(false)) {
                throw new \Exception('Failed to save order');
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Can be cancelled
     */
    public function canBeCancelled()
    {
        return !in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    /**
     * Get item count
     */
    public function getItemCount()
    {
        return count($this->orderItems);
    }

    /**
     * Get total quantity
     */
    public function getTotalQuantity()
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item->quantity;
        }
        return $total;
    }

    // =====================================================
    // Scopes
    // =====================================================

    public static function find()
    {
        return new OrderQuery(get_called_class());
    }
}

/**
 * Order Query Class
 */
class OrderQuery extends \yii\db\ActiveQuery
{
    public function pending()
    {
        return $this->andWhere(['status' => Order::STATUS_PENDING]);
    }

    public function confirmed()
    {
        return $this->andWhere(['status' => Order::STATUS_CONFIRMED]);
    }

    public function preparing()
    {
        return $this->andWhere(['status' => Order::STATUS_PREPARING]);
    }

    public function shipped()
    {
        return $this->andWhere(['status' => Order::STATUS_SHIPPED]);
    }

    public function delivered()
    {
        return $this->andWhere(['status' => Order::STATUS_DELIVERED]);
    }

    public function cancelled()
    {
        return $this->andWhere(['status' => Order::STATUS_CANCELLED]);
    }

    public function unpaid()
    {
        return $this->andWhere(['payment_status' => Order::PAYMENT_UNPAID]);
    }

    public function paid()
    {
        return $this->andWhere(['payment_status' => Order::PAYMENT_PAID]);
    }

    public function today()
    {
        return $this->andWhere(['order_date' => date('Y-m-d')]);
    }

    public function thisMonth()
    {
        return $this->andWhere([
            'between',
            'order_date',
            date('Y-m-01'),
            date('Y-m-t')
        ]);
    }

    public function byCustomer($customerId)
    {
        return $this->andWhere(['customer_id' => $customerId]);
    }

    public function search($keyword)
    {
        return $this->joinWith('customer')
            ->andWhere([
                'or',
                ['like', 'order.order_number', $keyword],
                ['like', 'customer.name', $keyword],
                ['like', 'customer.phone', $keyword],
                ['like', 'order.tracking_number', $keyword],
            ]);
    }
}
