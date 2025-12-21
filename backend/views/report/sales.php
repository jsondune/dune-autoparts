<?php
/**
 * Sales Report - รายงานยอดขาย
 * @var yii\web\View $this
 * @var array $salesData
 * @var array $topProducts
 * @var array $topCustomers
 * @var string $period (daily, weekly, monthly, yearly)
 * @var string $dateFrom
 * @var string $dateTo
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'รายงานยอดขาย';
?>

<div class="report-sales">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">วิเคราะห์ยอดขายและประสิทธิภาพการขาย</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> พิมพ์
            </button>
            <a href="<?= Url::to(['export-sales', 'from' => $dateFrom, 'to' => $dateTo]) ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['sales'],
                'options' => ['class' => 'row g-3 align-items-end'],
            ]); ?>
            
            <div class="col-md-2">
                <label class="form-label">ช่วงเวลา</label>
                <?= Html::dropDownList('period', $period, [
                    'daily' => 'รายวัน',
                    'weekly' => 'รายสัปดาห์',
                    'monthly' => 'รายเดือน',
                    'yearly' => 'รายปี',
                ], ['class' => 'form-select']) ?>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">จากวันที่</label>
                <?= Html::input('date', 'date_from', $dateFrom, ['class' => 'form-control']) ?>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">ถึงวันที่</label>
                <?= Html::input('date', 'date_to', $dateTo, ['class' => 'form-control']) ?>
            </div>
            
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-search"></i> ดูรายงาน', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="btn-group w-100">
                    <a href="<?= Url::to(['sales', 'period' => 'daily', 'date_from' => date('Y-m-01'), 'date_to' => date('Y-m-t')]) ?>" 
                       class="btn btn-outline-secondary btn-sm">เดือนนี้</a>
                    <a href="<?= Url::to(['sales', 'period' => 'daily', 'date_from' => date('Y-01-01'), 'date_to' => date('Y-12-31')]) ?>" 
                       class="btn btn-outline-secondary btn-sm">ปีนี้</a>
                </div>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">ยอดขายรวม</div>
                            <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($salesData['total_sales'] ?? 0) ?></h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-currency-dollar text-primary fs-4"></i>
                        </div>
                    </div>
                    <?php if (isset($salesData['sales_growth'])): ?>
                    <div class="small mt-2 <?= $salesData['sales_growth'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="bi bi-<?= $salesData['sales_growth'] >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                        <?= abs($salesData['sales_growth']) ?>% จากช่วงก่อนหน้า
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">จำนวนออเดอร์</div>
                            <h3 class="mb-0"><?= number_format($salesData['total_orders'] ?? 0) ?></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-cart-check text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        เฉลี่ย <?= Yii::$app->formatter->asCurrency($salesData['avg_order_value'] ?? 0) ?>/ออเดอร์
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">สินค้าขายได้</div>
                            <h3 class="mb-0"><?= number_format($salesData['total_items'] ?? 0) ?></h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-box text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        เฉลี่ย <?= number_format($salesData['avg_items_per_order'] ?? 0, 1) ?> ชิ้น/ออเดอร์
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted small mb-1">กำไรขั้นต้น</div>
                            <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($salesData['gross_profit'] ?? 0) ?></h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-graph-up-arrow text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        Margin <?= number_format($salesData['profit_margin'] ?? 0, 1) ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> กราฟยอดขาย</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Sales by Category -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart"></i> ยอดขายตามหมวดหมู่</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <!-- Top Selling Products -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-trophy"></i> สินค้าขายดี</h6>
                    <a href="<?= Url::to(['top-products']) ?>" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>สินค้า</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">ยอดขาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topProducts)): ?>
                                    <?php foreach ($topProducts as $index => $product): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($product['image']): ?>
                                                    <img src="<?= $product['image'] ?>" class="rounded me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold"><?= Html::encode($product['name_th']) ?></div>
                                                    <small class="text-muted"><?= Html::encode($product['sku']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($product['quantity']) ?></td>
                                        <td class="text-end"><?= Yii::$app->formatter->asCurrency($product['total']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ไม่มีข้อมูล</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Customers -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people"></i> ลูกค้าซื้อมากที่สุด</h6>
                    <a href="<?= Url::to(['top-customers']) ?>" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ลูกค้า</th>
                                    <th class="text-center">ออเดอร์</th>
                                    <th class="text-end">ยอดซื้อ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topCustomers)): ?>
                                    <?php foreach ($topCustomers as $index => $customer): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold"><?= Html::encode($customer['full_name']) ?></div>
                                            <small class="text-muted"><?= Html::encode($customer['customer_code']) ?></small>
                                        </td>
                                        <td class="text-center"><?= number_format($customer['orders']) ?></td>
                                        <td class="text-end"><?= Yii::$app->formatter->asCurrency($customer['total']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ไม่มีข้อมูล</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by Payment Method & Shipping -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-credit-card"></i> ยอดขายตามวิธีชำระเงิน</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>วิธีชำระ</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-end">ยอด</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $paymentMethods = $salesData['by_payment_method'] ?? [];
                            foreach ($paymentMethods as $method): 
                            ?>
                            <tr>
                                <td><?= Html::encode($method['label']) ?></td>
                                <td class="text-center"><?= number_format($method['count']) ?></td>
                                <td class="text-end"><?= Yii::$app->formatter->asCurrency($method['total']) ?></td>
                                <td class="text-end"><?= number_format($method['percentage'], 1) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-truck"></i> ยอดขายตามวิธีจัดส่ง</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>วิธีจัดส่ง</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-end">ยอด</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $shippingMethods = $salesData['by_shipping_method'] ?? [];
                            foreach ($shippingMethods as $method): 
                            ?>
                            <tr>
                                <td><?= Html::encode($method['label']) ?></td>
                                <td class="text-center"><?= number_format($method['count']) ?></td>
                                <td class="text-end"><?= Yii::$app->formatter->asCurrency($method['total']) ?></td>
                                <td class="text-end"><?= number_format($method['percentage'], 1) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$chartLabels = json_encode($salesData['chart_labels'] ?? []);
$chartSales = json_encode($salesData['chart_sales'] ?? []);
$chartOrders = json_encode($salesData['chart_orders'] ?? []);
$categoryLabels = json_encode($salesData['category_labels'] ?? []);
$categoryValues = json_encode($salesData['category_values'] ?? []);

$js = <<<JS
// Sales Chart
var salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: {$chartLabels},
        datasets: [{
            label: 'ยอดขาย',
            data: {$chartSales},
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
        }, {
            label: 'ออเดอร์',
            data: {$chartOrders},
            borderColor: '#198754',
            backgroundColor: 'transparent',
            borderDash: [5, 5],
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                ticks: {
                    callback: function(value) {
                        return '฿' + value.toLocaleString();
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
                ticks: {
                    callback: function(value) {
                        return value + ' ออเดอร์';
                    }
                }
            }
        }
    }
});

// Category Chart
var catCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: {$categoryLabels},
        datasets: [{
            data: {$categoryValues},
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
                labels: {
                    usePointStyle: true
                }
            }
        }
    }
});
JS;
$this->registerJs($js);
?>
