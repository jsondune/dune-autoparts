<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Customer;
use frontend\models\LoginForm;
use frontend\models\SignupForm;

class CustomerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['profile', 'logout', 'change-password'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * เข้าสู่ระบบ
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ ' . Yii::$app->user->identity->full_name);
            return $this->goBack();
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    /**
     * สมัครสมาชิก
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new SignupForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ');
            return $this->redirect(['/customer/login']);
        }
        
        return $this->render('register', [
            'model' => $model,
        ]);
    }
    
    /**
     * ออกจากระบบ
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('success', 'ออกจากระบบเรียบร้อยแล้ว');
        
        return $this->goHome();
    }
    
    /**
     * โปรไฟล์
     */
    public function actionProfile()
    {
        $model = Yii::$app->user->identity;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
            return $this->refresh();
        }
        
        return $this->render('profile', [
            'model' => $model,
        ]);
    }
    
    /**
     * เปลี่ยนรหัสผ่าน
     */
    public function actionChangePassword()
    {
        $model = Yii::$app->user->identity;
        
        if (Yii::$app->request->isPost) {
            $oldPassword = Yii::$app->request->post('old_password');
            $newPassword = Yii::$app->request->post('new_password');
            $confirmPassword = Yii::$app->request->post('confirm_password');
            
            if (!$model->validatePassword($oldPassword)) {
                Yii::$app->session->setFlash('error', 'รหัสผ่านเดิมไม่ถูกต้อง');
            } elseif ($newPassword !== $confirmPassword) {
                Yii::$app->session->setFlash('error', 'รหัสผ่านใหม่ไม่ตรงกัน');
            } elseif (strlen($newPassword) < 6) {
                Yii::$app->session->setFlash('error', 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร');
            } else {
                $model->setPassword($newPassword);
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
                    return $this->redirect(['/customer/profile']);
                }
            }
        }
        
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }
}
