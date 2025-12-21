<?php
/**
 * Part Index - รายการสินค้าทั้งหมด
 * @var yii\web\View $this
 * @var backend\models\PartSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $stats
 * @var array $categories
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'จัดการสินค้า';
?>

<div class="part-index">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">รายการอะไหล่ทั้งหมดในระบบ</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['export']) ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้าใหม่
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 text-primary"><?= number_format($stats['total']) ?></h4>
                    <small class="text-muted">สินค้าทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 text-success"><?= number_format($stats['new_parts']) ?></h4>
                    <small class="text-muted">ของใหม่</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 text-info"><?= number_format($stats['used_parts']) ?></h4>
                    <small class="text-muted">มือสองนำเข้า</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-6">
            <div class="card border-0 shadow-sm h-100 <?= $stats['low_stock'] > 0 ? 'border-warning' : '' ?>">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 <?= $stats['low_stock'] > 0 ? 'text-warning' : '' ?>"><?= number_format($stats['low_stock']) ?></h4>
                    <small class="text-muted">ใกล้หมด</small>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-6">
            <div class="card border-0 shadow-sm h-100 <?= $stats['out_of_stock'] > 0 ? 'border-danger' : '' ?>">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 <?= $stats['out_of_stock'] > 0 ? 'text-danger' : '' ?>"><?= number_format($stats['out_of_stock']) ?></h4>
                    <small class="text-muted">หมดสต็อก</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <?php $form = \yii\widgets\ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'row g-3'],
            ]); ?>
            
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <?= Html::activeTextInput($searchModel, 'search', [
                        'class' => 'form-control',
                        'placeholder' => 'ค้นหา SKU, OEM, ชื่อสินค้า...',
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'category_id', $categories, [
                    'class' => 'form-select',
                    'prompt' => '-- หมวดหมู่ทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'part_type', [
                    'new' => 'ของใหม่',
                    'used_imported' => 'มือสองนำเข้า',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- ประเภททั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'stock_status', [
                    'in_stock' => 'มีสต็อก',
                    'low_stock' => 'ใกล้หมด',
                    'out_of_stock' => 'หมดสต็อก',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- สต็อกทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-search"></i> ค้นหา', ['class' => 'btn btn-primary']) ?>
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
            
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Parts Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">รูป</th>
                            <th><?= $dataProvider->sort->link('sku', ['label' => 'SKU']) ?></th>
                            <th><?= $dataProvider->sort->link('name_th', ['label' => 'ชื่อสินค้า']) ?></th>
                            <th>หมวดหมู่</th>
                            <th class="text-center">ประเภท</th>
                            <th class="text-end"><?= $dataProvider->sort->link('selling_price', ['label' => 'ราคาขาย']) ?></th>
                            <th class="text-center"><?= $dataProvider->sort->link('stock_quantity', ['label' => 'สต็อก']) ?></th>
                            <th class="text-center" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dataProvider->getCount() == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    ไม่พบสินค้าที่ค้นหา
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->getModels() as $part): ?>
                                <tr>
                                    <td>
                                        <?php if ($part->main_image): ?>
                                            <img src="<?= Yii::getAlias('@web/uploads/parts/' . $part->main_image) ?>" 
                                                 class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= Url::to(['view', 'id' => $part->id]) ?>" class="fw-medium">
                                            <?= Html::encode($part->sku) ?>
                                        </a>
                                        <?php if ($part->oem_number): ?>
                                            <br><small class="text-muted">OEM: <?= Html::encode($part->oem_number) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= Html::encode($part->name_th ?: $part->name_en) ?>
                                        <?php if ($part->is_featured): ?>
                                            <span class="badge bg-warning text-dark ms-1"><i class="bi bi-star-fill"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $part->category ? Html::encode($part->category->name_th) : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($part->part_type == 'new'): ?>
                                            <span class="badge bg-success">ของใหม่</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">มือสอง</span>
                                            <?php if ($part->condition_grade): ?>
                                                <span class="badge bg-secondary"><?= $part->condition_grade ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($part->discount_price > 0): ?>
                                            <del class="text-muted small"><?= Yii::$app->formatter->asCurrency($part->selling_price, 'THB') ?></del><br>
                                            <span class="text-danger fw-medium"><?= Yii::$app->formatter->asCurrency($part->discount_price, 'THB') ?></span>
                                        <?php else: ?>
                                            <span class="fw-medium"><?= Yii::$app->formatter->asCurrency($part->selling_price, 'THB') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($part->stock_quantity == 0): ?>
                                            <span class="badge bg-danger">หมด</span>
                                        <?php elseif ($part->stock_quantity <= $part->min_stock_level): ?>
                                            <span class="badge bg-warning text-dark"><?= $part->stock_quantity ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= $part->stock_quantity ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= Url::to(['view', 'id' => $part->id]) ?>" 
                                               class="btn btn-outline-primary" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= Url::to(['update', 'id' => $part->id]) ?>" 
                                               class="btn btn-outline-secondary" title="แก้ไข">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($dataProvider->getTotalCount() > $dataProvider->pagination->pageSize): ?>
        <div class="card-footer bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= number_format($dataProvider->getTotalCount()) ?> รายการ
                </div>
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
