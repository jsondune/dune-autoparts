<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * VehicleBrand Model - ยี่ห้อรถยนต์
 */
class VehicleBrand extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%vehicle_brand}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_active'], 'boolean'],
            [['sort_order'], 'integer'],
            [['name', 'name_th'], 'string', 'max' => 100],
            [['logo'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 50],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่อยี่ห้อ (EN)',
            'name_th' => 'ชื่อยี่ห้อ (TH)',
            'logo' => 'โลโก้',
            'country' => 'ประเทศ',
            'is_active' => 'เปิดใช้งาน',
            'sort_order' => 'ลำดับ',
        ];
    }

    public function getVehicleModels()
    {
        return $this->hasMany(VehicleModel::class, ['brand_id' => 'id']);
    }

    public function getDisplayName()
    {
        return $this->name_th ? $this->name_th . ' (' . $this->name_th . ')' : $this->name_th;
    }

    public static function getList()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();
    }

    public static function getDropdownList()
    {
        return \yii\helpers\ArrayHelper::map(
            self::getList(),
            'id',
            'name'
        );
    }
}

/**
 * VehicleModel Model - รุ่นรถยนต์
 */
class VehicleModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%vehicle_model}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['brand_id', 'name'], 'required'],
            [['brand_id', 'year_start', 'year_end'], 'integer'],
            [['is_active'], 'boolean'],
            [['name', 'name_th'], 'string', 'max' => 100],
            [['generation', 'body_type'], 'string', 'max' => 50],
            [['brand_id'], 'exist', 'targetClass' => VehicleBrand::class, 'targetAttribute' => ['brand_id' => 'id']],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand_id' => 'ยี่ห้อ',
            'name' => 'ชื่อรุ่น (EN)',
            'name_th' => 'ชื่อรุ่น (TH)',
            'generation' => 'เจเนอเรชัน',
            'year_start' => 'ปีเริ่มต้น',
            'year_end' => 'ปีสิ้นสุด',
            'body_type' => 'ประเภทตัวถัง',
            'is_active' => 'เปิดใช้งาน',
        ];
    }

    public function getBrand()
    {
        return $this->hasOne(VehicleBrand::class, ['id' => 'brand_id']);
    }

    public function getEngineTypes()
    {
        return $this->hasMany(EngineType::class, ['model_id' => 'id']);
    }

    public function getDisplayName()
    {
        $name = $this->name;
        if ($this->generation) {
            $name .= ' ' . $this->generation;
        }
        if ($this->year_start && $this->year_end) {
            $name .= ' (' . $this->year_start . '-' . $this->year_end . ')';
        } elseif ($this->year_start) {
            $name .= ' (' . $this->year_start . '-)';
        }
        return $name;
    }

    public function getFullName()
    {
        return $this->brand->name_th . ' ' . $this->getDisplayName();
    }

    public static function getDropdownList($brandId = null)
    {
        $query = self::find()->where(['is_active' => true]);
        if ($brandId) {
            $query->andWhere(['brand_id' => $brandId]);
        }
        return \yii\helpers\ArrayHelper::map(
            $query->orderBy(['name_th' => SORT_ASC])->all(),
            'id',
            'displayName'
        );
    }
}

/**
 * EngineType Model - ประเภทเครื่องยนต์
 */
class EngineType extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%engine_type}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['model_id', 'engine_code'], 'required'],
            [['model_id', 'power_hp', 'torque_nm', 'year_start', 'year_end'], 'integer'],
            [['displacement'], 'number'],
            [['is_active'], 'boolean'],
            [['engine_code'], 'string', 'max' => 50],
            [['fuel_type'], 'string', 'max' => 20],
            [['model_id'], 'exist', 'targetClass' => VehicleModel::class, 'targetAttribute' => ['model_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_id' => 'รุ่นรถ',
            'engine_code' => 'รหัสเครื่องยนต์',
            'displacement' => 'ความจุ (ลิตร)',
            'fuel_type' => 'ประเภทเชื้อเพลิง',
            'power_hp' => 'แรงม้า (HP)',
            'torque_nm' => 'แรงบิด (Nm)',
            'year_start' => 'ปีเริ่มต้น',
            'year_end' => 'ปีสิ้นสุด',
            'is_active' => 'เปิดใช้งาน',
        ];
    }

    public function getModel()
    {
        return $this->hasOne(VehicleModel::class, ['id' => 'model_id']);
    }

    public function getDisplayName()
    {
        $name = $this->engine_code;
        if ($this->displacement) {
            $name .= ' (' . $this->displacement . 'L)';
        }
        return $name;
    }
}

