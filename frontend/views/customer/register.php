<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'สมัครสมาชิก';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus display-4 text-primary"></i>
                        <h3 class="mt-3">สมัครสมาชิก</h3>
                        <p class="text-muted">สร้างบัญชีเพื่อรับสิทธิประโยชน์พิเศษ</p>
                    </div>
                    
                    <?php $form = ActiveForm::begin(['id' => 'register-form']); ?>
                    
                    <?= $form->field($model, 'name')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'ชื่อ-นามสกุล',
                        'class' => 'form-control form-control-lg',
                    ])->label('ชื่อ-นามสกุล <span class="text-danger">*</span>') ?>
                    
                    <?= $form->field($model, 'email')->textInput([
                        'type' => 'email',
                        'placeholder' => 'อีเมล',
                        'class' => 'form-control form-control-lg',
                    ])->label('อีเมล <span class="text-danger">*</span>') ?>
                    
                    <?= $form->field($model, 'phone')->textInput([
                        'placeholder' => 'เบอร์โทรศัพท์ (ถ้ามี)',
                        'class' => 'form-control form-control-lg',
                    ])->label('เบอร์โทรศัพท์') ?>
                    
                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)',
                        'class' => 'form-control form-control-lg',
                    ])->label('รหัสผ่าน <span class="text-danger">*</span>') ?>
                    
                    <?= $form->field($model, 'password_confirm')->passwordInput([
                        'placeholder' => 'ยืนยันรหัสผ่าน',
                        'class' => 'form-control form-control-lg',
                    ])->label('ยืนยันรหัสผ่าน <span class="text-danger">*</span>') ?>
                    
                    <div class="d-grid mb-4">
                        <?= Html::submitButton('<i class="bi bi-person-plus me-2"></i>สมัครสมาชิก', [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'register-button',
                        ]) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                    
                    <div class="text-center">
                        <p class="mb-0">มีบัญชีอยู่แล้ว? 
                            <a href="<?= Url::to(['/customer/login']) ?>" class="text-primary fw-bold">เข้าสู่ระบบ</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-gift text-primary me-2"></i>สิทธิประโยชน์สมาชิก</h6>
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>ดูประวัติสั่งซื้อ</p>
                            <p class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>ติดตามสถานะสินค้า</p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>สั่งซื้อได้รวดเร็ว</p>
                            <p class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>รับโปรโมชั่นพิเศษ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
