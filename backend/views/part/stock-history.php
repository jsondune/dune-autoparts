<?php
/**
 * Stock History - ประวัติการเคลื่อนไหวสต็อก
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var int|null $partId
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'ประวัติสต็อก';
?>

<div class="stock-history">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">บันทึกการรับเข้า-เบิกออกสินค้า</p>
        </div>
        <div>
            <?php if ($partId): ?>
                <a href="<?= Url::to(['view', 'id' => $partId]) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> กลับ
                </a>
            <?php else: ?>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> กลับ
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="ค้นหา SKU, ชื่อสินค้า..." 
                               value="<?= Html::encode(Yii::$app->request->get('search')) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">-- ประเภททั้งหมด --</option>
                        <option value="in" <?= Yii::$app->request->get('type') == 'in' ? 'selected' : '' ?>>รับเข้า</option>
                        <option value="out" <?= Yii::$app->request->get('type') == 'out' ? 'selected' : '' ?>>เบิกออก</option>
                        <option value="adjustment" <?= Yii::$app->request->get('type') == 'adjustment' ? 'selected' : '' ?>>ปรับยอด</option>
                        <option value="return" <?= Yii::$app->request->get('type') == 'return' ? 'selected' : '' ?>>รับคืน</option>
                        <option value="damaged" <?= Yii::$app->request->get('type') == 'damaged' ? 'selected' : '' ?>>เสียหาย</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" 
                           value="<?= Html::encode(Yii::$app->request->get('date')) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>วันที่/เวลา</th>
                            <th>สินค้า</th>
                            <th>ประเภท</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-center">ก่อน</th>
                            <th class="text-center">หลัง</th>
                            <th>เลขอ้างอิง</th>
                            <th>หมายเหตุ</th>
                            <th>ผู้ทำรายการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dataProvider->getCount() == 0): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    ไม่พบประวัติ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->getModels() as $sm): ?>
                                <tr>
                                    <td>
                                        <?= Yii::$app->formatter->asDatetime($sm->created_at, 'php:d/m/Y') ?>
                                        <br><small class="text-muted"><?= Yii::$app->formatter->asDatetime($sm->created_at, 'php:H:i:s') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($sm->part): ?>
                                            <a href="<?= Url::to(['view', 'id' => $sm->part_id]) ?>" class="fw-medium">
                                                <?= Html::encode($sm->part->sku) ?>
                                            </a>
                                            <br><small class="text-muted"><?= Html::encode(mb_substr($sm->part->name_th ?: $sm->part->name_en, 0, 30)) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $sm->getTypeBadge() ?></td>
                                    <td class="text-center">
                                        <?php if (in_array($sm->movement_type, ['in', 'return'])): ?>
                                            <span class="text-success fw-bold">+<?= $sm->quantity ?></span>
                                        <?php else: ?>
                                            <span class="text-danger fw-bold">-<?= $sm->quantity ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $sm->stock_before ?></td>
                                    <td class="text-center">
                                        <span class="fw-bold"><?= $sm->stock_after ?></span>
                                    </td>
                                    <td>
                                        <?= $sm->reference_no ? Html::encode($sm->reference_no) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <small><?= Html::encode($sm->reason) ?: '-' ?></small>
                                    </td>
                                    <td>
                                        <?php if ($sm->user): ?>
                                            <?= Html::encode($sm->user->username) ?>
                                        <?php else: ?>
                                            <span class="text-muted">ระบบ</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($dataProvider->getTotalCount() > $dataProvider->pagination->pageSize): ?>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= number_format($dataProvider->getTotalCount()) ?> รายการ
                </div>
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
