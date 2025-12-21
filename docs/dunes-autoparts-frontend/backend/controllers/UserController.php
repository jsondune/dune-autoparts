<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * UserController - จัดการผู้ใช้งานระบบ
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                    'delete' => ['POST'],
                    'toggle-status' => ['POST'],
                    'reset-password' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * รายการผู้ใช้งาน
     */
    public function actionIndex()
    {
        $search = Yii::$app->request->get('search');
        $status = Yii::$app->request->get('status');
        $role = Yii::$app->request->get('role');
        
        $query = User::find();
        
        // ค้นหา
        if ($search) {
            $query->andWhere([
                'or',
                ['like', 'username', $search],
                ['like', 'email_address', $search],
                ['like', 'full_name', $search],
                ['like', 'phone_number', $search],
            ]);
        }
        
        // กรองสถานะ
        if ($status !== null && $status !== '') {
            $query->andWhere(['user_status' => $status]);
        }
        
        // กรองบทบาท
        if ($role) {
            $query->andWhere(['role' => $role]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        
        // สถิติ
        $stats = [
            'total' => User::find()->count(),
            'active' => User::find()->andWhere(['user_status' => User::STATUS_ACTIVE])->count(),
            'inactive' => User::find()->andWhere(['user_status' => User::STATUS_INACTIVE])->count(),
        ];
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'stats' => $stats,
        ]);
    }

    /**
     * ดูรายละเอียดผู้ใช้
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * สร้างผู้ใช้ใหม่
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';
        
        if ($model->load(Yii::$app->request->post())) {
            $password = Yii::$app->request->post('password');
            if ($password) {
                $model->setPassword($password);
            }
            $model->generateAuthKey();
            $model->user_status = User::STATUS_ACTIVE;
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'สร้างผู้ใช้งานเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * แก้ไขผู้ใช้
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        // เก็บค่า avatar เดิมไว้ก่อน load (สำคัญมาก!)
        $oldAvatarFilePath = $model->avatar_file_path;
        $uploadPath = Yii::getAlias('@backend') . '/web/uploads/avatars';
        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            
            $password = Yii::$app->request->post('password');
            if ($password) {
                $model->setPassword($password);
            }
            
            // Handle avatar upload
            $avatarFile = \yii\web\UploadedFile::getInstanceByName('avatar');
            
            if ($avatarFile && $avatarFile->size > 0) {
                // Create upload directory if not exists
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Delete old avatar if exists
                if (!empty($oldAvatarFilePath)) {
                    $oldFilePath = $uploadPath . '/' . $oldAvatarFilePath;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                // Generate unique filename
                $fileName = 'avatar_' . $model->id . '_' . time() . '.' . $avatarFile->extension;
                $newFilePath = $uploadPath . '/' . $fileName;
                
                // Save file
                if ($avatarFile->saveAs($newFilePath)) {
                    $model->avatar_file_path = $fileName;
                } else {
                    // ถ้า save ไม่ได้ ให้คงค่าเดิม
                    $model->avatar_file_path = $oldAvatarFilePath;
                }
            } else {
                // ถ้าไม่มีการอัปโหลดใหม่ ให้คงค่าเดิมไว้
                $model->avatar_file_path = $oldAvatarFilePath;
            }
            
            // ใช้ save(false) เพื่อข้าม unique validation สำหรับ username/email ของตัวเอง
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'แก้ไขข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * ลบผู้ใช้
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // ไม่ให้ลบตัวเอง
        if ($model->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
            return $this->redirect(['index']);
        }
        
        // ไม่ให้ลบ Super Admin
        if ($model->role === 'super_admin') {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบ Super Admin ได้');
            return $this->redirect(['index']);
        }
        
        $model->user_status = User::STATUS_DELETED;
        $model->save(false);
        
        Yii::$app->session->setFlash('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * เปิด/ปิดสถานะผู้ใช้
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        
        // ไม่ให้ปิดตัวเอง
        if ($model->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถปิดบัญชีของตัวเองได้');
            return $this->redirect(['index']);
        }
        
        $model->user_status = $model->user_status == User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        $model->save(false);
        
        $statusText = $model->user_status == User::STATUS_ACTIVE ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
        Yii::$app->session->setFlash('success', "{$statusText}ผู้ใช้งาน {$model->username} เรียบร้อยแล้ว");
        
        return $this->redirect(['index']);
    }

    /**
     * รีเซ็ตรหัสผ่าน
     */
    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);
        
        $newPassword = Yii::$app->request->post('new_password');
        if ($newPassword) {
            $model->setPassword($newPassword);
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'รีเซ็ตรหัสผ่านสำเร็จ');
            }
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * โปรไฟล์ตัวเอง
     */
    public function actionProfile()
    {
        $model = $this->findModel(Yii::$app->user->id);
        
        // เก็บค่า avatar เดิมไว้ก่อน load (สำคัญมาก!)
        $oldAvatarFilePath = $model->avatar_file_path;
        $uploadPath = Yii::getAlias('@backend') . '/web/uploads/avatars';
        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            
            // Handle avatar upload
            $avatarFile = \yii\web\UploadedFile::getInstanceByName('avatar');
            
            if ($avatarFile && $avatarFile->size > 0) {
                // Create upload directory if not exists
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Delete old avatar if exists
                if (!empty($oldAvatarFilePath)) {
                    $oldFilePath = $uploadPath . '/' . $oldAvatarFilePath;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                // Generate unique filename
                $fileName = 'avatar_' . $model->id . '_' . time() . '.' . $avatarFile->extension;
                $newFilePath = $uploadPath . '/' . $fileName;
                
                // Save file
                if ($avatarFile->saveAs($newFilePath)) {
                    $model->avatar_file_path = $fileName;
                } else {
                    // ถ้า save ไม่ได้ ให้คงค่าเดิม
                    $model->avatar_file_path = $oldAvatarFilePath;
                }
            } else {
                // ถ้าไม่มีการอัปโหลดใหม่ ให้คงค่าเดิมไว้
                $model->avatar_file_path = $oldAvatarFilePath;
            }
            
            // ใช้ save(false) เพื่อข้าม unique validation
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'อัปเดตโปรไฟล์เรียบร้อยแล้ว');
                return $this->refresh();
            }
        }
        
        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * เปลี่ยนรหัสผ่านตัวเอง
     */
    public function actionChangePassword()
    {
        $model = $this->findModel(Yii::$app->user->id);
        
        if (Yii::$app->request->isPost) {
            $currentPassword = Yii::$app->request->post('current_password');
            $newPassword = Yii::$app->request->post('new_password');
            $confirmPassword = Yii::$app->request->post('confirm_password');
            
            // ตรวจสอบรหัสผ่านเดิม
            if (!$model->validatePassword($currentPassword)) {
                Yii::$app->session->setFlash('error', 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
                return $this->redirect(['profile']);
            }
            
            // ตรวจสอบรหัสผ่านใหม่
            if (strlen($newPassword) < 6) {
                Yii::$app->session->setFlash('error', 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร');
                return $this->redirect(['profile']);
            }
            
            if ($newPassword !== $confirmPassword) {
                Yii::$app->session->setFlash('error', 'รหัสผ่านใหม่ไม่ตรงกัน');
                return $this->redirect(['profile']);
            }
            
            $model->setPassword($newPassword);
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
            }
        }
        
        return $this->redirect(['profile']);
    }

    /**
     * ค้นหา Model
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบผู้ใช้งานที่ต้องการ');
    }
}
