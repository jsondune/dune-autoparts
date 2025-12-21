<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model - โมเดลผู้ใช้งานระบบ
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email_address
 * @property string|null $full_name
 * @property string|null $phone_number
 * @property string|null $line_id
 * @property string|null $department
 * @property string|null $avatar_file_path
 * @property string $role
 * @property int $user_status
 * @property string|null $notes
 * @property int|null $last_login_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property string $statusText
 * @property string $statusBadge
 * @property string $roleText
 * @property string $roleBadge
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const ROLE_USER = 'user';
    const ROLE_STAFF = 'staff';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMIN = 'admin';

    public $password;
    public $password_confirm;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_status', 'default', 'value' => self::STATUS_ACTIVE],
            ['user_status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            
            ['role', 'default', 'value' => self::ROLE_STAFF],
            ['role', 'in', 'range' => [self::ROLE_USER, self::ROLE_STAFF, self::ROLE_MANAGER, self::ROLE_ADMIN]],

            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษรภาษาอังกฤษ ตัวเลข หรือ _ เท่านั้น'],

            ['email_address', 'trim'],
            ['email_address', 'required'],
            ['email_address', 'email'],
            ['email_address', 'string', 'max' => 255],
            ['email_address', 'unique', 'targetClass' => '\common\models\User', 'message' => 'อีเมลนี้ถูกใช้งานแล้ว'],

            [['full_name', 'phone_number'], 'string', 'max' => 255],
            ['phone_number', 'match', 'pattern' => '/^[0-9\-\s]+$/', 'message' => 'รูปแบบเบอร์โทรไม่ถูกต้อง'],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'รหัสผ่านไม่ตรงกัน'],    
            
            // Profile fields - safe in all scenarios
            [['line_id', 'department'], 'string', 'max' => 100],
            [['notes'], 'string'],
            [['avatar_file_path'], 'string', 'max' => 255],
            [['last_login_at'], 'integer'],
            
            // Make avatar_file_path safe in all scenarios
            [['avatar_file_path'], 'safe'],
            
            // Profile scenario - only these fields are safe
            [['full_name', 'email_address', 'phone_number', 'line_id', 'department', 'notes'], 'safe', 'on' => 'profile'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ชื่อผู้ใช้',
            'email_address' => 'อีเมล',
            'full_name' => 'ชื่อ-นามสกุล',
            'phone_number' => 'เบอร์โทร',
            'line_id' => 'Line ID',
            'department' => 'แผนก/ฝ่าย',
            'avatar_file_path' => 'รูปโปรไฟล์',
            'role' => 'บทบาท',
            'user_status' => 'สถานะ',
            'notes' => 'หมายเหตุ',
            'password' => 'รหัสผ่าน',
            'password_confirm' => 'ยืนยันรหัสผ่าน',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
            'last_login_at' => 'เข้าสู่ระบบล่าสุด',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'user_status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'user_status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'user_status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600;
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->generateAuthKey();
        }

        if ($this->password) {
            $this->setPassword($this->password);
        }

        return true;
    }

    /**
     * Get status list
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'ใช้งาน',
            self::STATUS_INACTIVE => 'ระงับการใช้งาน',
            self::STATUS_DELETED => 'ลบแล้ว',
        ];
    }

    /**
     * Get status text
     * @return string
     */
    public function getStatusText()
    {
        $list = self::getStatusList();
        return $list[$this->user_status] ?? 'ไม่ทราบ';
    }

    /**
     * Get status badge HTML
     * @return string
     */
    public function getStatusBadge()
    {
        $badges = [
            self::STATUS_ACTIVE => '<span class="badge bg-success">ใช้งาน</span>',
            self::STATUS_INACTIVE => '<span class="badge bg-warning">ระงับการใช้งาน</span>',
            self::STATUS_DELETED => '<span class="badge bg-danger">ลบแล้ว</span>',
        ];
        return $badges[$this->user_status] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get role list
     * @return array
     */
    public static function getRoleList()
    {
        return [
            self::ROLE_USER => 'ผู้ใช้ทั่วไป',
            self::ROLE_STAFF => 'พนักงาน',
            self::ROLE_MANAGER => 'ผู้จัดการ',
            self::ROLE_ADMIN => 'ผู้ดูแลระบบ',
        ];
    }

    /**
     * Get role text
     * @return string
     */
    public function getRoleText()
    {
        $list = self::getRoleList();
        return $list[$this->role] ?? 'ไม่ทราบ';
    }

    /**
     * Get role badge HTML
     * @return string
     */
    public function getRoleBadge()
    {
        $badges = [
            self::ROLE_USER => '<span class="badge bg-light text-dark">ผู้ใช้ทั่วไป</span>',
            self::ROLE_STAFF => '<span class="badge bg-info">พนักงาน</span>',
            self::ROLE_MANAGER => '<span class="badge bg-primary">ผู้จัดการ</span>',
            self::ROLE_ADMIN => '<span class="badge bg-danger">ผู้ดูแลระบบ</span>',
        ];
        return $badges[$this->role] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get display name
     * @return string
     */
    public function getDisplayName()
    {
        return $this->full_name ?: $this->username;
    }

    /**
     * Get avatar URL
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->avatar_file_path) {
            // ใช้ Yii::$app->urlManager->baseUrl เพื่อให้ได้ path ที่ถูกต้อง
            if (Yii::$app instanceof \yii\web\Application) {
                return Yii::$app->urlManager->baseUrl . '/uploads/avatars/' . $this->avatar_file_path;
            }
            return '/uploads/avatars/' . $this->avatar_file_path;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->getDisplayName()) . '&background=random';
    }

    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is manager or above
     * @return bool
     */
    public function isManager()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    /**
     * Check if user is staff or above
     * @return bool
     */
    public function isStaff()
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_STAFF]);
    }

    /**
     * Update last login time
     */
    public function updateLastLogin()
    {
        $this->last_login_at = date('Y-m-d H:i:s');
        $this->save(false, ['last_login_at']);
    }

    /**
     * Get activity logs relation
     * @return \yii\db\ActiveQuery
     */
    public function getActivityLogs()
    {
        return $this->hasMany(ActivityLog::class, ['user_id' => 'id']);
    }
}
