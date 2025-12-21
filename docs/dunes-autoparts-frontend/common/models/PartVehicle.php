<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * PartVehicle Model - ความเข้ากันได้ระหว่างอะไหล่กับรถ
 *
 * @property int $id
 * @property int $part_id
 * @property int $brand_id
 * @property int|null $model_id
 * @property int|null $engine_type_id
 * @property int|null $year_start
 * @property int|null $year_end
 * @property string|null $notes
 * @property int|null $created_at
 *
 * @property Part $part
 * @property VehicleBrand $brand
 * @property VehicleModel $model
 * @property EngineType $engineType
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
        $parts = [];
        
        if ($this->brand) {
            $parts[] = $this->brand->name_en;
        }
        
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
