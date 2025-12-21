<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * PartCategory Model - หมวดหมู่อะไหล่
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $name_en
 * @property string $name_th
 * @property string|null $description
 * @property string|null $icon
 * @property int $is_active
 * @property int|null $sort_order
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property PartCategory $parent
 * @property PartCategory[] $children
 * @property Part[] $parts
 */
class PartCategory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%part_category}}';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['name_th'], 'required'],
            [['description'], 'string'],
            [['parent_id', 'sort_order'], 'integer'],
            [['is_active'], 'boolean'],
            [['name_en', 'name_th'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 50],
            [['parent_id'], 'exist', 'targetClass' => self::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['is_active'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'หมวดหมู่หลัก',
            'name_en' => 'ชื่อหมวดหมู่ (EN)',
            'name_th' => 'ชื่อหมวดหมู่ (TH)',
            'icon' => 'ไอคอน',
            'description' => 'รายละเอียด',
            'is_active' => 'เปิดใช้งาน',
            'sort_order' => 'ลำดับ',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(self::class, ['parent_id' => 'id']);
    }

    public function getParts()
    {
        return $this->hasMany(Part::class, ['category_id' => 'id']);
    }

    public function getPartsCount()
    {
        return $this->hasMany(Part::class, ['category_id' => 'id'])->count();
    }

    public function getDisplayName()
    {
        if ($this->name_en) {
            return $this->name_th . ' (' . $this->name_en . ')';
        }
        return $this->name_th;
    }

    public static function getDropdownList($parentId = null)
    {
        $query = self::find()->where(['is_active' => true]);
        if ($parentId === false) {
            $query->andWhere(['parent_id' => null]);
        } elseif ($parentId) {
            $query->andWhere(['parent_id' => $parentId]);
        }
        return \yii\helpers\ArrayHelper::map(
            $query->orderBy(['sort_order' => SORT_ASC])->all(),
            'id',
            function($model) {
                return $model->name_th;
            }
        );
    }

    public static function getHierarchicalList()
    {
        $categories = self::find()
            ->where(['is_active' => true, 'parent_id' => null])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($categories as $category) {
            $result[$category->id] = $category->name_th;
            foreach ($category->children as $child) {
                $result[$child->id] = '— ' . $child->name_th;
            }
        }
        return $result;
    }
}
