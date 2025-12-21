<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Customer Model - ลูกค้า
 *
 * @property int $id
 * @property string $customer_code
 * @property string $customer_type
 * @property string $full_name
 * @property string|null $company_name
 * @property string|null $tax_id
 * @property string $phone
 * @property string|null $phone2
 * @property string|null $email
 * @property string|null $line_id
 * @property string|null $facebook
 * @property string|null $address
 * @property string|null $province
 * @property string|null $district
 * @property string|null $postal_code
 * @property string|null $shipping_address
 * @property string|null $notes
 * @property float|null $discount_percent
 * @property float|null $credit_limit
 * @property float|null $total_purchases
 * @property int|null $total_orders
 * @property bool $is_active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Order[] $orders
 * @property CustomerVehicle[] $customerVehicles
 * @property Inquiry[] $inquiries
 */
class Customer extends ActiveRecord
{
    // Customer Types
    const TYPE_RETAIL = 'retail';
    const TYPE_WHOLESALE = 'wholesale';
    const TYPE_GARAGE = 'garage';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'phone'], 'required'],
            [['address', 'shipping_address', 'notes'], 'string'],
            [['discount_percent', 'credit_limit', 'total_purchases'], 'number'],
            [['total_orders'], 'integer'],
            [['is_active'], 'boolean'],
            [['customer_code'], 'string', 'max' => 20],
            [['customer_type'], 'string', 'max' => 20],
            [['full_name', 'company_name', 'email', 'facebook'], 'string', 'max' => 255],
            [['tax_id', 'phone', 'phone2'], 'string', 'max' => 20],
            [['line_id', 'province', 'district'], 'string', 'max' => 100],
            [['postal_code'], 'string', 'max' => 10],
            [['customer_code'], 'unique'],
            [['email'], 'email'],
            [['customer_type'], 'default', 'value' => self::TYPE_RETAIL],
            [['is_active'], 'default', 'value' => true],
            [['discount_percent', 'credit_limit', 'total_purchases', 'total_orders'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_code' => 'รหัสลูกค้า',
            'customer_type' => 'ประเภทลูกค้า',
            'full_name' => 'ชื่อ-นามสกุล',
            'company_name' => 'ชื่อบริษัท/ร้าน',
            'tax_id' => 'เลขประจำตัวผู้เสียภาษี',
            'phone' => 'เบอร์โทรศัพท์',
            'phone2' => 'เบอร์โทรสำรอง',
            'email' => 'อีเมล',
            'line_id' => 'Line ID',
            'facebook' => 'Facebook',            
            'address' => 'ที่อยู่',
            'province' => 'จังหวัด',
            'district' => 'อำเภอ/เขต',
            'postal_code' => 'รหัสไปรษณีย์',
            'shipping_address' => 'ที่อยู่จัดส่ง',
            'notes' => 'หมายเหตุ',
            'discount_percent' => 'ส่วนลด',
            'credit_limit' => 'วงเงินเครดิต',
            'total_purchases' => 'ยอดซื้อสะสม',
            'total_orders' => 'จำนวนออเดอร์',
            'is_active' => 'สถานะ',
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
            if ($insert && empty($this->customer_code)) {
                $this->customer_code = $this->generateCustomerCode();
            }
            return true;
        }
        return false;
    }

    // =====================================================
    // Relations
    // =====================================================

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['customer_id' => 'id']);
    }

    public function getCustomerVehicles()
    {
        return $this->hasMany(CustomerVehicle::class, ['customer_id' => 'id']);
    }

    public function getInquiries()
    {
        return $this->hasMany(Inquiry::class, ['customer_id' => 'id']);
    }

    // =====================================================
    // Static Lists
    // =====================================================

    public static function getCustomerTypes()
    {
        return [
            self::TYPE_RETAIL => 'ลูกค้าทั่วไป',
            self::TYPE_WHOLESALE => 'ลูกค้าขายส่ง',
            self::TYPE_GARAGE => 'อู่/ร้านซ่อม',
        ];
    }

    // =====================================================
    // Helper Methods
    // =====================================================

    /**
     * Get customer type label
     */
    public function getCustomerTypeLabel()
    {
        return self::getCustomerTypes()[$this->customer_type] ?? $this->customer_type;
    }

    /**
     * Get customer type badge
     */
    public function getCustomerTypeBadge()
    {
        $badges = [
            self::TYPE_RETAIL => '<span class="badge bg-primary">ลูกค้าทั่วไป</span>',
            self::TYPE_WHOLESALE => '<span class="badge bg-success">ขายส่ง</span>',
            self::TYPE_GARAGE => '<span class="badge bg-info">อู่/ร้านซ่อม</span>',
        ];
        return $badges[$this->customer_type] ?? '<span class="badge bg-secondary">' . $this->customer_type . '</span>';
    }

    /**
     * Get full address
     */
    public function getFullAddress()
    {
        $parts = array_filter([
            $this->address,
            $this->district,
            $this->province,
            $this->postal_code
        ]);
        return implode(' ', $parts);
    }

    /**
     * Get shipping address or default to main address
     */
    public function getShippingAddressOrDefault()
    {
        return !empty($this->shipping_address) ? $this->shipping_address : $this->getFullAddress();
    }

    /**
     * Get display full name (company name if available)
     */
    public function getDisplayName()
    {
        if (!empty($this->company_name)) {
            return $this->company_name . ' (' . $this->full_name . ')';
        }
        return $this->full_name;
    }

    /**
     * Get primary vehicle
     */
    public function getPrimaryVehicle()
    {
        return $this->hasOne(CustomerVehicle::class, ['customer_id' => 'id'])
            ->andWhere(['is_primary' => true]);
    }

    /**
     * Generate customer code
     */
    protected function generateCustomerCode()
    {
        $prefix = match ($this->customer_type) {
            self::TYPE_WHOLESALE => 'WS',
            self::TYPE_GARAGE => 'GR',
            default => 'RT',
        };

        $year = date('y');
        $lastCustomer = self::find()
            ->where(['like', 'customer_code', $prefix . $year])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($lastCustomer) {
            preg_match('/(\d+)$/', $lastCustomer->customer_code, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update purchase statistics
     */
    public function updatePurchaseStats()
    {
        $stats = Order::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['status' => Order::STATUS_DELIVERED])
            ->select([
                'total' => 'SUM(total_amount)',
                'count' => 'COUNT(*)'
            ])
            ->asArray()
            ->one();

        $this->total_purchases = $stats['total'] ?? 0;
        $this->total_orders = $stats['count'] ?? 0;
        $this->save(false, ['total_purchases', 'total_orders', 'updated_at']);
    }

    /**
     * Check if customer has credit available
     */
    public function hasCreditAvailable($amount)
    {
        if ($this->credit_limit <= 0) {
            return false;
        }

        $pendingAmount = Order::find()
            ->where(['customer_id' => $this->id])
            ->andWhere(['payment_status' => [Order::PAYMENT_UNPAID, Order::PAYMENT_PARTIAL]])
            ->sum('total_amount') ?? 0;

        return ($this->credit_limit - $pendingAmount) >= $amount;
    }

    // =====================================================
    // Scopes
    // =====================================================

    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }
}

/**
 * Customer Query Class
 */
class CustomerQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['is_active' => true]);
    }

    public function retail()
    {
        return $this->andWhere(['customer_type' => Customer::TYPE_RETAIL]);
    }

    public function wholesale()
    {
        return $this->andWhere(['customer_type' => Customer::TYPE_WHOLESALE]);
    }

    public function garage()
    {
        return $this->andWhere(['customer_type' => Customer::TYPE_GARAGE]);
    }

    public function search($keyword)
    {
        return $this->andWhere([
            'or',
            ['like', 'full_name', $keyword],
            ['like', 'company_name', $keyword],
            ['like', 'phone', $keyword],
            ['like', 'line_id', $keyword],
            ['like', 'facebook', $keyword],            
            ['like', 'customer_code', $keyword],
        ]);
    }
}
