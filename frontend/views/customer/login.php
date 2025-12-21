<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'เข้าสู่ระบบ';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle display-4 text-primary"></i>
                        <h3 class="mt-3">เข้าสู่ระบบ</h3>
                        <p class="text-muted">เข้าสู่ระบบเพื่อดูประวัติการสั่งซื้อ</p>
                    </div>
                    
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    
                    <?= $form->field($model, 'email')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'อีเมล',
                        'class' => 'form-control form-control-lg',
                    ])->label(false) ?>
                    
                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'รหัสผ่าน',
                        'class' => 'form-control form-control-lg',
                    ])->label(false) ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <?= $form->field($model, 'rememberMe')->checkbox([
                            'template' => '<div class="form-check">{input} {label}</div>',
                            'class' => 'form-check-input',
                        ]) ?>
                    </div>
                    
                    <div class="d-grid mb-4">
                        <?= Html::submitButton('<i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ', [
                            'class' => 'btn btn-primary btn-lg',
                            'name' => 'login-button',
                        ]) ?>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                    
                    <div class="text-center">
                        <p class="mb-0">ยังไม่มีบัญชี? 
                            <a href="<?= Url::to(['/customer/register']) ?>" class="text-primary fw-bold">สมัครสมาชิก</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= Url::to(['/order/track']) ?>" class="text-muted">
                    <i class="bi bi-search me-1"></i>ติดตามคำสั่งซื้อโดยไม่ต้องเข้าสู่ระบบ
                </a>
            </div>
        </div>
    </div>
</div>
