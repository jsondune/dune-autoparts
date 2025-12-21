<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Supplier Model - ซัพพลายเออร์
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $contact_name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $country
 * @property string $supplier_type
 * @property string|null $payment_terms
 * @property string|null $notes
 * @property int $is_active
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Part[] $parts
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
            [['is_active'], 'default', 'value' => 1],
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
