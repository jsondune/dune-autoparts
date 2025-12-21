<?php
/**
 * Customer View - รายละเอียดลูกค้า
 * @var yii\web\View $this
 * @var common\models\Customer $model
 * @var array $orders
 * @var array $vehicles
 * @var array $stats
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'ลูกค้า: ' . $model->getDisplayName();
?>

<div class="customer-view">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">ลูกค้า</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($model->customer_code) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <?= Html::encode($model->getDisplayName()) ?>
                <?= $model->getCustomerTypeBadge() ?>
                <?php if (!$model->is_active): ?>
                    <span class="badge bg-secondary">ปิดการใช้งาน</span>
                <?php endif; ?>
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['/order/create', 'customer_id' => $model->id]) ?>" class="btn btn-success">
                <i class="bi bi-cart-plus me-1"></i> สร้างคำสั่งซื้อ
            </a>
            <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> แก้ไข
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="text-muted small">คำสั่งซื้อทั้งหมด</div>
                    <h3 class="mb-0 text-primary"><?= number_format($model->total_orders) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="text-muted small">ยอดซื้อรวม</div>
                    <h3 class="mb-0 text-success"><?= Yii::$app->formatter->asCurrency($model->total_spent, 'THB') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="text-muted small">วงเงินเครดิต</div>
                    <h3 class="mb-0 text-info"><?= Yii::$app->formatter->asCurrency($model->credit_limit, 'THB') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="text-muted small">ส่วนลด</div>
                    <h3 class="mb-0 text-info"><?= Yii::$app->formatter->asCurrency($model->discount_percent, 'THB') ?></h3>
                </div>
            </div>
        </div>        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="text-muted small">ยอดค้างชำระ</div>
                    <h3 class="mb-0 <?= $model->credit_balance > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= Yii::$app->formatter->asCurrency($model->credit_balance, 'THB') ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-xl-8">
            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>คำสั่งซื้อล่าสุด</h5>
                    <a href="<?= Url::to(['/order/index', 'OrderSearch[customer_id]' => $model->id]) ?>" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($orders)): ?>
                        <p class="text-muted text-center py-4 mb-0">ยังไม่มีคำสั่งซื้อ</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>เลขที่</th>
                                        <th>วันที่</th>
                                        <th class="text-center">รายการ</th>
                                        <th class="text-end">ยอดรวม</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">ชำระ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>" class="fw-medium">
                                                    <?= Html::encode($order->order_number) ?>
                                                </a>
                                            </td>
                                            <td><?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d/m/Y') ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark"><?= count($order->items) ?></span>
                                            </td>
                                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($order->grand_total, 'THB') ?></td>
                                            <td class="text-center"><?= $order->getStatusBadge() ?></td>
                                            <td class="text-center"><?= $order->getPaymentStatusBadge() ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Customer Vehicles -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>รถของลูกค้า</h5>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                        <i class="bi bi-plus-lg me-1"></i> เพิ่มรถ
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($vehicles)): ?>
                        <p class="text-muted text-center py-4 mb-0">ยังไม่มีข้อมูลรถ</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ทะเบียน</th>
                                        <th>ยี่ห้อ/รุ่น</th>
                                        <th>ปี</th>
                                        <th>เครื่องยนต์</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vehicles as $vehicle): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold"><?= Html::encode($vehicle->license_plate) ?: '-' ?></span>
                                            </td>
                                            <td>
                                                <?php if ($vehicle->vehicleModel): ?>
                                                    <?= Html::encode($vehicle->vehicleModel->brand->name_th ?? '') ?>
                                                    <?= Html::encode($vehicle->vehicleModel->name_th) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $vehicle->year ?: '-' ?></td>
                                            <td>
                                                <?php if ($vehicle->engineType): ?>
                                                    <?= Html::encode($vehicle->engineType->code) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-edit-vehicle"
                                                            data-id="<?= $vehicle->id ?>"
                                                            data-license="<?= Html::encode($vehicle->license_plate) ?>"
                                                            data-model="<?= $vehicle->vehicle_model_id ?>"
                                                            data-year="<?= $vehicle->year ?>"
                                                            data-engine="<?= $vehicle->engine_type_id ?>"
                                                            data-vin="<?= Html::encode($vehicle->vin) ?>"
                                                            title="แก้ไข">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="<?= Url::to(['delete-vehicle', 'id' => $vehicle->id]) ?>" 
                                                       class="btn btn-outline-danger" 
                                                       title="ลบ"
                                                       onclick="return confirm('ยืนยันลบข้อมูลรถคันนี้?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>ข้อมูลลูกค้า</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">รหัสลูกค้า</td>
                            <td class="fw-bold"><?= Html::encode($model->customer_code) ?></td>
                        </tr>
                        <?php if ($model->company_name): ?>
                        <tr>
                            <td class="text-muted">บริษัท/ร้าน</td>
                            <td><?= Html::encode($model->company_name) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($model->tax_id): ?>
                        <tr>
                            <td class="text-muted">เลขประจำตัวผู้เสียภาษี</td>
                            <td><?= Html::encode($model->tax_id) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">ส่วนลดพิเศษ</td>
                            <td>
                                <?php if ($model->discount_percent > 0): ?>
                                    <span class="badge bg-success"><?= $model->discount_percent ?>%</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">สมัครเมื่อ</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>ข้อมูลติดต่อ</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->phone): ?>
                        <p class="mb-2">
                            <i class="bi bi-telephone me-2 text-muted"></i>
                            <a href="tel:<?= $model->phone ?>"><?= Html::encode($model->phone) ?></a>
                        </p>
                    <?php endif; ?>
                    <?php if ($model->email): ?>
                        <p class="mb-2">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            <a href="mailto:<?= $model->email ?>"><?= Html::encode($model->email) ?></a>
                        </p>
                    <?php endif; ?>
                    <?php if ($model->line_id): ?>
                        <p class="mb-2">
                            <i class="bi bi-line me-2 text-success"></i>
                            <?= Html::encode($model->line_id) ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($model->facebook): ?>
                        <p class="mb-2">
                            <i class="bi bi-facebook me-2 text-primary"></i>
                            <?= Html::encode($model->facebook) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!$model->phone && !$model->email && !$model->line_id): ?>
                        <p class="text-muted mb-0">ไม่มีข้อมูลติดต่อ</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>ที่อยู่</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->address): ?>
                        <p class="mb-0"><?= nl2br(Html::encode($model->address)) ?></p>
                        <?php if ($model->district || $model->province || $model->postal_code): ?>
                            <p class="mb-0 mt-2">
                                <?= Html::encode($model->district) ?>
                                <?= Html::encode($model->province) ?>
                                <?= Html::encode($model->postal_code) ?>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">ไม่มีข้อมูลที่อยู่</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if ($model->notes): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>หมายเหตุ</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(Html::encode($model->notes)) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['add-vehicle', 'customer_id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มรถของลูกค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ทะเบียนรถ</label>
                    <input type="text" name="license_plate" class="form-control" placeholder="กข-1234">
                </div>
                <div class="mb-3">
                    <label class="form-label">ยี่ห้อ</label>
                    <select name="brand_id" id="vehicle-brand" class="form-select" required>
                        <option value="">เลือกยี่ห้อ</option>
                        <?php foreach ($brands ?? [] as $brand): ?>
                            <option value="<?= $brand->id ?>"><?= Html::encode($brand->name_th) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">รุ่น</label>
                    <select name="vehicle_model_id" id="vehicle-model" class="form-select" required>
                        <option value="">เลือกรุ่น</option>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">ปี</label>
                        <input type="number" name="year" class="form-control" min="1990" max="<?= date('Y') + 1 ?>" placeholder="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">รหัสเครื่องยนต์</label>
                        <select name="engine_type_id" id="vehicle-engine" class="form-select">
                            <option value="">เลือกรหัสเครื่อง</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label">หมายเลขตัวถัง (VIN)</label>
                    <input type="text" name="vin" class="form-control" placeholder="17 ตัวอักษร">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มรถ
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<?php
$getModelsUrl = Url::to(['/api/vehicle-models']);
$getEnginesUrl = Url::to(['/api/engine-types']);
$js = <<<JS
// Load vehicle models when brand changes
$('#vehicle-brand').on('change', function() {
    var brandId = $(this).val();
    var modelSelect = $('#vehicle-model');
    var engineSelect = $('#vehicle-engine');
    
    modelSelect.html('<option value="">กำลังโหลด...</option>');
    engineSelect.html('<option value="">เลือกรหัสเครื่อง</option>');
    
    if (brandId) {
        $.get('{$getModelsUrl}', {brand_id: brandId}, function(data) {
            var html = '<option value="">เลือกรุ่น</option>';
            $.each(data, function(i, model) {
                html += '<option value="' + model.id + '">' + model.name_th + '</option>';
            });
            modelSelect.html(html);
        });
    } else {
        modelSelect.html('<option value="">เลือกรุ่น</option>');
    }
});

// Load engine types when model changes
$('#vehicle-model').on('change', function() {
    var modelId = $(this).val();
    var engineSelect = $('#vehicle-engine');
    
    engineSelect.html('<option value="">กำลังโหลด...</option>');
    
    if (modelId) {
        $.get('{$getEnginesUrl}', {model_id: modelId}, function(data) {
            var html = '<option value="">เลือกรหัสเครื่อง</option>';
            $.each(data, function(i, engine) {
                html += '<option value="' + engine.id + '">' + engine.code + ' (' + engine.fuel_type + ')</option>';
            });
            engineSelect.html(html);
        });
    } else {
        engineSelect.html('<option value="">เลือกรหัสเครื่อง</option>');
    }
});
JS;
$this->registerJs($js);
?>
