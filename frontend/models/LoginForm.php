<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Customer;

class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;
    
    private $_customer;
    
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
            'rememberMe' => 'จดจำการเข้าสู่ระบบ',
        ];
    }
    
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $customer = $this->getCustomer();
            if (!$customer || !$customer->validatePassword($this->password)) {
                $this->addError($attribute, 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
            }
        }
    }
    
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getCustomer(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }
    
    protected function getCustomer()
    {
        if ($this->_customer === null) {
            $this->_customer = Customer::findOne([
                'email' => $this->email,
                'status' => Customer::STATUS_ACTIVE,
            ]);
        }
        return $this->_customer;
    }
}
