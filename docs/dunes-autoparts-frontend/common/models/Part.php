<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Part Model - อะไหล่รถยนต์
 *
 * @property int $id
 * @property string $sku
 * @property string|null $oem_number
 * @property string $name
 * @property string $name_th
 * @property int $category_id
 * @property string|null $brand_manufacturer
 * @property string $part_type
 * @property string|null $condition_grade
 * @property string|null $origin_country
 * @property string|null $description
 * @property string|null $specifications
 * @property float|null $weight_kg
 * @property string|null $dimensions
 * @property float $cost_price
 * @property float $selling_price
 * @property float|null $discount_price
 * @property int|null $warranty_days
 * @property string|null $warranty_description
 * @property int $stock_quantity
 * @property int|null $min_stock_level
 * @property string|null $location
 * @property int|null $supplier_id
 * @property string|null $main_image
 * @property string|null $images
 * @property string|null $tags
 * @property int|null $view_count
 * @property int|null $sold_count
 * @property bool $is_featured
 * @property bool $is_active
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property PartCategory $category
 * @property Supplier $supplier
 * @property User $createdBy
 * @property User $updatedBy
 * @property PartVehicle[] $partVehicles
 * @property OrderItem[] $orderItems
 * @property StockMovement[] $stockMovements
 */
class Part extends ActiveRecord
{
    // Part Types
    const TYPE_NEW = 'new';
    const TYPE_USED_IMPORTED = 'used_imported';

    // Condition Grades (for used parts)
    const GRADE_A_PLUS = 'A+';
    const GRADE_A = 'A';
    const GRADE_B = 'B';
    const GRADE_C = 'C';

    // Origin Countries
    const ORIGIN_JAPAN = 'Japan';
    const ORIGIN_GERMANY = 'Germany';
    const ORIGIN_THAILAND = 'Thailand';
    const ORIGIN_KOREA = 'Korea';
    const ORIGIN_CHINA = 'China';
    const ORIGIN_USA = 'USA';

