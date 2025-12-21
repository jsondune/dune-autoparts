<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'จัดการผู้ใช้งาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// รายการบทบาท
$roles = [
    'super_admin' => ['label' => 'Super Admin', 'badge' => 'bg-danger'],
    'admin' => ['label' => 'Admin', 'badge' => 'bg-primary'],
    'manager' => ['label' => 'ผู้จัดการ', 'badge' => 'bg-info'],
    'staff' => ['label' => 'พนักงาน', 'badge' => 'bg-secondary'],
    'sales' => ['label' => 'พนักงานขาย', 'badge' => 'bg-success'],
];
$roleInfo = $roles[$model->role] ?? ['label' => $model->role, 'badge' => 'bg-secondary'];
?>

<div class="user-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-person me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div>
            <a href="<?= Url::to(['user/update', 'id' => $model->id]) ?>" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>แก้ไข
            </a>
            <?php if ($model->id != Yii::$app->user->id && $model->role !== 'super_admin'): ?>
            <?= Html::beginForm(['user/delete', 'id' => $model->id], 'post', ['class' => 'd-inline']) ?>
                <button type="submit" class="btn btn-danger" onclick="return confirm('ต้องการลบผู้ใช้นี้หรือไม่?')">
                    <i class="bi bi-trash me-1"></i>ลบ
                </button>
            <?= Html::endForm() ?>
            <?php endif; ?>
            <a href="<?= Url::to(['user/index']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- ข้อมูลหลัก -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if ($model->avatar_file_path): ?>
                    <img src="<?= $model->avatar_file_path ?>" class="rounded-circle mb-3" width="120" height="120" alt="Avata File Path">
                    <?php else: ?>
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" style="width: 120px; height: 120px; font-size: 48px;">
                        <?= strtoupper(substr($model->username, 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                    
                    <h4 class="mb-1"><?= Html::encode($model->full_name ?: $model->username) ?></h4>
                    <p class="text-muted mb-2">@<?= Html::encode($model->username) ?></p>
                    
                    <span class="badge <?= $roleInfo['badge'] ?> mb-3"><?= $roleInfo['label'] ?></span>
                    
                    <?php if ($model->user_status == User::STATUS_ACTIVE): ?>
                    <div class="badge bg-success d-block py-2">
                        <i class="bi bi-check-circle me-1"></i>ใช้งานอยู่
                    </div>
                    <?php else: ?>
                    <div class="badge bg-secondary d-block py-2">
                        <i class="bi bi-x-circle me-1"></i>ปิดใช้งาน
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- เปลี่ยนสถานะ -->
            <?php if ($model->id != Yii::$app->user->id): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">การดำเนินการ</h6>
                </div>
                <div class="card-body">
                    <?= Html::beginForm(['user/toggle-status', 'id' => $model->id], 'post') ?>
                        <button type="submit" class="btn btn-<?= $model->user_status == User::STATUS_ACTIVE ? 'warning' : 'success' ?> w-100 mb-2"
                                onclick="return confirm('ต้องการ<?= $model->user_status == User::STATUS_ACTIVE ? 'ปิด' : 'เปิด' ?>ใช้งานผู้ใช้นี้หรือไม่?')">
                            <i class="bi bi-<?= $model->user_status == User::STATUS_ACTIVE ? 'pause' : 'play' ?> me-1"></i>
                            <?= $model->user_status == User::STATUS_ACTIVE ? 'ปิดใช้งาน' : 'เปิดใช้งาน' ?>
                        </button>
                    <?= Html::endForm() ?>
                    
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="bi bi-key me-1"></i>รีเซ็ตรหัสผ่าน
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-8">
            <!-- ข้อมูลติดต่อ -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>ข้อมูลติดต่อ</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 150px;">อีเมล</th>
                            <td>
                                <a href="mailto:<?= $model->email_address ?>" class="text-decoration-none">
                                    <i class="bi bi-envelope me-1"></i><?= Html::encode($model->email_address) ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>เบอร์โทรศัพท์</th>
                            <td>
                                <?php if ($model->phone_number): ?>
                                <a href="tel:<?= $model->phone_number ?>" class="text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i><?= Html::encode($model->phone_number) ?>
                                </a>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>LINE ID</th>
                            <td>
                                <?php if ($model->line_id): ?>
                                <i class="bi bi-chat-dots me-1"></i><?= Html::encode($model->line_id) ?>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>
                                <?php if ($model->department): ?>
                                <i class="bi bi-chat-dots me-1"></i><?= Html::encode($model->department) ?>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>                        
                    </table>
                </div>
            </div>

            <!-- ข้อมูลบัญชี -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>ข้อมูลบัญชี</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 150px;">บทบาท</th>
                            <td>
                                <span class="badge <?= $roleInfo['badge'] ?>"><?= $roleInfo['label'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>สถานะ</th>
                            <td>
                                <?php if ($model->user_status == User::STATUS_ACTIVE): ?>
                                <span class="badge bg-success">ใช้งานอยู่</span>
                                <?php elseif ($model->user_status == User::STATUS_INACTIVE): ?>
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                                <?php else: ?>
                                <span class="badge bg-danger">ลบแล้ว</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>เข้าใช้ล่าสุด</th>
                            <td>
                                <?php if ($model->last_login_at): ?>
                                <?= Yii::$app->formatter->asDatetime($model->last_login_at) ?>
                                <br><small class="text-muted"><?= Yii::$app->formatter->asRelativeTime($model->last_login_at) ?></small>
                                <?php else: ?>
                                <span class="text-muted">ยังไม่เคยเข้าใช้</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>IP ล่าสุด</th>
                            <td><?= $model->last_login_ip ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>สร้างเมื่อ</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td>
                        </tr>
                        <tr>
                            <th>แก้ไขล่าสุด</th>
                            <td><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- หมายเหตุ -->
            <?php if ($model->notes): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>หมายเหตุ</h6>
                </div>
                <div class="card-body">
                    <?= nl2br(Html::encode($model->notes)) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal รีเซ็ตรหัสผ่าน -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['user/reset-password', 'id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">รีเซ็ตรหัสผ่าน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    กำหนดรหัสผ่านใหม่ให้ผู้ใช้ <strong><?= Html::encode($model->username) ?></strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                    <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>รีเซ็ตรหัสผ่าน
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
