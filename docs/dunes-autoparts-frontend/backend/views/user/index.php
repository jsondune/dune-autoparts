<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\User;

$this->title = 'จัดการผู้ใช้งาน';
$this->params['breadcrumbs'][] = $this->title;

// รายการบทบาท
$roles = [
    'super_admin' => ['label' => 'Super Admin', 'badge' => 'bg-danger'],
    'admin' => ['label' => 'Admin', 'badge' => 'bg-primary'],
    'manager' => ['label' => 'ผู้จัดการ', 'badge' => 'bg-info'],
    'staff' => ['label' => 'พนักงาน', 'badge' => 'bg-secondary'],
    'sales' => ['label' => 'พนักงานขาย', 'badge' => 'bg-success'],
];
?>

<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-people me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <a href="<?= Url::to(['user/create']) ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>เพิ่มผู้ใช้งาน
        </a>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- สถิติ -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">ผู้ใช้ทั้งหมด</div>
                            <div class="h3 mb-0"><?= number_format($stats['total']) ?></div>
                        </div>
                        <i class="bi bi-people fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="<?= Url::to(['user/index', 'status' => User::STATUS_ACTIVE]) ?>" class="text-decoration-none">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small">ใช้งานอยู่</div>
                                <div class="h3 mb-0"><?= number_format($stats['active']) ?></div>
                            </div>
                            <i class="bi bi-person-check fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= Url::to(['user/index', 'status' => User::STATUS_INACTIVE]) ?>" class="text-decoration-none">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 small">ปิดใช้งาน</div>
                                <div class="h3 mb-0"><?= number_format($stats['inactive']) ?></div>
                            </div>
                            <i class="bi bi-person-x fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- ค้นหา -->
    <div class="card mb-4">
        <div class="card-body">
            <?= Html::beginForm(['user/index'], 'get', ['class' => 'row g-3']) ?>
                <div class="col-md-4">
                    <?= Html::textInput('search', $search, [
                        'class' => 'form-control',
                        'placeholder' => 'ค้นหาชื่อ, อีเมล, เบอร์โทร...',
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= Html::dropDownList('status', $status, [
                        '' => '-- สถานะทั้งหมด --',
                        User::STATUS_ACTIVE => 'ใช้งานอยู่',
                        User::STATUS_INACTIVE => 'ปิดใช้งาน',
                    ], ['class' => 'form-select']) ?>
                </div>
                <div class="col-md-3">
                    <?= Html::dropDownList('role', $role, array_merge(
                        ['' => '-- บทบาททั้งหมด --'],
                        array_map(function($r) { return $r['label']; }, $roles)
                    ), ['class' => 'form-select']) ?>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <!-- รายการผู้ใช้ -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;"></th>
                            <th>ชื่อผู้ใช้</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>บทบาท</th>
                            <th class="text-center">สถานะ</th>
                            <th>เข้าใช้ล่าสุด</th>
                            <th class="text-center" style="width: 150px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataProvider->getModels() as $user): ?>
                        <tr>
                            <td>
                                <?php if ($user->avatar_file_path): ?>
                                <img src="<?= $user->getAvatarUrl() ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;" alt="Avatar">
                                <?php else: ?>
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                    <?= strtoupper(substr($user->username, 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= Url::to(['user/view', 'id' => $user->id]) ?>" class="fw-medium text-decoration-none">
                                    <?= Html::encode($user->username) ?>
                                </a>
                            </td>
                            <td>
                                <?= Html::encode($user->full_name ?: '-') ?>
                                <?php if ($user->phone_number): ?>
                                <br><small class="text-muted"><i class="bi bi-telephone me-1"></i><?= Html::encode($user->phone_number) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="mailto:<?= $user->email_address ?>" class="text-decoration-none">
                                    <?= Html::encode($user->email_address) ?>
                                </a>
                            </td>
                            <td>
                                <?php
                                $roleInfo = $roles[$user->role] ?? ['label' => $user->role, 'badge' => 'bg-secondary'];
                                ?>
                                <span class="badge <?= $roleInfo['badge'] ?>"><?= $roleInfo['label'] ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($user->user_status == User::STATUS_ACTIVE): ?>
                                <span class="badge bg-success">ใช้งาน</span>
                                <?php elseif ($user->user_status == User::STATUS_INACTIVE): ?>
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                                <?php else: ?>
                                <span class="badge bg-danger">ลบแล้ว</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user->last_login_at): ?>
                                <?= Yii::$app->formatter->asRelativeTime($user->last_login_at) ?>
                                <br><small class="text-muted"><?= Yii::$app->formatter->asDatetime($user->last_login_at, 'short') ?></small>
                                <?php else: ?>
                                <span class="text-muted">ยังไม่เคยเข้าใช้</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= Url::to(['user/view', 'id' => $user->id]) ?>" class="btn btn-outline-primary" title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= Url::to(['user/update', 'id' => $user->id]) ?>" class="btn btn-outline-secondary" title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($user->id != Yii::$app->user->id): ?>
                                    <?= Html::beginForm(['user/toggle-status', 'id' => $user->id], 'post', ['class' => 'd-inline']) ?>
                                        <button type="submit" class="btn btn-outline-<?= $user->status == User::STATUS_ACTIVE ? 'warning' : 'success' ?>" 
                                                title="<?= $user->status == User::STATUS_ACTIVE ? 'ปิดใช้งาน' : 'เปิดใช้งาน' ?>"
                                                onclick="return confirm('ต้องการ<?= $user->status == User::STATUS_ACTIVE ? 'ปิด' : 'เปิด' ?>ใช้งานผู้ใช้นี้หรือไม่?')">
                                            <i class="bi bi-<?= $user->status == User::STATUS_ACTIVE ? 'pause' : 'play' ?>"></i>
                                        </button>
                                    <?= Html::endForm() ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($dataProvider->getCount() == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">ไม่พบผู้ใช้งาน</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($dataProvider->getTotalCount() > $dataProvider->getPagination()->pageSize): ?>
        <div class="card-footer">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->getPagination(),
                'options' => ['class' => 'pagination mb-0 justify-content-center'],
                'linkContainerOptions' => ['class' => 'page-item'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['class' => 'page-link'],
            ]) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
