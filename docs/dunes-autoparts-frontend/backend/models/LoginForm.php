<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\ActivityLog;

/**
 * LoginForm - ฟอร์มเข้าสู่ระบบ
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'ชื่อผู้ใช้',
            'password' => 'รหัสผ่าน',
            'rememberMe' => 'จดจำการเข้าสู่ระบบ',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            
            // Check if user is staff or above for backend
            if (!$user->isStaff()) {
                $this->addError('username', 'คุณไม่มีสิทธิ์เข้าใช้งานระบบนี้');
                return false;
            }
            
            $result = Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            
            if ($result) {
                // Update last login
                $user->updateLastLogin();
                
                // Log login activity
                ActivityLog::logLogin($user);
            }
            
            return $result;
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
