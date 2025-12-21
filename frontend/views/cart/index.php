<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'ตะกร้าสินค้า';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">ตะกร้าสินค้า</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-cart3 text-primary me-2"></i>ตะกร้าสินค้า</h2>
    
    <?php if (!empty($cartItems)): ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 100px;">รูป</th>
                                    <th>สินค้า</th>
                                    <th class="text-center" style="width: 100px;">ราคา</th>
                                    <th class="text-center" style="width: 150px;">จำนวน</th>
                                    <th class="text-end" style="width: 120px;">รวม</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <?php if ($item['part']->image): ?>
                                        <img src="<?= $item['part']->image ?>" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="bi bi-box-seam text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= Url::to(['/part/view', 'id' => $item['part']->id]) ?>" class="text-decoration-none">
                                            <strong><?= Html::encode($item['part']->name_th) ?></strong>
                                        </a>
                                        <br>
                                        <small class="text-muted"><?= Html::encode($item['part']->part_number) ?></small>
                                    </td>
                                    <td class="text-center">
                                        ฿<?= number_format($item['part']->sell_price) ?>
                                    </td>
                                    <td>
                                        <?= Html::beginForm(['/cart/update'], 'post', ['class' => 'd-flex justify-content-center']) ?>
                                        <input type="hidden" name="part_id" value="<?= $item['part']->id ?>">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <button type="submit" name="qty" value="<?= $item['qty'] - 1 ?>" class="btn btn-outline-secondary">-</button>
                                            <input type="text" class="form-control text-center" value="<?= $item['qty'] ?>" readonly>
                                            <button type="submit" name="qty" value="<?= $item['qty'] + 1 ?>" class="btn btn-outline-secondary">+</button>
                                        </div>
                                        <?= Html::endForm() ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        ฿<?= number_format($item['subtotal']) ?>
                                    </td>
                                    <td>
                                        <a href="<?= Url::to(['/cart/remove', 'id' => $item['part']->id]) ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('ต้องการลบสินค้านี้?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="<?= Url::to(['/part/index']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>เลือกสินค้าเพิ่ม
                    </a>
                    <a href="<?= Url::to(['/cart/clear']) ?>" class="btn btn-outline-danger" onclick="return confirm('ต้องการล้างตะกร้า?')">
                        <i class="bi bi-trash me-2"></i>ล้างตะกร้า
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>สรุปคำสั่งซื้อ</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>รวมสินค้า (<?= count($cartItems) ?> รายการ)</span>
                        <span>฿<?= number_format($total) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ค่าจัดส่ง</span>
                        <?php if ($total >= Yii::$app->params['freeShippingMinimum']): ?>
                        <span class="text-success">ฟรี</span>
                        <?php else: ?>
                        <span>฿100</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($total < Yii::$app->params['freeShippingMinimum']): ?>
                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        ซื้อเพิ่มอีก ฿<?= number_format(Yii::$app->params['freeShippingMinimum'] - $total) ?> รับส่งฟรี!
                    </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>รวมทั้งหมด</strong>
                        <strong class="text-primary fs-5">
                            ฿<?= number_format($total >= Yii::$app->params['freeShippingMinimum'] ? $total : $total + 100) ?>
                        </strong>
                    </div>
                    <div class="d-grid">
                        <a href="<?= Url::to(['/order/checkout']) ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card me-2"></i>ดำเนินการสั่งซื้อ
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Payment Methods -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3">ช่องทางชำระเงิน</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-light text-dark border"><i class="bi bi-bank me-1"></i>โอนเงิน</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-credit-card me-1"></i>บัตรเครดิต</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-cash me-1"></i>เก็บเงินปลายทาง</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h4 class="mt-4">ตะกร้าสินค้าว่างเปล่า</h4>
        <p class="text-muted">เลือกซื้อสินค้าได้เลย!</p>
        <a href="<?= Url::to(['/part/index']) ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-box me-2"></i>เลือกซื้อสินค้า
        </a>
    </div>
    <?php endif; ?>
</div>