/**
 * PartCategory Model - หมวดหมู่อะไหล่
 */
class PartCategory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%part_category}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['name', 'name_th', 'slug'], 'required'],
            [['description'], 'string'],
            [['parent_id', 'sort_order'], 'integer'],
            [['is_active'], 'boolean'],
            [['name', 'name_th', 'slug'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 50],
            [['image'], 'string', 'max' => 255],
            [['slug'], 'unique'],
            [['parent_id'], 'exist', 'targetClass' => self::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'หมวดหมู่หลัก',
            'name' => 'ชื่อหมวดหมู่ (EN)',
            'name_th' => 'ชื่อหมวดหมู่ (TH)',
            'slug' => 'Slug',
            'icon' => 'ไอคอน',
            'description' => 'รายละเอียด',
            'image' => 'รูปภาพ',
            'is_active' => 'เปิดใช้งาน',
            'sort_order' => 'ลำดับ',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(self::class, ['parent_id' => 'id']);
    }

    public function getParts()
    {
        return $this->hasMany(Part::class, ['category_id' => 'id']);
    }

    public function getPartsCount()
    {
        return $this->hasMany(Part::class, ['category_id' => 'id'])->count();
    }

    public function getDisplayName()
    {
        return $this->name_th . ' (' . $this->name_en . ')';
    }

    public static function getDropdownList($parentId = null)
    {
        $query = self::find()->where(['is_active' => true]);
        if ($parentId === false) {
            $query->andWhere(['parent_id' => null]);
        } elseif ($parentId) {
            $query->andWhere(['parent_id' => $parentId]);
        }
        return \yii\helpers\ArrayHelper::map(
            $query->orderBy(['sort_order' => SORT_ASC])->all(),
            'id',
            'displayName'
        );
    }

    public static function getHierarchicalList()
    {
        $categories = self::find()
            ->where(['is_active' => true, 'parent_id' => null])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($categories as $category) {
            $result[$category->id] = $category->displayName;
            foreach ($category->children as $child) {
                $result[$child->id] = '— ' . $child->displayName;
            }
        }
        return $result;
    }
}

/**
 * Supplier Model - ซัพพลายเออร์
 */
class Supplier extends ActiveRecord
{
    const TYPE_LOCAL = 'local';
    const TYPE_JAPAN = 'japan';
    const TYPE_EUROPE = 'europe';

    public static function tableName()
    {
        return '{{%supplier}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['code', 'name', 'supplier_type'], 'required'],
            [['address', 'notes'], 'string'],
            [['is_active'], 'boolean'],
            [['code'], 'string', 'max' => 20],
            [['name', 'email'], 'string', 'max' => 255],
            [['contact_name', 'payment_terms'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
            [['country'], 'string', 'max' => 50],
            [['supplier_type'], 'string', 'max' => 20],
            [['code'], 'unique'],
            [['email'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'รหัสซัพพลายเออร์',
            'name' => 'ชื่อ',
            'contact_name' => 'ผู้ติดต่อ',
            'phone' => 'เบอร์โทร',
            'email' => 'อีเมล',
            'address' => 'ที่อยู่',
            'country' => 'ประเทศ',
            'supplier_type' => 'ประเภท',
            'payment_terms' => 'เงื่อนไขการชำระเงิน',
            'notes' => 'หมายเหตุ',
            'is_active' => 'เปิดใช้งาน',
        ];
    }

    public static function getSupplierTypes()
    {
        return [
            self::TYPE_LOCAL => 'ในประเทศ',
            self::TYPE_JAPAN => 'ญี่ปุ่น',
            self::TYPE_EUROPE => 'ยุโรป',
        ];
    }

    public function getSupplierTypeLabel()
    {
        return self::getSupplierTypes()[$this->supplier_type] ?? $this->supplier_type;
    }

    public function getParts()
    {
        return $this->hasMany(Part::class, ['supplier_id' => 'id']);
    }

    public static function getDropdownList()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['is_active' => true])->orderBy(['name' => SORT_ASC])->all(),
            'id',
            'name'
        );
    }
}

