<?php
/**
 * Part View - รายละเอียดสินค้า
 * @var yii\web\View $this
 * @var common\models\Part $model
 * @var array $stockMovements
 * @var array $compatibleVehicles
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->sku . ' - ' . ($model->name_th ?: $model->name_en);
?>

<div class="part-view">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">สินค้า</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($model->sku) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mb-0"><?= Html::encode($model->name_th ?: $model->name_en) ?></h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> แก้ไข
            </a>
            <?= Html::beginForm(['delete', 'id' => $model->id], 'post', ['class' => 'd-inline']) ?>
            <?= Html::submitButton('<i class="bi bi-trash me-1"></i> ลบ', [
                'class' => 'btn btn-outline-danger',
                'data' => ['confirm' => 'ยืนยันการลบสินค้านี้?'],
            ]) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Info -->
        <div class="col-xl-8">
            <!-- Product Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <!-- Images -->
                        <div class="col-md-4 mb-3 mb-md-0">
                            <?php if ($model->main_image): ?>
                                <img src="<?= Yii::getAlias('@web/uploads/parts/' . $model->main_image) ?>" 
                                     class="img-fluid rounded mb-2" id="mainImage">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 250px;" id="mainImage">
                                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <?php 
                            $images = is_array($model->images) ? $model->images : json_decode($model->images, true);
                            if (!empty($images)): 
                            ?>
                                <div class="d-flex gap-2 flex-wrap mt-2">
                                    <?php foreach ($images as $img): ?>
                                        <img src="<?= Yii::getAlias('@web/uploads/parts/' . $img) ?>" 
                                             class="rounded cursor-pointer" 
                                             style="width: 60px; height: 60px; object-fit: cover;"
                                             onclick="document.getElementById('mainImage').src=this.src">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Details -->
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted small">SKU</label>
                                    <div class="fw-bold"><?= Html::encode($model->sku) ?></div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">เลข OEM</label>
                                    <div class="fw-bold"><?= Html::encode($model->oem_number) ?: '-' ?></div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">หมวดหมู่</label>
                                    <div><?= $model->category ? Html::encode($model->category->name_th) : '-' ?></div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">ประเภท</label>
                                    <div>
                                        <?php if ($model->part_type == 'new'): ?>
                                            <span class="badge bg-success">ของใหม่</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">มือสองนำเข้า</span>
                                            <?php if ($model->condition_grade): ?>
                                                <span class="badge bg-secondary ms-1">Grade <?= $model->condition_grade ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small">ชื่อสินค้า (ไทย)</label>
                                    <div><?= Html::encode($model->name_th) ?: '-' ?></div>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small">ชื่อสินค้า (EN)</label>
                                    <div><?= Html::encode($model->name_en) ?: '-' ?></div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">Supplier</label>
                                    <div><?= $model->supplier ? Html::encode($model->supplier->name) : '-' ?></div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small">การรับประกัน</label>
                                    <div><?= $model->getWarrantyText() ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if ($model->description): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-card-text me-2"></i>รายละเอียด</h5>
                </div>
                <div class="card-body pt-0">
                    <?= nl2br(Html::encode($model->description)) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Specifications -->
            <?php 
            $specs = is_array($model->specifications) ? $model->specifications : json_decode($model->specifications, true);
            if (!empty($specs)): 
            ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>สเปค</h5>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-sm mb-0">
                        <?php foreach ($specs as $key => $value): ?>
                            <tr>
                                <td class="text-muted" style="width: 40%;"><?= Html::encode($key) ?></td>
                                <td class="fw-medium"><?= Html::encode($value) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Compatible Vehicles -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>รถที่ใช้ได้ (<?= count($compatibleVehicles) ?>)</h5>
                </div>
                <div class="card-body pt-0">
                    <?php if (empty($compatibleVehicles)): ?>
                        <p class="text-muted mb-0">ยังไม่ได้ระบุรถที่ใช้ได้</p>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($compatibleVehicles as $pv): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="bg-light rounded p-2 small">
                                        <i class="bi bi-car-front me-1"></i>
                                        <?= $pv->getCompatibilityText() ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stock History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>ประวัติสต็อก</h5>
                    <a href="<?= Url::to(['stock-history', 'id' => $model->id]) ?>" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่</th>
                                    <th>ประเภท</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th>หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($stockMovements)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">ยังไม่มีประวัติ</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($stockMovements as $sm): ?>
                                        <tr>
                                            <td><?= Yii::$app->formatter->asDatetime($sm->created_at, 'php:d/m/Y H:i') ?></td>
                                            <td><?= $sm->getTypeBadge() ?></td>
                                            <td class="text-center">
                                                <?php if (in_array($sm->movement_type, ['in', 'return'])): ?>
                                                    <span class="text-success">+<?= $sm->quantity ?></span>
                                                <?php else: ?>
                                                    <span class="text-danger">-<?= $sm->quantity ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= $sm->stock_after ?></td>
                                            <td class="small"><?= Html::encode($sm->reason) ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Price Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-tag me-2"></i>ราคา</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small">ราคาทุน</label>
                            <div class="h5 mb-0"><?= Yii::$app->formatter->asCurrency($model->cost_price, 'THB') ?></div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">ราคาขาย</label>
                            <div class="h5 mb-0"><?= Yii::$app->formatter->asCurrency($model->selling_price, 'THB') ?></div>
                        </div>
                        <?php if ($model->discount_price > 0): ?>
                            <div class="col-6">
                                <label class="text-muted small">ราคาลด</label>
                                <div class="h5 mb-0 text-danger"><?= Yii::$app->formatter->asCurrency($model->discount_price, 'THB') ?></div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small">ส่วนลด</label>
                                <div class="h5 mb-0 text-danger"><?= $model->getDiscountPercent() ?>%</div>
                            </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <hr class="my-2">
                            <label class="text-muted small">กำไรต่อชิ้น</label>
                            <?php $profit = $model->getCurrentPrice() - $model->cost_price; ?>
                            <div class="h5 mb-0 <?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= Yii::$app->formatter->asCurrency($profit, 'THB') ?>
                                <small class="text-muted fw-normal">
                                    (<?= $model->cost_price > 0 ? round(($profit / $model->cost_price) * 100, 1) : 0 ?>%)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>สต็อก</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-4 fw-bold <?= $model->stock_quantity == 0 ? 'text-danger' : ($model->stock_quantity <= $model->min_stock_level ? 'text-warning' : 'text-success') ?>">
                            <?= $model->stock_quantity ?>
                        </div>
                        <div class="text-muted">ชิ้น</div>
                        <?php if ($model->stock_quantity == 0): ?>
                            <span class="badge bg-danger mt-2">หมดสต็อก</span>
                        <?php elseif ($model->stock_quantity <= $model->min_stock_level): ?>
                            <span class="badge bg-warning text-dark mt-2">ใกล้หมด (Min: <?= $model->min_stock_level ?>)</span>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <!-- Stock Update Form -->
                    <?= Html::beginForm(['update-stock', 'id' => $model->id], 'post') ?>
                    <div class="mb-3">
                        <label class="form-label small">ปรับปรุงสต็อก</label>
                        <div class="input-group input-group-sm">
                            <select name="type" class="form-select" style="max-width: 120px;">
                                <option value="in">รับเข้า</option>
                                <option value="out">เบิกออก</option>
                                <option value="adjustment">ปรับยอด</option>
                                <option value="return">รับคืน</option>
                                <option value="damaged">เสียหาย</option>
                            </select>
                            <input type="number" name="quantity" class="form-control" placeholder="จำนวน" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="reason" class="form-control form-control-sm" placeholder="เหตุผล/หมายเหตุ">
                    </div>
                    <div class="mb-3">
                        <input type="text" name="reference_no" class="form-control form-control-sm" placeholder="เลขอ้างอิง (ถ้ามี)">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-check-lg me-1"></i> บันทึก
                    </button>
                    <?= Html::endForm() ?>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>สถิติ</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="h4 mb-0"><?= number_format($model->view_count) ?></div>
                            <small class="text-muted">เข้าชม</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0"><?= number_format($model->sold_count) ?></div>
                            <small class="text-muted">ขายแล้ว</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลระบบ</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">สถานะ</td>
                            <td class="text-end">
                                <?= $model->is_active ? '<span class="badge bg-success">เปิดขาย</span>' : '<span class="badge bg-secondary">ปิดขาย</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">สินค้าแนะนำ</td>
                            <td class="text-end">
                                <?= $model->is_featured ? '<span class="badge bg-warning text-dark">ใช่</span>' : '<span class="text-muted">-</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">สร้างเมื่อ</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">แก้ไขล่าสุด</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
