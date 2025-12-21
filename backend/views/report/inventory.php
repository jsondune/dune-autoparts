<?php
/**
 * Inventory Report - รายงานสินค้าคงคลัง
 * @var yii\web\View $this
 * @var array $inventoryData
 * @var array $lowStockItems
 * @var array $topMovers
 * @var array $slowMovers
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'รายงานสินค้าคงคลัง';
?>

<div class="report-inventory">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">สรุปสถานะสินค้าคงคลังและการเคลื่อนไหว</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> พิมพ์
            </button>
            <a href="<?= Url::to(['export-inventory']) ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">สินค้าทั้งหมด</div>
                    <h3 class="mb-0 text-primary"><?= number_format($inventoryData['total_products'] ?? 0) ?></h3>
                    <small class="text-muted">รายการ</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">มูลค่าคงคลัง</div>
                    <h3 class="mb-0 text-success"><?= Yii::$app->formatter->asShortSize($inventoryData['total_value'] ?? 0, 0) ?></h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">จำนวนชิ้น</div>
                    <h3 class="mb-0 text-info"><?= number_format($inventoryData['total_quantity'] ?? 0) ?></h3>
                    <small class="text-muted">ชิ้น</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 <?= ($inventoryData['low_stock_count'] ?? 0) > 0 ? 'border-warning border-2' : '' ?>">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">ใกล้หมด</div>
                    <h3 class="mb-0 text-warning"><?= number_format($inventoryData['low_stock_count'] ?? 0) ?></h3>
                    <small class="text-muted">รายการ</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 <?= ($inventoryData['out_of_stock_count'] ?? 0) > 0 ? 'border-danger border-2' : '' ?>">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">สินค้าหมด</div>
                    <h3 class="mb-0 text-danger"><?= number_format($inventoryData['out_of_stock_count'] ?? 0) ?></h3>
                    <small class="text-muted">รายการ</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">เคลื่อนไหว 30 วัน</div>
                    <h3 class="mb-0"><?= number_format($inventoryData['movements_30d'] ?? 0) ?></h3>
                    <small class="text-muted">รายการ</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Stock by Category -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart"></i> สต็อกตามหมวดหมู่</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryStockChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Stock Value Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> มูลค่าสต็อกตามประเภท</h6>
                </div>
                <div class="card-body">
                    <canvas id="valueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Stock Movement Trend -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-activity"></i> การเคลื่อนไหวสต็อก (30 วัน)</h6>
                </div>
                <div class="card-body">
                    <canvas id="movementChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Low Stock Alert -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> สินค้าใกล้หมด</h6>
                    <a href="<?= Url::to(['part/low-stock']) ?>" class="btn btn-sm btn-dark">จัดการสต็อก</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th class="text-center">ขั้นต่ำ</th>
                                    <th class="text-center">แนะนำสั่ง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($lowStockItems)): ?>
                                    <?php foreach ($lowStockItems as $item): ?>
                                    <tr class="<?= $item['quantity'] == 0 ? 'table-danger' : '' ?>">
                                        <td>
                                            <div class="fw-bold"><?= Html::encode($item['name']) ?></div>
                                            <small class="text-muted"><?= Html::encode($item['sku']) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $item['quantity'] == 0 ? 'danger' : 'warning' ?>">
                                                <?= number_format($item['quantity']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?= number_format($item['min_stock_level']) ?></td>
                                        <td class="text-center">
                                            <span class="text-primary fw-bold"><?= number_format($item['suggested_order']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                                            สต็อกทุกรายการปกติ
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock Movement Summary -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-arrow-left-right"></i> สรุปการเคลื่อนไหวสต็อก (30 วัน)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ประเภท</th>
                                    <th class="text-center">รายการ</th>
                                    <th class="text-end">จำนวน</th>
                                    <th class="text-end">มูลค่า</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $movementTypes = [
                                    'in' => ['รับเข้า', 'success'],
                                    'out' => ['ขายออก', 'primary'],
                                    'adjustment' => ['ปรับปรุง', 'warning'],
                                    'return' => ['คืนสินค้า', 'info'],
                                    'damaged' => ['เสียหาย', 'danger'],
                                ];
                                $movements = $inventoryData['movements_by_type'] ?? [];
                                foreach ($movementTypes as $type => $info): 
                                    $data = $movements[$type] ?? ['count' => 0, 'quantity' => 0, 'value' => 0];
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= $info[1] ?>"><?= $info[0] ?></span>
                                    </td>
                                    <td class="text-center"><?= number_format($data['count']) ?></td>
                                    <td class="text-end <?= in_array($type, ['in', 'return']) ? 'text-success' : 'text-danger' ?>">
                                        <?= in_array($type, ['in', 'return']) ? '+' : '-' ?><?= number_format($data['quantity']) ?>
                                    </td>
                                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($data['value']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fast & Slow Movers -->
    <div class="row g-4 mt-2">
        <!-- Fast Movers -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> สินค้าขายดี (Fast Movers)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>สินค้า</th>
                                    <th class="text-center">ขาย/เดือน</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th class="text-center">หมุนเวียน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topMovers)): ?>
                                    <?php foreach ($topMovers as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold"><?= Html::encode($item['name']) ?></div>
                                            <small class="text-muted"><?= Html::encode($item['sku']) ?></small>
                                        </td>
                                        <td class="text-center"><?= number_format($item['sold_qty']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $item['stock'] <= $item['min_stock_level'] ? 'warning' : 'success' ?>">
                                                <?= number_format($item['stock']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= number_format($item['turnover'], 1) ?>x</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">ไม่มีข้อมูล</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slow Movers -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-hourglass"></i> สินค้าค้างสต็อก (Slow Movers)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>สินค้า</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th class="text-end">มูลค่า</th>
                                    <th class="text-center">วันค้าง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($slowMovers)): ?>
                                    <?php foreach ($slowMovers as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold"><?= Html::encode($item['name']) ?></div>
                                            <small class="text-muted"><?= Html::encode($item['sku']) ?></small>
                                        </td>
                                        <td class="text-center"><?= number_format($item['stock']) ?></td>
                                        <td class="text-end"><?= Yii::$app->formatter->asCurrency($item['value']) ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $item['days_stale'] > 90 ? 'danger' : 'warning' ?>">
                                                <?= number_format($item['days_stale']) ?> วัน
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">ไม่มีข้อมูล</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory by Part Type -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0"><i class="bi bi-boxes"></i> สรุปตามประเภทสินค้า</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ประเภท</th>
                            <th class="text-center">จำนวน SKU</th>
                            <th class="text-center">จำนวนชิ้น</th>
                            <th class="text-end">มูลค่าทุน</th>
                            <th class="text-end">มูลค่าขาย</th>
                            <th class="text-end">กำไรโดยประมาณ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $byPartType = $inventoryData['by_part_type'] ?? [];
                        foreach ($byPartType as $type): 
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-<?= $type['type'] == 'new' ? 'success' : 'info' ?>">
                                    <?= $type['type'] == 'new' ? 'สินค้าใหม่' : 'สินค้ามือสอง' ?>
                                </span>
                            </td>
                            <td class="text-center"><?= number_format($type['sku_count']) ?></td>
                            <td class="text-center"><?= number_format($type['total_qty']) ?></td>
                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($type['cost_value']) ?></td>
                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($type['sell_value']) ?></td>
                            <td class="text-end text-success fw-bold">
                                <?= Yii::$app->formatter->asCurrency($type['sell_value'] - $type['cost_value']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>รวมทั้งหมด</td>
                            <td class="text-center"><?= number_format($inventoryData['total_products'] ?? 0) ?></td>
                            <td class="text-center"><?= number_format($inventoryData['total_quantity'] ?? 0) ?></td>
                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($inventoryData['total_cost'] ?? 0) ?></td>
                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($inventoryData['total_value'] ?? 0) ?></td>
                            <td class="text-end text-success">
                                <?= Yii::$app->formatter->asCurrency(($inventoryData['total_value'] ?? 0) - ($inventoryData['total_cost'] ?? 0)) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$categoryLabels = json_encode($inventoryData['category_labels'] ?? []);
$categoryQty = json_encode($inventoryData['category_qty'] ?? []);
$valueLabels = json_encode(['สินค้าใหม่', 'สินค้ามือสอง']);
$valueData = json_encode([
    $inventoryData['by_part_type']['new']['cost_value'] ?? 0,
    $inventoryData['by_part_type']['used']['cost_value'] ?? 0,
]);
$movementDates = json_encode($inventoryData['movement_dates'] ?? []);
$movementIn = json_encode($inventoryData['movement_in'] ?? []);
$movementOut = json_encode($inventoryData['movement_out'] ?? []);

$js = <<<JS
// Category Stock Chart
var catCtx = document.getElementById('categoryStockChart').getContext('2d');
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: {$categoryLabels},
        datasets: [{
            data: {$categoryQty},
            backgroundColor: [
                '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                '#fd7e14', '#20c997', '#0dcaf0', '#6c757d', '#d63384'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { usePointStyle: true }
            }
        }
    }
});

// Value Chart
var valueCtx = document.getElementById('valueChart').getContext('2d');
new Chart(valueCtx, {
    type: 'bar',
    data: {
        labels: {$valueLabels},
        datasets: [{
            label: 'มูลค่า (บาท)',
            data: {$valueData},
            backgroundColor: ['#198754', '#0dcaf0']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                ticks: {
                    callback: function(value) {
                        return '฿' + (value/1000) + 'K';
                    }
                }
            }
        }
    }
});

// Movement Chart
var moveCtx = document.getElementById('movementChart').getContext('2d');
new Chart(moveCtx, {
    type: 'line',
    data: {
        labels: {$movementDates},
        datasets: [{
            label: 'รับเข้า',
            data: {$movementIn},
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'ขายออก',
            data: {$movementOut},
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { usePointStyle: true }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
JS;
$this->registerJs($js);
?>
