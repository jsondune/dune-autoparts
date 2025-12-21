<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Setting Model - การตั้งค่าระบบ
 *
 * @property int $id
 * @property string $setting_group
 * @property string $setting_key
 * @property string $setting_value
 * @property string $setting_type
 * @property string $setting_label
 * @property string $setting_description
 * @property int $is_system
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 */
class Setting extends ActiveRecord
{
    // ประเภทการตั้งค่า
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_SELECT = 'select';
    const TYPE_EMAIL = 'email';
    const TYPE_URL = 'url';
    const TYPE_PASSWORD = 'password';
    const TYPE_TIME = 'time';
    const TYPE_DATE = 'date';
    const TYPE_JSON = 'json';
    const TYPE_IMAGE = 'image';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['setting_group', 'setting_key', 'setting_label'], 'required'],
            [['setting_value', 'setting_description'], 'string'],
            [['is_system', 'sort_order'], 'integer'],
            [['setting_group', 'setting_key', 'setting_type'], 'string', 'max' => 50],
            [['setting_label'], 'string', 'max' => 255],
            [['setting_key'], 'unique'],
            [['setting_type'], 'default', 'value' => self::TYPE_TEXT],
            [['is_system'], 'default', 'value' => 0],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'setting_group' => 'กลุ่ม',
            'setting_key' => 'Key',
            'setting_value' => 'ค่า',
            'setting_type' => 'ประเภท',
            'setting_label' => 'ชื่อการตั้งค่า',
            'setting_description' => 'คำอธิบาย',
            'is_system' => 'การตั้งค่าระบบ',
            'sort_order' => 'ลำดับ',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
        ];
    }

    /**
     * ดึงค่าการตั้งค่าตาม Key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::findOne(['setting_key' => $key]);
        if (!$setting) {
            return $default;
        }
        
        // แปลงค่าตามประเภท
        switch ($setting->setting_type) {
            case self::TYPE_BOOLEAN:
                return (bool) $setting->setting_value;
            case self::TYPE_NUMBER:
                return is_numeric($setting->setting_value) ? (float) $setting->setting_value : $default;
            case self::TYPE_JSON:
                $decoded = json_decode($setting->setting_value, true);
                return $decoded !== null ? $decoded : $default;
            default:
                return $setting->setting_value;
        }
    }

    /**
     * ตั้งค่าตาม Key
     */
    public static function setValue($key, $value, $group = 'general')
    {
        $setting = static::findOne(['setting_key' => $key]);
        
        if (!$setting) {
            $setting = new static();
            $setting->setting_key = $key;
            $setting->setting_group = $group;
            $setting->setting_label = $key;
        }
        
        if (is_array($value)) {
            $setting->setting_value = json_encode($value);
            $setting->setting_type = self::TYPE_JSON;
        } elseif (is_bool($value)) {
            $setting->setting_value = $value ? '1' : '0';
            $setting->setting_type = self::TYPE_BOOLEAN;
        } else {
            $setting->setting_value = (string) $value;
        }
        
        return $setting->save();
    }

    /**
     * ดึงการตั้งค่าทั้งหมดในกลุ่ม
     */
    public static function getGroup($group)
    {
        $settings = static::find()
            ->where(['setting_group' => $group])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->setting_key] = static::getValue($setting->setting_key);
        }
        
        return $result;
    }

    /**
     * รายการประเภทการตั้งค่า
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_TEXT => 'ข้อความ',
            self::TYPE_TEXTAREA => 'ข้อความหลายบรรทัด',
            self::TYPE_NUMBER => 'ตัวเลข',
            self::TYPE_BOOLEAN => 'ใช่/ไม่',
            self::TYPE_SELECT => 'เลือกรายการ',
            self::TYPE_EMAIL => 'อีเมล',
            self::TYPE_URL => 'URL',
            self::TYPE_PASSWORD => 'รหัสผ่าน',
            self::TYPE_TIME => 'เวลา',
            self::TYPE_DATE => 'วันที่',
            self::TYPE_JSON => 'JSON',
            self::TYPE_IMAGE => 'รูปภาพ',
        ];
    }

    /**
     * รายการกลุ่มการตั้งค่า
     */
    public static function getGroupList()
    {
        return [
            'general' => 'ทั่วไป',
            'shop' => 'ข้อมูลร้าน',
            'order' => 'คำสั่งซื้อ',
            'shipping' => 'การจัดส่ง',
            'payment' => 'การชำระเงิน',
            'notification' => 'การแจ้งเตือน',
            'line' => 'LINE OA',
            'system' => 'ระบบ',
        ];
    }

    /**
     * ดึงค่า JSON แบบ Array
     */
    public function getJsonValue()
    {
        if ($this->setting_type !== self::TYPE_JSON) {
            return null;
        }
        return json_decode($this->setting_value, true) ?: [];
    }

    /**
     * ตั้งค่า JSON จาก Array
     */
    public function setJsonValue($value)
    {
        $this->setting_value = json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
