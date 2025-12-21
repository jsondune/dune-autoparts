<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * VehicleBrand Model - ยี่ห้อรถยนต์
 *
 * @property int $id
 * @property string $name_th
 * @property string|null $name_en
 * @property string|null $logo
 * @property string|null $country
 * @property int $is_active
 * @property int|null $sort_order
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property VehicleModel[] $vehicleModels
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
            [['name_th'], 'required'],
            [['is_active'], 'boolean'],
            [['sort_order'], 'integer'],
            [['name_th', 'name_en'], 'string', 'max' => 100],
            [['logo'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 50],
            [['is_active'], 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_th' => 'ชื่อยี่ห้อ (TH)',            
            'name_en' => 'ชื่อยี่ห้อ (EN)',
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
        return $this->name_th ?: $this->name_en;
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
            function($model) {
                return $model->name_th ?: $model->name_en;
            }
        );
    }
}
