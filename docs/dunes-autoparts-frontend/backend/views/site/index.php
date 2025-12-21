<?php
/**
 * Dashboard - หน้าแรกระบบหลังบ้าน
 * @var yii\web\View $this
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard - Dune\'s Auto Parts';
?>

<div class="site-index">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">ภาพรวมธุรกิจ ณ วันที่ <?= date('d/m/Y H:i') ?></p>
        </div>
        <div>
            <button class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i> รีเฟรช
            </button>
        </div>
    </div>

    <!-- KPI Cards Row 1 -->
    <div class="row g-3 mb-4">
        <!-- ยอดขายวันนี้ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-baht-sign text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">ยอดขายวันนี้</p>
                            <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($todaySales, 'THB') ?></h3>
                            <small class="text-muted"><?= $todayOrders ?> ออเดอร์</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ยอดขายเดือนนี้ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-chart-line text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">ยอดขายเดือนนี้</p>
                            <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($monthSales, 'THB') ?></h3>
                            <?php if ($salesChange >= 0): ?>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> <?= $salesChange ?>% จากเดือนก่อน</small>
                            <?php else: ?>
                                <small class="text-danger"><i class="fas fa-arrow-down"></i> <?= abs($salesChange) ?>% จากเดือนก่อน</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ออเดอร์รอดำเนินการ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-clock text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">รอดำเนินการ</p>
                            <h3 class="mb-0"><?= $pendingOrders ?> <small class="fs-6">ออเดอร์</small></h3>
                            <a href="<?= Url::to(['/order/index', 'status' => 'pending']) ?>" class="small">ดูรายละเอียด →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inquiry รอตอบ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-comments text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">สอบถามรอตอบ</p>
                            <h3 class="mb-0"><?= $openInquiries ?> <small class="fs-6">รายการ</small></h3>
                            <a href="<?= Url::to(['/inquiry/index', 'status' => 'open']) ?>" class="small">ดูรายละเอียด →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards Row 2 -->
    <div class="row g-3 mb-4">
        <!-- สินค้าใกล้หมด -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 <?= $lowStockCount > 0 ? 'border-start border-warning border-4' : '' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">สินค้าใกล้หมด</p>
                            <h4 class="mb-0 <?= $lowStockCount > 0 ? 'text-warning' : '' ?>"><?= $lowStockCount ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สินค้าหมด -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 <?= $outOfStockCount > 0 ? 'border-start border-danger border-4' : '' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">สินค้าหมด</p>
                            <h4 class="mb-0 <?= $outOfStockCount > 0 ? 'text-danger' : '' ?>"><?= $outOfStockCount ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box-open text-danger fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ลูกค้าใหม่ -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">ลูกค้าใหม่เดือนนี้</p>
                            <h4 class="mb-0"><?= $newCustomers ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-plus text-success fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- สินค้าทั้งหมด -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">สินค้าที่ขายอยู่</p>
                            <h4 class="mb-0"><?= \common\models\Part::find()->where(['is_active' => 1])->count() ?></h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs text-secondary fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Tables Row -->
    <div class="row g-3 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="mb-0"><i class="fas fa-chart-area text-primary me-2"></i>ยอดขาย 7 วันย้อนหลัง</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Order Status Pie -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>สถานะออเดอร์</h5>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-3">
        <!-- Recent Orders -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary me-2"></i>ออเดอร์ล่าสุด</h5>
                    <a href="<?= Url::to(['/order/index']) ?>" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>เลขที่</th>
                                    <th>ลูกค้า</th>
                                    <th class="text-end">ยอด</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ยังไม่มีออเดอร์</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>">
                                                    <?= Html::encode($order->order_number) ?>
                                                </a>
                                                <br><small class="text-muted"><?= Yii::$app->formatter->asRelativeTime($order->created_at) ?></small>
                                            </td>
                                            <td><?= $order->customer ? Html::encode($order->customer->getDisplayName()) : '-' ?></td>
                                            <td class="text-end"><?= Yii::$app->formatter->asCurrency($order->grand_total, 'THB') ?></td>
                                            <td class="text-center"><?= $order->getStatusBadge() ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i>สินค้าใกล้หมด</h5>
                    <a href="<?= Url::to(['/part/index', 'low_stock' => 1]) ?>" class="btn btn-sm btn-outline-warning">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อสินค้า</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th class="text-center">ขั้นต่ำ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lowStockParts)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle text-success me-1"></i> สต๊อกปกติ
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($lowStockParts as $part): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= Url::to(['/part/view', 'id' => $part->id]) ?>">
                                                    <?= Html::encode($part->sku) ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?= Html::encode(mb_substr($part->name_th ?: $part->name_en, 0, 30)) ?>
                                                <?= strlen($part->name_th ?: $part->name_en) > 30 ? '...' : '' ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($part->stock_quantity == 0): ?>
                                                    <span class="badge bg-danger">หมด</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark"><?= $part->stock_quantity ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= $part->min_stock_level ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inquiry & Top Selling Row -->
    <div class="row g-3 mt-0">
        <!-- Recent Inquiries -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-comments text-info me-2"></i>การสอบถามล่าสุด</h5>
                    <a href="<?= Url::to(['/inquiry/index']) ?>" class="btn btn-sm btn-outline-info">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>เลขที่</th>
                                    <th>ลูกค้า</th>
                                    <th>ช่องทาง</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentInquiries)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ไม่มีการสอบถามรอตอบ</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentInquiries as $inquiry): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= Url::to(['/inquiry/view', 'id' => $inquiry->id]) ?>">
                                                    <?= Html::encode($inquiry->inquiry_number) ?>
                                                </a>
                                                <br><small class="text-muted"><?= Yii::$app->formatter->asRelativeTime($inquiry->created_at) ?></small>
                                            </td>
                                            <td>
                                                <?= $inquiry->customer_name ?: ($inquiry->customer ? $inquiry->customer->getDisplayName() : '-') ?>
                                            </td>
                                            <td><?= $inquiry->getChannelBadge() ?></td>
                                            <td class="text-center"><?= $inquiry->getStatusBadge() ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="mb-0"><i class="fas fa-trophy text-warning me-2"></i>สินค้าขายดีเดือนนี้</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>สินค้า</th>
                                    <th class="text-center">ขายได้</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topSellingParts)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">ยังไม่มียอดขายเดือนนี้</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($topSellingParts as $i => $part): ?>
                                        <tr>
                                            <td>
                                                <?php if ($i == 0): ?>
                                                    <i class="fas fa-medal text-warning"></i>
                                                <?php elseif ($i == 1): ?>
                                                    <i class="fas fa-medal text-secondary"></i>
                                                <?php elseif ($i == 2): ?>
                                                    <i class="fas fa-medal" style="color: #cd7f32;"></i>
                                                <?php else: ?>
                                                    <?= $i + 1 ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= Url::to(['/part/view', 'id' => $part['id']]) ?>">
                                                    <?= Html::encode($part['sku']) ?>
                                                </a>
                                                <br><small class="text-muted"><?= Html::encode(mb_substr($part['name_th'] ?: $part['name_en'], 0, 40)) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success"><?= $part['total_sold'] ?> ชิ้น</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Chart.js Scripts
$salesChartJson = json_encode($salesChartData);
$orderStatusJson = json_encode($orderStatusSummary);

$js = <<<JS
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesData = {$salesChartJson};

new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesData.map(d => d.date),
        datasets: [{
            label: 'ยอดขาย (บาท)',
            data: salesData.map(d => d.sales),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '฿' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '฿' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Order Status Pie Chart
const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
const statusData = {$orderStatusJson};

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['รอยืนยัน', 'ยืนยันแล้ว', 'กำลังจัด', 'จัดส่งแล้ว', 'สำเร็จ', 'ยกเลิก'],
        datasets: [{
            data: [
                statusData.pending,
                statusData.confirmed,
                statusData.preparing,
                statusData.shipped,
                statusData.delivered,
                statusData.cancelled
            ],
            backgroundColor: [
                '#ffc107',
                '#17a2b8',
                '#fd7e14',
                '#0d6efd',
                '#198754',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, usePointStyle: true }
            }
        }
    }
});
JS;

$this->registerJs($js);
?>
