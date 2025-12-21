<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * ActivityLog model - บันทึกกิจกรรมในระบบ
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $model_class
 * @property int|null $model_id
 * @property string|null $description
 * @property string|null $old_values
 * @property string|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property int|null $created_at
 *
 * @property User $user
 */
class ActivityLog extends ActiveRecord
{
    // Action types
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_EXPORT = 'export';
    const ACTION_IMPORT = 'import';
    const ACTION_STATUS_CHANGE = 'status_change';
    const ACTION_STOCK_UPDATE = 'stock_update';
    const ACTION_PAYMENT = 'payment';
    const ACTION_ORDER_STATUS = 'order_status';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%activity_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['action'], 'required'],
            [['user_id', 'model_id', 'created_at'], 'integer'],
            [['description', 'old_values', 'new_values'], 'string'],
            [['action'], 'string', 'max' => 50],
            [['model_class'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ผู้ใช้',
            'action' => 'การกระทำ',
            'model_class' => 'ประเภทข้อมูล',
            'model_id' => 'รหัสข้อมูล',
            'description' => 'รายละเอียด',
            'old_values' => 'ค่าเดิม',
            'new_values' => 'ค่าใหม่',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'created_at' => 'วันที่บันทึก',
        ];
    }

    /**
     * Gets query for [[User]].
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Log an activity
     * 
     * @param string $action Action type
     * @param string|null $modelClass Model class name
     * @param int|null $modelId Model ID
     * @param string|null $description Description
     * @param array|null $oldValues Old values
     * @param array|null $newValues New values
     * @return bool
     */
    public static function log($action, $modelClass = null, $modelId = null, $description = null, $oldValues = null, $newValues = null)
    {
        $log = new self();
        $log->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $log->action = $action;
        $log->model_class = $modelClass;
        $log->model_id = $modelId;
        $log->description = $description;
        $log->old_values = $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null;
        $log->new_values = $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null;
        $log->ip_address = Yii::$app->request->userIP ?? null;
        $log->user_agent = isset(Yii::$app->request->userAgent) ? mb_substr(Yii::$app->request->userAgent, 0, 500) : null;
        
        return $log->save(false);
    }

    /**
     * Log model creation
     * @param ActiveRecord $model
     * @param string|null $description
     * @return bool
     */
    public static function logCreate($model, $description = null)
    {
        $modelClass = get_class($model);
        $shortName = (new \ReflectionClass($model))->getShortName();
        $desc = $description ?: "สร้าง {$shortName} #{$model->primaryKey}";
        
        return self::log(self::ACTION_CREATE, $modelClass, $model->primaryKey, $desc, null, $model->attributes);
    }

    /**
     * Log model update
     * @param ActiveRecord $model
     * @param array $oldAttributes
     * @param string|null $description
     * @return bool
     */
    public static function logUpdate($model, $oldAttributes, $description = null)
    {
        $modelClass = get_class($model);
        $shortName = (new \ReflectionClass($model))->getShortName();
        $desc = $description ?: "แก้ไข {$shortName} #{$model->primaryKey}";
        
        // Get only changed attributes
        $changedOld = [];
        $changedNew = [];
        foreach ($model->attributes as $attr => $newValue) {
            $oldValue = $oldAttributes[$attr] ?? null;
            if ($oldValue !== $newValue) {
                $changedOld[$attr] = $oldValue;
                $changedNew[$attr] = $newValue;
            }
        }
        
        return self::log(self::ACTION_UPDATE, $modelClass, $model->primaryKey, $desc, $changedOld, $changedNew);
    }

    /**
     * Log model deletion
     * @param ActiveRecord $model
     * @param string|null $description
     * @return bool
     */
    public static function logDelete($model, $description = null)
    {
        $modelClass = get_class($model);
        $shortName = (new \ReflectionClass($model))->getShortName();
        $desc = $description ?: "ลบ {$shortName} #{$model->primaryKey}";
        
        return self::log(self::ACTION_DELETE, $modelClass, $model->primaryKey, $desc, $model->attributes, null);
    }

    /**
     * Log user login
     * @param User $user
     * @return bool
     */
    public static function logLogin($user)
    {
        $log = new self();
        $log->user_id = $user->id;
        $log->action = self::ACTION_LOGIN;
        $log->model_class = User::class;
        $log->model_id = $user->id;
        $log->description = "ผู้ใช้ {$user->username} เข้าสู่ระบบ";
        $log->ip_address = Yii::$app->request->userIP ?? null;
        $log->user_agent = isset(Yii::$app->request->userAgent) ? mb_substr(Yii::$app->request->userAgent, 0, 500) : null;
        
        return $log->save(false);
    }

    /**
     * Log user logout
     * @return bool
     */
    public static function logLogout()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        
        $user = Yii::$app->user->identity;
        return self::log(self::ACTION_LOGOUT, User::class, $user->id, "ผู้ใช้ {$user->username} ออกจากระบบ");
    }

    /**
     * Get action list
     * @return array
     */
    public static function getActionList()
    {
        return [
            self::ACTION_LOGIN => 'เข้าสู่ระบบ',
            self::ACTION_LOGOUT => 'ออกจากระบบ',
            self::ACTION_CREATE => 'สร้าง',
            self::ACTION_UPDATE => 'แก้ไข',
            self::ACTION_DELETE => 'ลบ',
            self::ACTION_VIEW => 'ดู',
            self::ACTION_EXPORT => 'ส่งออก',
            self::ACTION_IMPORT => 'นำเข้า',
            self::ACTION_STATUS_CHANGE => 'เปลี่ยนสถานะ',
            self::ACTION_STOCK_UPDATE => 'ปรับสต็อก',
            self::ACTION_PAYMENT => 'ชำระเงิน',
            self::ACTION_ORDER_STATUS => 'เปลี่ยนสถานะคำสั่งซื้อ',
        ];
    }

    /**
     * Get action text
     * @return string
     */
    public function getActionText()
    {
        $list = self::getActionList();
        return $list[$this->action] ?? $this->action;
    }

    /**
     * Get action badge
     * @return string
     */
    public function getActionBadge()
    {
        $badges = [
            self::ACTION_LOGIN => '<span class="badge bg-success">เข้าสู่ระบบ</span>',
            self::ACTION_LOGOUT => '<span class="badge bg-secondary">ออกจากระบบ</span>',
            self::ACTION_CREATE => '<span class="badge bg-primary">สร้าง</span>',
            self::ACTION_UPDATE => '<span class="badge bg-info">แก้ไข</span>',
            self::ACTION_DELETE => '<span class="badge bg-danger">ลบ</span>',
            self::ACTION_VIEW => '<span class="badge bg-light text-dark">ดู</span>',
            self::ACTION_EXPORT => '<span class="badge bg-warning">ส่งออก</span>',
            self::ACTION_IMPORT => '<span class="badge bg-warning">นำเข้า</span>',
            self::ACTION_STATUS_CHANGE => '<span class="badge bg-info">เปลี่ยนสถานะ</span>',
            self::ACTION_STOCK_UPDATE => '<span class="badge bg-warning">ปรับสต็อก</span>',
            self::ACTION_PAYMENT => '<span class="badge bg-success">ชำระเงิน</span>',
            self::ACTION_ORDER_STATUS => '<span class="badge bg-info">สถานะคำสั่งซื้อ</span>',
        ];
        return $badges[$this->action] ?? '<span class="badge bg-secondary">' . $this->action . '</span>';
    }

    /**
     * Get decoded old values
     * @return array|null
     */
    public function getOldValuesArray()
    {
        return $this->old_values ? json_decode($this->old_values, true) : null;
    }

    /**
     * Get decoded new values
     * @return array|null
     */
    public function getNewValuesArray()
    {
        return $this->new_values ? json_decode($this->new_values, true) : null;
    }

    /**
     * Get model short name
     * @return string|null
     */
    public function getModelShortName()
    {
        if (!$this->model_class) {
            return null;
        }
        
        $parts = explode('\\', $this->model_class);
        return end($parts);
    }
}
