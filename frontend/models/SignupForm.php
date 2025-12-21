<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Customer;

class SignupForm extends Model
{
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirm;
    
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'password_confirm'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => Customer::class, 'message' => 'อีเมลนี้ถูกใช้งานแล้ว'],
            ['phone', 'string', 'max' => 20],
            ['password', 'string', 'min' => 6],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'รหัสผ่านไม่ตรงกัน'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'name' => 'ชื่อ-นามสกุล',
            'email' => 'อีเมล',
            'phone' => 'เบอร์โทรศัพท์',
            'password' => 'รหัสผ่าน',
            'password_confirm' => 'ยืนยันรหัสผ่าน',
        ];
    }
    
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $customer = new Customer();
        $customer->name = $this->name;
        $customer->email = $this->email;
        $customer->phone = $this->phone;
        $customer->type = 'retail';
        $customer->status = Customer::STATUS_ACTIVE;
        $customer->setPassword($this->password);
        $customer->generateAuthKey();
        
        return $customer->save() ? $customer : null;
    }
}
