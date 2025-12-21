<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;

$this->title = 'ติดตามคำสั่งซื้อ';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">ติดตามคำสั่งซื้อ</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="text-center mb-4">
                <i class="bi bi-search text-primary me-2"></i>ติดตามคำสั่งซื้อ
            </h2>
            
            <!-- Search Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="get">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">หมายเลขคำสั่งซื้อ</label>
                                <input type="text" name="order_number" class="form-control" 
                                       placeholder="เช่น ORD-20241221-001" 
                                       value="<?= Html::encode($orderNumber) ?>">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">อีเมลที่ใช้สั่งซื้อ</label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="example@email.com"
                                       value="<?= Html::encode($email) ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> ค้นหา
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($orderNumber && $email): ?>
                <?php if ($order): ?>
                <!-- Order Found -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>พบคำสั่งซื้อ</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <small class="text-muted">หมายเลขคำสั่งซื้อ</small>
                                <div class="fs-5 fw-bold text-primary"><?= Html::encode($order->order_number) ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">วันที่สั่งซื้อ</small>
                                <div><?= Yii::$app->formatter->asDatetime($order->order_date, 'php:d/m/Y H:i') ?></div>
                            </div>
                        </div>
                        
                        <!-- Status Timeline -->
                        <?php
                        $statuses = [
                            Order::STATUS_PENDING => ['icon' => 'bi-hourglass', 'text' => 'รอดำเนินการ'],
                            Order::STATUS_CONFIRMED => ['icon' => 'bi-check-circle', 'text' => 'ยืนยันแล้ว'],
                            Order::STATUS_PROCESSING => ['icon' => 'bi-box-seam', 'text' => 'กำลังจัดเตรียม'],
                            Order::STATUS_SHIPPED => ['icon' => 'bi-truck', 'text' => 'จัดส่งแล้ว'],
                            Order::STATUS_DELIVERED => ['icon' => 'bi-house-check', 'text' => 'ส่งถึงแล้ว'],
                        ];
                        $currentIndex = array_search($order->status, array_keys($statuses));
                        ?>
                        <div class="d-flex justify-content-between position-relative mb-4">
                            <div class="progress position-absolute" style="height: 4px; top: 20px; left: 10%; right: 10%; z-index: 0;">
                                <div class="progress-bar bg-success" style="width: <?= $currentIndex !== false ? ($currentIndex / (count($statuses) - 1)) * 100 : 0 ?>%;"></div>
                            </div>
                            <?php $i = 0; foreach ($statuses as $key => $status): ?>
                            <div class="text-center position-relative" style="z-index: 1;">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center 
                                            <?= $i <= $currentIndex ? 'bg-success text-white' : 'bg-light text-muted' ?>"
                                     style="width: 45px; height: 45px;">
                                    <i class="bi <?= $status['icon'] ?>"></i>
                                </div>
                                <div class="mt-2 small <?= $i <= $currentIndex ? 'fw-bold' : 'text-muted' ?>">
                                    <?= $status['text'] ?>
                                </div>
                            </div>
                            <?php $i++; endforeach; ?>
                        </div>
                        
                        <?php if ($order->status == Order::STATUS_CANCELLED): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>คำสั่งซื้อนี้ถูกยกเลิก
                        </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <!-- Order Summary -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6>รายการสินค้า</h6>
                                <?php foreach ($order->orderItems as $item): ?>
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?= $item->part ? Html::encode($item->part->name_th) : 'สินค้า' ?> x<?= $item->quantity ?></span>
                                    <span>฿<?= number_format($item->subtotal) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6">
                                <h6>สรุปยอด</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>รวมสินค้า</span>
                                    <span>฿<?= number_format($order->subtotal) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>ค่าจัดส่ง</span>
                                    <span><?= $order->shipping_cost > 0 ? '฿' . number_format($order->shipping_cost) : 'ฟรี' ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>รวมทั้งหมด</strong>
                                    <strong class="text-primary">฿<?= number_format($order->total_amount) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Order Not Found -->
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ไม่พบคำสั่งซื้อ กรุณาตรวจสอบหมายเลขคำสั่งซื้อและอีเมลอีกครั้ง
                </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <p class="text-muted">มีบัญชีอยู่แล้ว? <a href="<?= Url::to(['/customer/login']) ?>">เข้าสู่ระบบ</a> เพื่อดูประวัติการสั่งซื้อทั้งหมด</p>
            </div>
        </div>
    </div>
</div>
