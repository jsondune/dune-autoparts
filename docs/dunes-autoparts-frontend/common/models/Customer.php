<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Customer Model - ลูกค้า
 *
 * @property int $id
 * @property string $customer_code
 * @property string $customer_type
 * @property string $name
 * @property string|null $company_name
 * @property string|null $tax_id
 * @property string $phone
 * @property string|null $phone2
 * @property string|null $email
 * @property string|null $password_hash
 * @property string|null $auth_key
 * @property string|null $line_id
 * @property string|null $address
 * @property string|null $province
 * @property string|null $district
 * @property string|null $postal_code
 * @property string|null $shipping_address
 * @property string|null $notes
 * @property float|null $credit_limit
 * @property float|null $total_purchases
 * @property int|null $total_orders
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Order[] $orders
 * @property CustomerVehicle[] $customerVehicles
 * @property Inquiry[] $inquiries
 */
class Customer extends ActiveRecord implements IdentityInterface
{
    // Customer Types
    const TYPE_RETAIL = 'retail';
    const TYPE_WHOLESALE = 'wholesale';
    const TYPE_GARAGE = 'garage';
    
    // Status
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

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
            [['name'], 'required'],
            [['address', 'shipping_address', 'notes'], 'string'],
            [['credit_limit', 'total_purchases'], 'number'],
            [['total_orders', 'status'], 'integer'],
            [['customer_code'], 'string', 'max' => 20],
            [['customer_type'], 'string', 'max' => 20],
            [['name', 'company_name', 'email'], 'string', 'max' => 255],
            [['tax_id', 'phone', 'phone2'], 'string', 'max' => 20],
            [['line_id', 'province', 'district'], 'string', 'max' => 100],
            [['postal_code'], 'string', 'max' => 10],
            [['customer_code'], 'unique'],
            [['email'], 'email'],
            [['email'], 'unique', 'message' => 'อีเมลนี้ถูกใช้งานแล้ว'],
            [['customer_type'], 'default', 'value' => self::TYPE_RETAIL],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['credit_limit', 'total_purchases', 'total_orders'], 'default', 'value' => 0],
            [['phone'], 'string', 'max' => 20],
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
            'name' => 'ชื่อลูกค้า',
            'company_name' => 'ชื่อบริษัท/ร้าน',
            'tax_id' => 'เลขประจำตัวผู้เสียภาษี',
            'phone' => 'เบอร์โทรศัพท์',
            'phone2' => 'เบอร์โทรสำรอง',
            'email' => 'อีเมล',
            'line_id' => 'Line ID',
            'address' => 'ที่อยู่',
            'province' => 'จังหวัด',
            'district' => 'อำเภอ/เขต',
            'postal_code' => 'รหัสไปรษณีย์',
            'shipping_address' => 'ที่อยู่จัดส่ง',
            'notes' => 'หมายเหตุ',
            'credit_limit' => 'วงเงินเครดิต',
            'total_purchases' => 'ยอดซื้อสะสม',
            'total_orders' => 'จำนวนออเดอร์',
            'status' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    // =====================================================
    // IdentityInterface Implementation
    // =====================================================
    
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Sets password
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates auth key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Find by email
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
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
     * Get display name (company name if available)
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
    
    /**
     * Get type alias for customer_type
     */
    public function getType()
    {
        return $this->customer_type;
    }
    
    /**
     * Set type alias for customer_type
     */
    public function setType($value)
    {
        $this->customer_type = $value;
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
        return $this->andWhere(['status' => Customer::STATUS_ACTIVE]);
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
            ['like', 'name', $keyword],
            ['like', 'company_name', $keyword],
            ['like', 'phone', $keyword],
            ['like', 'line_id', $keyword],
            ['like', 'customer_code', $keyword],
        ]);
    }
}