/**
 * OrderItem Model - รายการสินค้าในคำสั่งซื้อ
 */
class OrderItem extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_item}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['order_id', 'part_id', 'part_name', 'part_sku', 'part_type', 'unit_price', 'line_total'], 'required'],
            [['order_id', 'part_id', 'quantity', 'warranty_days'], 'integer'],
            [['unit_price', 'discount_percent', 'discount_amount', 'line_total'], 'number'],
            [['warranty_expires_at'], 'safe'],
            [['part_name'], 'string', 'max' => 255],
            [['part_sku'], 'string', 'max' => 50],
            [['part_type'], 'string', 'max' => 20],
            [['notes'], 'string', 'max' => 255],
            [['quantity'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'ออเดอร์',
            'part_id' => 'สินค้า',
            'part_name' => 'ชื่อสินค้า',
            'part_sku' => 'SKU',
            'part_type' => 'ประเภท',
            'unit_price' => 'ราคาต่อหน่วย',
            'quantity' => 'จำนวน',
            'discount_percent' => 'ส่วนลด (%)',
            'discount_amount' => 'ส่วนลด (บาท)',
            'line_total' => 'รวม',
            'warranty_days' => 'ประกัน (วัน)',
            'warranty_expires_at' => 'หมดประกัน',
            'notes' => 'หมายเหตุ',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->calculateLineTotal();
            return true;
        }
        return false;
    }

    public function calculateLineTotal()
    {
        $subtotal = $this->unit_price * $this->quantity;
        
        if ($this->discount_percent > 0) {
            $this->discount_amount = $subtotal * ($this->discount_percent / 100);
        }
        
        $this->line_total = $subtotal - ($this->discount_amount ?? 0);
    }

    public function setWarrantyExpiration()
    {
        if ($this->warranty_days > 0) {
            $this->warranty_expires_at = date('Y-m-d', strtotime("+{$this->warranty_days} days"));
        }
    }
}

/**
 * PartVehicle Model - ความเข้ากันได้ระหว่างอะไหล่กับรถ
 */
class PartVehicle extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%part_vehicle}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['part_id', 'brand_id'], 'required'],
            [['part_id', 'brand_id', 'model_id', 'engine_type_id', 'year_start', 'year_end'], 'integer'],
            [['notes'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'part_id' => 'อะไหล่',
            'brand_id' => 'ยี่ห้อรถ',
            'model_id' => 'รุ่นรถ',
            'engine_type_id' => 'เครื่องยนต์',
            'year_start' => 'ปีเริ่มต้น',
            'year_end' => 'ปีสิ้นสุด',
            'notes' => 'หมายเหตุ',
        ];
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    public function getBrand()
    {
        return $this->hasOne(VehicleBrand::class, ['id' => 'brand_id']);
    }

    public function getModel()
    {
        return $this->hasOne(VehicleModel::class, ['id' => 'model_id']);
    }

    public function getEngineType()
    {
        return $this->hasOne(EngineType::class, ['id' => 'engine_type_id']);
    }

    public function getCompatibilityText()
    {
        $parts = [$this->brand->name_th];
        
        if ($this->model) {
            $parts[] = $this->model->name_th;
            if ($this->model->generation) {
                $parts[] = $this->model->generation;
            }
        }
        
        if ($this->engineType) {
            $parts[] = $this->engineType->engine_code;
        }
        
        if ($this->year_start && $this->year_end) {
            $parts[] = '(' . $this->year_start . '-' . $this->year_end . ')';
        } elseif ($this->year_start) {
            $parts[] = '(' . $this->year_start . '-)';
        }
        
        return implode(' ', $parts);
    }
}

/**
 * StockMovement Model - การเคลื่อนไหวสต็อก
 */
