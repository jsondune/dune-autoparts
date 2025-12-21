<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'เปลี่ยนรหัสผ่าน';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/customer/profile']) ?>">โปรไฟล์</a></li>
                <li class="breadcrumb-item active">เปลี่ยนรหัสผ่าน</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($model->name_th, 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= Html::encode($model->name_th) ?></h5>
                    <p class="text-muted small mb-0"><?= Html::encode($model->email) ?></p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= Url::to(['/customer/profile']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว
                    </a>
                    <a href="<?= Url::to(['/order/history']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-bag me-2"></i>ประวัติสั่งซื้อ
                    </a>
                    <a href="<?= Url::to(['/customer/change-password']) ?>" class="list-group-item list-group-item-action active">
                        <i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-key me-2 text-primary"></i>เปลี่ยนรหัสผ่าน</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านเดิม <span class="text-danger">*</span></label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                            <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>เปลี่ยนรหัสผ่าน
                            </button>
                            <a href="<?= Url::to(['/customer/profile']) ?>" class="btn btn-outline-secondary">
                                ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
