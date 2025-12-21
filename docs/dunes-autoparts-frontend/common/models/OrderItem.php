<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * OrderItem Model - รายการสินค้าในคำสั่งซื้อ
 *
 * @property int $id
 * @property int $order_id
 * @property int $part_id
 * @property string $part_name
 * @property string $part_sku
 * @property string $part_type
 * @property float $unit_price
 * @property int $quantity
 * @property float|null $discount_percent
 * @property float|null $discount_amount
 * @property float $line_total
 * @property int|null $warranty_days
 * @property string|null $warranty_expires_at
 * @property string|null $notes
 * @property int|null $created_at
 *
 * @property Order $order
 * @property Part $part
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
            [['discount_amount'], 'default', 'value' => 0],
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

    /**
     * Get discount display
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount_amount ?? 0;
    }
}
