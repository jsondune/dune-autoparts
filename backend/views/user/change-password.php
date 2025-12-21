<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'เปลี่ยนรหัสผ่าน';
$this->params['breadcrumbs'][] = ['label' => 'โปรไฟล์ของฉัน', 'url' => ['profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-change-password">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-key text-warning me-2"></i><?= Html::encode($this->title) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="mb-4">
                        <label class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control form-control-lg" 
                                   id="current-password" required placeholder="กรอกรหัสผ่านปัจจุบัน">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control form-control-lg" 
                                   id="new-password" required minlength="6" placeholder="กรอกรหัสผ่านใหม่">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control form-control-lg" 
                                   id="confirm-password" required minlength="6" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>คำแนะนำ:</strong> ใช้รหัสผ่านที่ประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษ เพื่อความปลอดภัยสูงสุด
                    </div>

                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                            '<i class="bi bi-check-lg me-1"></i>เปลี่ยนรหัสผ่าน',
                            ['class' => 'btn btn-warning btn-lg']
                        ) ?>
                        <?= Html::a(
                            '<i class="bi bi-arrow-left me-1"></i>ยกเลิก',
                            ['profile'],
                            ['class' => 'btn btn-outline-secondary btn-lg']
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });

    // Validate confirm password
    const newPassword = document.getElementById('new-password');
    const confirmPassword = document.getElementById('confirm-password');
    
    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            this.setCustomValidity('รหัสผ่านไม่ตรงกัน');
        } else {
            this.setCustomValidity('');
        }
    });
    
    newPassword.addEventListener('input', function() {
        if (confirmPassword.value !== '' && confirmPassword.value !== this.value) {
            confirmPassword.setCustomValidity('รหัสผ่านไม่ตรงกัน');
        } else {
            confirmPassword.setCustomValidity('');
        }
    });
});
</script>