class StockMovement extends ActiveRecord
{
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_RETURN = 'return';
    const TYPE_DAMAGED = 'damaged';

    public static function tableName()
    {
        return '{{%stock_movement}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['part_id', 'movement_type', 'quantity', 'quantity_before', 'quantity_after'], 'required'],
            [['part_id', 'quantity', 'quantity_before', 'quantity_after', 'reference_id', 'created_by'], 'integer'],
            [['unit_cost'], 'number'],
            [['notes'], 'string'],
            [['movement_type'], 'string', 'max' => 20],
            [['reference_type'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'part_id' => 'สินค้า',
            'movement_type' => 'ประเภท',
            'quantity' => 'จำนวน',
            'quantity_before' => 'ก่อนหน้า',
            'quantity_after' => 'หลังจาก',
            'reference_type' => 'อ้างอิงจาก',
            'reference_id' => 'รหัสอ้างอิง',
            'unit_cost' => 'ราคาต่อหน่วย',
            'notes' => 'หมายเหตุ',
            'created_by' => 'ทำโดย',
            'created_at' => 'วันที่',
        ];
    }

    public static function getMovementTypes()
    {
        return [
            self::TYPE_IN => 'รับเข้า',
            self::TYPE_OUT => 'จ่ายออก',
            self::TYPE_ADJUSTMENT => 'ปรับปรุง',
            self::TYPE_RETURN => 'รับคืน',
            self::TYPE_DAMAGED => 'ชำรุด/เสียหาย',
        ];
    }

    public function getMovementTypeLabel()
    {
        return self::getMovementTypes()[$this->movement_type] ?? $this->movement_type;
    }

    public function getMovementTypeBadge()
    {
        $badges = [
            self::TYPE_IN => '<span class="badge bg-success">รับเข้า</span>',
            self::TYPE_OUT => '<span class="badge bg-danger">จ่ายออก</span>',
            self::TYPE_ADJUSTMENT => '<span class="badge bg-warning">ปรับปรุง</span>',
            self::TYPE_RETURN => '<span class="badge bg-info">รับคืน</span>',
            self::TYPE_DAMAGED => '<span class="badge bg-dark">ชำรุด</span>',
        ];
        return $badges[$this->movement_type] ?? '<span class="badge bg-secondary">' . $this->movement_type . '</span>';
    }

    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}

/**
 * CustomerVehicle Model - รถของลูกค้า
 */
class CustomerVehicle extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%customer_vehicle}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['customer_id', 'brand_id'], 'required'],
            [['customer_id', 'brand_id', 'model_id', 'engine_type_id', 'year', 'mileage'], 'integer'],
            [['notes'], 'string'],
            [['is_primary'], 'boolean'],
            [['vin'], 'string', 'max' => 17],
            [['license_plate'], 'string', 'max' => 20],
            [['color'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'ลูกค้า',
            'brand_id' => 'ยี่ห้อ',
            'model_id' => 'รุ่น',
            'engine_type_id' => 'เครื่องยนต์',
            'year' => 'ปีรถ',
            'vin' => 'เลขตัวถัง (VIN)',
            'license_plate' => 'ทะเบียนรถ',
            'color' => 'สี',
            'mileage' => 'เลขไมล์',
            'notes' => 'หมายเหตุ',
            'is_primary' => 'รถหลัก',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getBrand()
    {
        return $this->hasOne(VehicleBrand::class, ['id' => 'brand_id']);
    }

    public function getModel()
    {
        return $this->hasOne(VehicleModel::class, ['id' => 'model_id']);
    }

    public function getEngineType()
    {
        return $this->hasOne(EngineType::class, ['id' => 'engine_type_id']);
    }

    public function getDisplayName()
    {
        $parts = [$this->brand->name_th];
        if ($this->model) {
            $parts[] = $this->model->name_th;
        }
        if ($this->year) {
            $parts[] = $this->year;
        }
        if ($this->license_plate) {
            $parts[] = '(' . $this->license_plate . ')';
        }
        return implode(' ', $parts);
    }
}

/**
 * Payment Model - การชำระเงิน
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
                'class' => \yii\behaviors\BlameableBehavior::class,
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
}
