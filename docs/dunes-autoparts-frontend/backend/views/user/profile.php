<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'โปรไฟล์ของฉัน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-profile">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-person-circle text-primary me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="bi bi-key me-1"></i>เปลี่ยนรหัสผ่าน', ['change-password'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row">
        <div class="col-lg-4">
            <!-- Avatar Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php 
                    $avatarUrl = null;
                    if (!empty($model->avatar_file_path)) {
                        $avatarUrl = Yii::$app->urlManager->baseUrl . '/uploads/avatars/' . $model->avatar_file_path;
                    }
                    ?>
                    <?php if ($avatarUrl): ?>
                    <img src="<?= $avatarUrl ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;" alt="Avatar" id="avatar-preview">
                    <?php else: ?>
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" 
                         style="width: 150px; height: 150px; font-size: 48px;" id="avatar-preview">
                        <?= strtoupper(substr($model->username, 0, 2)) ?>
                    </div>
                    <?php endif; ?>
                    
                    <h4 class="mb-1"><?= Html::encode($model->full_name ?: $model->username) ?></h4>
                    <p class="text-muted mb-3">
                        <span class="badge bg-<?= $model->role === 'super_admin' ? 'danger' : ($model->role === 'admin' ? 'primary' : ($model->role === 'manager' ? 'success' : 'secondary')) ?>">
                            <?= ucfirst($model->role) ?>
                        </span>
                    </p>
                    
                    <?php if ($avatarUrl): ?>
                    <div class="mb-2">
                        <small class="text-muted">ไฟล์ปัจจุบัน: <?= $model->avatar_file_path ?></small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <input type="file" name="avatar" class="form-control" accept="image/*" id="avatar-input">
                        <div class="form-text">รูปภาพ JPG, PNG ขนาดไม่เกิน 2MB</div>
                    </div>
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลบัญชี</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">ชื่อผู้ใช้</td>
                            <td><strong><?= Html::encode($model->username) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">บทบาท</td>
                            <td><?= ucfirst($model->role) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">สมัครเมื่อ</td>
                            <td><?= Yii::$app->formatter->asDate($model->created_at, 'long') ?></td>
                        </tr>
                        <?php if ($model->last_login_at): ?>
                        <tr>
                            <td class="text-muted">เข้าใช้ล่าสุด</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->last_login_at, 'short') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Personal Info Card -->
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
                            <?= $form->field($model, 'phone_number')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => '08X-XXX-XXXX',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'line_id')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'LINE ID',
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'department')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'แผนก/ฝ่าย',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>หมายเหตุ</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'notes')->textarea([
                        'rows' => 4,
                        'class' => 'form-control',
                        'placeholder' => 'หมายเหตุส่วนตัว...',
                    ])->label(false) ?>
                </div>
            </div>

            <!-- Submit -->
            <div class="card">
                <div class="card-body">
                    <?= Html::submitButton(
                        '<i class="bi bi-save me-1"></i>บันทึกการเปลี่ยนแปลง',
                        ['class' => 'btn btn-primary btn-lg']
                    ) ?>
                </div>
            </div>
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
