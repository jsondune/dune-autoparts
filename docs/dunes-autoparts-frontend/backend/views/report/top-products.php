<?php
/**
 * @var yii\web\View $this
 * @var string $dateFrom
 * @var string $dateTo
 * @var array $topProducts
 * @var int $totalQuantity
 * @var float $totalRevenue
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'สินค้าขายดี';
$this->params['breadcrumbs'][] = ['label' => 'รายงาน', 'url' => ['sales']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-top-products">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-trophy me-2"></i><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">รายการสินค้าที่มียอดขายสูงสุด</p>
        </div>
        <div>
            <a href="<?= Url::to(['sales']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="<?= Url::to(['top-products']) ?>" class="btn btn-outline-secondary">รีเซ็ต</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">จำนวนรายการ</div>
                    <div class="h3 mb-0 text-primary"><?= number_format(count($topProducts)) ?></div>
                    <div class="text-muted small">รายการ</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">ยอดขายรวม</div>
                    <div class="h3 mb-0 text-success"><?= number_format($totalQuantity) ?></div>
                    <div class="text-muted small">ชิ้น</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small">รายได้รวม</div>
                    <div class="h3 mb-0 text-info">฿<?= number_format($totalRevenue, 2) ?></div>
                    <div class="text-muted small">บาท</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-list-ol me-2"></i>รายการสินค้าขายดี</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="60">อันดับ</th>
                            <th>SKU</th>
                            <th>ชื่อสินค้า</th>
                            <th class="text-end">ราคาขาย</th>
                            <th class="text-end">จำนวนขาย</th>
                            <th class="text-end">รายได้</th>
                            <th class="text-center" width="80">ดู</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topProducts)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    ไม่พบข้อมูลสินค้าในช่วงเวลาที่เลือก
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($topProducts as $index => $product): ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($index < 3): ?>
                                            <span class="badge bg-<?= $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'danger') ?> rounded-pill fs-6">
                                                <?= $index + 1 ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted"><?= $index + 1 ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= Html::encode($product['sku']) ?></code></td>
                                    <td>
                                        <div class="fw-medium"><?= Html::encode($product['name_th']) ?></div>
                                        <?php if (!empty($product['name_en'])): ?>
                                            <small class="text-muted"><?= Html::encode($product['name_en']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">฿<?= number_format($product['selling_price'], 2) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?= number_format($product['total_quantity']) ?> ชิ้น</span>
                                    </td>
                                    <td class="text-end text-success fw-bold">฿<?= number_format($product['total_revenue'], 2) ?></td>
                                    <td class="text-center">
                                        <a href="<?= Url::to(['/part/view', 'id' => $product['id']]) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($topProducts)): ?>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">รวมทั้งหมด:</td>
                                <td class="text-end"><?= number_format($totalQuantity) ?> ชิ้น</td>
                                <td class="text-end text-success">฿<?= number_format($totalRevenue, 2) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
