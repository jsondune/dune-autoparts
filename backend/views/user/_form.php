<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// รายการบทบาท
$roles = [
    'admin' => 'Admin',
    'manager' => 'ผู้จัดการ',
    'staff' => 'พนักงาน',
    'sales' => 'พนักงานขาย',
];

// เพิ่ม super_admin ถ้าเป็น super_admin
if (Yii::$app->user->identity->role === 'super_admin') {
    $roles = ['super_admin' => 'Super Admin'] + $roles;
}
?>

<div class="user-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>ข้อมูลบัญชี</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'username')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ชื่อผู้ใช้ (ภาษาอังกฤษ)',
                                'disabled' => !$model->isNewRecord,
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'email_address')->textInput([
                                'type' => 'email',
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'example@email.com',
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <?= $model->isNewRecord ? 'รหัสผ่าน' : 'รหัสผ่านใหม่' ?>
                                    <?php if ($model->isNewRecord): ?>
                                    <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="<?= $model->isNewRecord ? 'กำหนดรหัสผ่าน' : 'เว้นว่างถ้าไม่ต้องการเปลี่ยน' ?>"
                                       minlength="6" <?= $model->isNewRecord ? 'required' : '' ?>>
                                <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'role')->dropDownList($roles, [
                                'class' => 'form-select',
                                'prompt' => '-- เลือกบทบาท --',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>ข้อมูลส่วนตัว</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'full_name')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ชื่อ-นามสกุล',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone_number')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => '08X-XXX-XXXX',
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'line_id')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'LINE ID',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'department')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'แผนก/ฝ่าย',
                            ]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'notes')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'หมายเหตุเพิ่มเติม',
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>รูปโปรไฟล์</h6>
                </div>
                <div class="card-body text-center">
                    <?php if ($model->avatar_file_path): ?>
                    <img src="<?= $model->getAvatarUrl() ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;" alt="Avatar" id="avatar-preview">
                    <?php else: ?>
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" 
                         style="width: 150px; height: 150px; font-size: 48px;" id="avatar-preview">
                        <i class="bi bi-person"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <input type="file" name="avatar" class="form-control" accept="image/*" id="avatar-input">
                        <div class="form-text">รูปภาพ JPG, PNG ขนาดไม่เกิน 2MB</div>
                    </div>
                </div>
            </div>

            <?php if (!$model->isNewRecord): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-toggle-on me-2"></i>สถานะ</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'user_status')->dropDownList([
                        10 => 'ใช้งาน',
                        9 => 'ปิดใช้งาน',
                    ], ['class' => 'form-select'])->label(false) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?= Html::submitButton(
                '<i class="bi bi-save me-1"></i>' . ($model->isNewRecord ? 'สร้างผู้ใช้งาน' : 'บันทึกการเปลี่ยนแปลง'),
                ['class' => 'btn btn-primary btn-lg']
            ) ?>
            <?= Html::a('ยกเลิก', ['index'], ['class' => 'btn btn-outline-secondary btn-lg ms-2']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview avatar
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded-circle mb-3';
                    img.style.width = '150px';
                    img.style.height = '150px';
                    img.id = 'avatar-preview';
                    preview.parentNode.replaceChild(img, preview);
                }
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
