<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * EngineType Model - ประเภทเครื่องยนต์
 *
 * @property int $id
 * @property int $model_id
 * @property string $engine_code
 * @property float|null $displacement
 * @property string|null $fuel_type
 * @property int|null $power_hp
 * @property int|null $torque_nm
 * @property int|null $year_start
 * @property int|null $year_end
 * @property int $is_active
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property VehicleModel $model
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
