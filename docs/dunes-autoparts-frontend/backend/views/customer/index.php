<?php
/**
 * Customer Index - รายการลูกค้า
 * @var yii\web\View $this
 * @var backend\models\CustomerSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $stats
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'จัดการลูกค้า';
?>

<div class="customer-index">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">รายชื่อลูกค้าทั้งหมด</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['export']) ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มลูกค้า
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 text-primary"><?= number_format($stats['total']) ?></h4>
                    <small class="text-muted">ลูกค้าทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'CustomerSearch[customer_type]' => 'retail']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-info"><?= number_format($stats['retail']) ?></h4>
                        <small class="text-muted">ลูกค้าทั่วไป</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'CustomerSearch[customer_type]' => 'wholesale']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-success"><?= number_format($stats['wholesale']) ?></h4>
                        <small class="text-muted">ลูกค้าขายส่ง</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'CustomerSearch[customer_type]' => 'garage']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-warning"><?= number_format($stats['garage']) ?></h4>
                        <small class="text-muted">อู่ซ่อมรถ</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 text-secondary"><?= number_format($stats['this_month']) ?></h4>
                    <small class="text-muted">ลูกค้าใหม่เดือนนี้</small>
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
                        'placeholder' => 'รหัส, ชื่อ, เบอร์โทร, อีเมล, Line ID...',
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'customer_type', [
                    'retail' => 'ลูกค้าทั่วไป',
                    'wholesale' => 'ลูกค้าขายส่ง',
                    'garage' => 'อู่ซ่อมรถ',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- ประเภททั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'status', [
                    '1' => 'ใช้งาน',
                    '0' => 'ปิดการใช้งาน',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- สถานะทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-3">
                <div class="input-group">
                    <?= Html::activeInput('date', $searchModel, 'date_from', [
                        'class' => 'form-control',
                    ]) ?>
                    <span class="input-group-text">-</span>
                    <?= Html::activeInput('date', $searchModel, 'date_to', [
                        'class' => 'form-control',
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-1">
                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'btn btn-primary']) ?>
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
            
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= $dataProvider->sort->link('customer_code', ['label' => 'รหัส']) ?></th>
                            <th>ชื่อลูกค้า</th>
                            <th>ติดต่อ</th>
                            <th class="text-center">ประเภท</th>
                            <th class="text-center"><?= $dataProvider->sort->link('total_orders', ['label' => 'คำสั่งซื้อ']) ?></th>
                            <th class="text-end"><?= $dataProvider->sort->link('total_spent', ['label' => 'ยอดซื้อรวม']) ?></th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dataProvider->getCount() == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    ไม่พบข้อมูลลูกค้า
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->getModels() as $customer): ?>
                                <tr>
                                    <td>
                                        <a href="<?= Url::to(['view', 'id' => $customer->id]) ?>" class="fw-bold">
                                            <?= Html::encode($customer->customer_code) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-person text-muted"></i>
                                            </div>
                                            <div>
                                                <span class="fw-medium"><?= Html::encode($customer->getDisplayName()) ?></span>
                                                <?php if ($customer->company_name): ?>
                                                    <br><small class="text-muted"><?= Html::encode($customer->company_name) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($customer->phone): ?>
                                            <div><i class="bi bi-telephone me-1 text-muted"></i><?= Html::encode($customer->phone) ?></div>
                                        <?php endif; ?>
                                        <?php if ($customer->line_id): ?>
                                            <div><i class="bi bi-line me-1 text-success"></i><?= Html::encode($customer->line_id) ?></div>
                                        <?php endif; ?>
                                        <?php if ($customer->email): ?>
                                            <div><i class="bi bi-envelope me-1 text-muted"></i><small><?= Html::encode($customer->email) ?></small></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $customer->getCustomerTypeBadge() ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark"><?= number_format($customer->total_orders) ?></span>
                                    </td>
                                    <td class="text-end">
                                        <strong><?= Yii::$app->formatter->asCurrency($customer->total_spent, 'THB') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($customer->is_active): ?>
                                            <span class="badge bg-success">ใช้งาน</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ปิดการใช้งาน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= Url::to(['view', 'id' => $customer->id]) ?>" 
                                               class="btn btn-outline-primary" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= Url::to(['update', 'id' => $customer->id]) ?>" 
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
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
