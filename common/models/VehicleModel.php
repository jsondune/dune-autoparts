<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * VehicleModel Model - รุ่นรถยนต์
 *
 * @property int $id
 * @property int $brand_id
 * @property string $name_th
 * @property string|null $name_en
 * @property string|null $generation
 * @property int|null $year_start
 * @property int|null $year_end
 * @property string|null $body_type
 * @property int $is_active
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property VehicleBrand $brand
 * @property EngineType[] $engineTypes
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
            [['brand_id', 'name_th', 'name_en'], 'required'],
            [['brand_id', 'year_start', 'year_end'], 'integer'],
            [['is_active'], 'boolean'],
            [['name_th', 'name_en'], 'string', 'max' => 100],
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
            'name_en' => 'ชื่อรุ่น (EN)',
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
        $name_th = $this->name_th;
        if ($this->generation) {
            $name_th .= ' ' . $this->generation;
        }
        if ($this->year_start && $this->year_end) {
            $name_th .= ' (' . $this->year_start . '-' . $this->year_end . ')';
        } elseif ($this->year_start) {
            $name_th .= ' (' . $this->year_start . '-)';
        }
        return $name_th;
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
