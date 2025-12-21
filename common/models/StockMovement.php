<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * StockMovement Model - การเคลื่อนไหวสต็อก
 *
 * @property int $id
 * @property int $part_id
 * @property string $movement_type
 * @property int $quantity
 * @property int $quantity_before
 * @property int $quantity_after
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property float|null $unit_cost
 * @property string|null $notes
 * @property int|null $created_by
 * @property int|null $created_at
 *
 * @property Part $part
 * @property User $createdByUser
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
                'class' => BlameableBehavior::class,
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

    /**
     * Record a stock movement
     * 
     * @param int $partId
     * @param string $type
     * @param int $quantity
     * @param int $before
     * @param int $after
     * @param string|null $referenceType
     * @param int|null $referenceId
     * @param float|null $unitCost
     * @param string|null $notes
     * @return StockMovement|null
     */
    public static function record($partId, $type, $quantity, $before, $after, $referenceType = null, $referenceId = null, $unitCost = null, $notes = null)
    {
        $movement = new self();
        $movement->part_id = $partId;
        $movement->movement_type = $type;
        $movement->quantity = $quantity;
        $movement->quantity_before = $before;
        $movement->quantity_after = $after;
        $movement->reference_type = $referenceType;
        $movement->reference_id = $referenceId;
        $movement->unit_cost = $unitCost;
        $movement->notes = $notes;
        
        if ($movement->save()) {
            return $movement;
        }
        
        return null;
    }
}