    /**
     * @var array Uploaded images
     */
    public $uploadedImages;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%part}}';
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
            [['sku', 'name', 'name_th', 'category_id', 'part_type', 'selling_price'], 'required'],
            [['category_id', 'warranty_days', 'stock_quantity', 'min_stock_level', 'supplier_id', 'view_count', 'sold_count', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['description', 'specifications', 'images'], 'string'],
            [['weight_kg', 'cost_price', 'selling_price', 'discount_price'], 'number'],
            [['is_featured', 'is_active'], 'boolean'],
            [['sku'], 'string', 'max' => 50],
            [['oem_number'], 'string', 'max' => 50],
            [['name', 'name_th', 'warranty_description', 'main_image'], 'string', 'max' => 255],
            [['brand_manufacturer', 'origin_country', 'location'], 'string', 'max' => 100],
            [['part_type'], 'string', 'max' => 20],
            [['condition_grade'], 'string', 'max' => 10],
            [['dimensions'], 'string', 'max' => 100],
            [['tags'], 'string', 'max' => 500],
            [['sku'], 'unique'],
            [['part_type'], 'in', 'range' => array_keys(self::getPartTypes())],
            [['condition_grade'], 'in', 'range' => array_keys(self::getConditionGrades()), 'when' => function ($model) {
                return $model->part_type === self::TYPE_USED_IMPORTED;
            }],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => Supplier::class, 'targetAttribute' => ['supplier_id' => 'id']],
            [['selling_price'], 'compare', 'compareAttribute' => 'cost_price', 'operator' => '>=', 'type' => 'number'],
            [['discount_price'], 'compare', 'compareAttribute' => 'selling_price', 'operator' => '<=', 'type' => 'number'],
            [['stock_quantity', 'min_stock_level'], 'integer', 'min' => 0],
            [['is_active'], 'default', 'value' => true],
            [['is_featured'], 'default', 'value' => false],
            [['stock_quantity'], 'default', 'value' => 0],
            [['view_count', 'sold_count'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku' => 'รหัสสินค้า (SKU)',
            'oem_number' => 'เลขอะไหล่แท้ (OEM)',
            'name' => 'ชื่อสินค้า (EN)',
            'name_th' => 'ชื่อสินค้า (TH)',
            'category_id' => 'หมวดหมู่',
            'brand_manufacturer' => 'ยี่ห้อผู้ผลิต',
            'part_type' => 'ประเภทสินค้า',
            'condition_grade' => 'เกรดสภาพ',
            'origin_country' => 'ประเทศต้นทาง',
            'description' => 'รายละเอียด',
            'specifications' => 'สเปคทางเทคนิค',
            'weight_kg' => 'น้ำหนัก (kg)',
            'dimensions' => 'ขนาด (กxยxส cm)',
            'cost_price' => 'ราคาทุน',
            'selling_price' => 'ราคาขาย',
            'discount_price' => 'ราคาลด',
            'warranty_days' => 'ระยะประกัน (วัน)',
            'warranty_description' => 'เงื่อนไขการประกัน',
            'stock_quantity' => 'จำนวนคงเหลือ',
            'min_stock_level' => 'จำนวนขั้นต่ำ',
            'location' => 'ตำแหน่งในคลัง',
            'supplier_id' => 'ซัพพลายเออร์',
            'main_image' => 'รูปหลัก',
            'images' => 'รูปเพิ่มเติม',
            'tags' => 'แท็ก',
            'view_count' => 'ยอดวิว',
            'sold_count' => 'ยอดขาย',
            'is_featured' => 'สินค้าแนะนำ',
            'is_active' => 'เปิดใช้งาน',
            'created_by' => 'สร้างโดย',
            'updated_by' => 'แก้ไขโดย',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    // =====================================================
    // Relations
    // =====================================================

    public function getCategory()
    {
        return $this->hasOne(PartCategory::class, ['id' => 'category_id']);
    }

    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id' => 'supplier_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getPartVehicles()
    {
        return $this->hasMany(PartVehicle::class, ['part_id' => 'id']);
    }

    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['part_id' => 'id']);
    }

    public function getStockMovements()
    {
        return $this->hasMany(StockMovement::class, ['part_id' => 'id']);
    }

    // =====================================================
    // Static Lists
    // =====================================================

    public static function getPartTypes()
    {
        return [
            self::TYPE_NEW => 'ของใหม่',
            self::TYPE_USED_IMPORTED => 'มือสองนำเข้า',
        ];
    }

    public static function getConditionGrades()
    {
        return [
            self::GRADE_A_PLUS => 'A+ (สภาพนางฟ้า)',
            self::GRADE_A => 'A (สภาพดีมาก)',
            self::GRADE_B => 'B (สภาพดี)',
            self::GRADE_C => 'C (ใช้งานได้)',
        ];
    }

    public static function getOriginCountries()
    {
        return [
            self::ORIGIN_JAPAN => 'ญี่ปุ่น',
            self::ORIGIN_GERMANY => 'เยอรมนี',
            self::ORIGIN_THAILAND => 'ไทย',
            self::ORIGIN_KOREA => 'เกาหลี',
            self::ORIGIN_CHINA => 'จีน',
            self::ORIGIN_USA => 'อเมริกา',
        ];
    }

    // =====================================================
    // Helper Methods
    // =====================================================

    /**
     * Get part type label
     */
    public function getPartTypeLabel()
    {
        return self::getPartTypes()[$this->part_type] ?? $this->part_type;
    }

    /**
     * Get condition grade label
     */
    public function getConditionGradeLabel()
    {
        return self::getConditionGrades()[$this->condition_grade] ?? $this->condition_grade;
    }

    /**
     * Get origin country label
     */
    public function getOriginCountryLabel()
    {
        return self::getOriginCountries()[$this->origin_country] ?? $this->origin_country;
    }

    /**
     * Get current price (discount if available)
     */
    public function getCurrentPrice()
    {
        return $this->discount_price > 0 ? $this->discount_price : $this->selling_price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercent()
    {
        if ($this->discount_price > 0 && $this->selling_price > 0) {
            return round((($this->selling_price - $this->discount_price) / $this->selling_price) * 100);
        }
        return 0;
    }

    /**
     * Check if in stock
     */
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Check if low stock
     */
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Get stock status label
     */
    public function getStockStatusLabel()
    {
        if ($this->stock_quantity <= 0) {
            return '<span class="badge bg-danger">หมดสต็อก</span>';
        }
        if ($this->isLowStock()) {
            return '<span class="badge bg-warning">ใกล้หมด</span>';
        }
        return '<span class="badge bg-success">มีสินค้า</span>';
    }

    /**
     * Get warranty text
     */
    public function getWarrantyText()
    {
        if ($this->warranty_days <= 0) {
            return 'ไม่มีประกัน';
        }
        if ($this->warranty_days >= 365) {
            $years = floor($this->warranty_days / 365);
            return $years . ' ปี';
        }
        if ($this->warranty_days >= 30) {
            $months = floor($this->warranty_days / 30);
            return $months . ' เดือน';
        }
        return $this->warranty_days . ' วัน';
    }

    /**
     * Get images array
     */
    public function getImagesArray()
    {
        if (empty($this->images)) {
            return [];
        }
        try {
            return Json::decode($this->images);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set images from array
     */
    public function setImagesArray($images)
    {
        $this->images = Json::encode($images);
    }

    /**
     * Get specifications array
     */
    public function getSpecificationsArray()
    {
        if (empty($this->specifications)) {
            return [];
        }
        try {
            return Json::decode($this->specifications);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Set specifications from array
     */
    public function setSpecificationsArray($specs)
    {
        $this->specifications = Json::encode($specs);
    }

    /**
     * Get main image URL
     */
    public function getMainImageUrl()
    {
        if (!empty($this->main_image)) {
            return Yii::getAlias('@web/uploads/parts/' . $this->main_image);
        }
        return Yii::getAlias('@web/images/no-image.png');
    }

    /**
     * Get tags array
     */
    public function getTagsArray()
    {
        if (empty($this->tags)) {
            return [];
        }
        return array_map('trim', explode(',', $this->tags));
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->updateCounters(['view_count' => 1]);
    }

    /**
     * Update stock (with movement logging)
     */
    public function updateStock($quantity, $movementType, $referenceType = null, $referenceId = null, $notes = null, $unitCost = null)
    {
        $oldQuantity = $this->stock_quantity;
        $newQuantity = $oldQuantity + $quantity;

        if ($newQuantity < 0) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->stock_quantity = $newQuantity;
            if (!$this->save(false, ['stock_quantity', 'updated_at'])) {
                throw new \Exception('Failed to update stock');
            }

            $movement = new StockMovement([
                'part_id' => $this->id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $newQuantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'unit_cost' => $unitCost ?? $this->cost_price,
                'notes' => $notes,
            ]);

            if (!$movement->save()) {
                throw new \Exception('Failed to create stock movement');
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
     * Generate SKU
     */
    public static function generateSku($categoryId, $partType)
    {
        $prefix = $partType === self::TYPE_NEW ? 'NEW' : 'USD';
        $category = PartCategory::findOne($categoryId);
        $categoryCode = $category ? strtoupper(substr($category->slug, 0, 3)) : 'GEN';

        $lastPart = self::find()
            ->where(['like', 'sku', $prefix . '-' . $categoryCode . '-'])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($lastPart) {
            preg_match('/(\d+)$/', $lastPart->sku, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . $categoryCode . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    // =====================================================
    // Scopes
    // =====================================================

    public static function find()
    {
        return new PartQuery(get_called_class());
    }
}

/**
 * Part Query Class
 */
class PartQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['is_active' => true]);
    }

    public function featured()
    {
        return $this->andWhere(['is_featured' => true]);
    }

    public function inStock()
    {
        return $this->andWhere(['>', 'stock_quantity', 0]);
    }

    public function lowStock()
    {
        return $this->andWhere('stock_quantity <= min_stock_level');
    }

    public function outOfStock()
    {
        return $this->andWhere(['stock_quantity' => 0]);
    }

    public function newParts()
    {
        return $this->andWhere(['part_type' => Part::TYPE_NEW]);
    }

    public function usedParts()
    {
        return $this->andWhere(['part_type' => Part::TYPE_USED_IMPORTED]);
    }

    public function byCategory($categoryId)
    {
        return $this->andWhere(['category_id' => $categoryId]);
    }

    public function byBrand($brandId)
    {
        return $this->joinWith('partVehicles')
            ->andWhere(['part_vehicle.brand_id' => $brandId]);
    }

    public function search($keyword)
    {
        return $this->andWhere([
            'or',
            ['like', 'name', $keyword],
            ['like', 'name_th', $keyword],
            ['like', 'sku', $keyword],
            ['like', 'oem_number', $keyword],
            ['like', 'tags', $keyword],
        ]);
    }
}
