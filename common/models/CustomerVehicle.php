<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * CustomerVehicle Model - รถของลูกค้า
 *
 * @property int $id
 * @property int $customer_id
 * @property int $brand_id
 * @property int|null $model_id
 * @property int|null $engine_type_id
 * @property int|null $year
 * @property string|null $vin
 * @property string|null $license_plate
 * @property string|null $color
 * @property int|null $mileage
 * @property string|null $notes
 * @property int $is_primary
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Customer $customer
 * @property VehicleBrand $brand
 * @property VehicleModel $model
 * @property EngineType $engineType
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
            [['is_primary'], 'default', 'value' => 0],
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
        $parts = [];
        if ($this->brand) {
            $parts[] = $this->brand->name_th;
        }
        if ($this->model) {
            $parts[] = $this->model->name_en;
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
