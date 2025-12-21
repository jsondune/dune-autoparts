<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;

$this->title = 'คำสั่งซื้อ ' . $order->order_number;
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/order/history']) ?>">ประวัติสั่งซื้อ</a></li>
                <li class="breadcrumb-item active"><?= Html::encode($order->order_number) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-receipt text-primary me-2"></i>คำสั่งซื้อ <?= Html::encode($order->order_number) ?>
        </h2>
        <a href="<?= Url::to(['/order/history']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>กลับ
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Status Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>สถานะคำสั่งซื้อ</h5>
                </div>
                <div class="card-body">
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
                    <div class="d-flex justify-content-between position-relative">
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
                    <div class="alert alert-danger mt-3 mb-0">
                        <i class="bi bi-x-circle me-2"></i>คำสั่งซื้อนี้ถูกยกเลิก
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-box me-2 text-primary"></i>รายการสินค้า</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-center">ราคา</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order->orderItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item->part && $item->part->image): ?>
                                            <img src="<?= $item->part->image ?>" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-box-seam text-muted"></i>
                                            </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= $item->part ? Html::encode($item->part->name_th) : 'สินค้าถูกลบ' ?></strong>
                                                <br>
                                                <small class="text-muted"><?= $item->part ? Html::encode($item->part->part_number) : '' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">฿<?= number_format($item->unit_price) ?></td>
                                    <td class="text-center"><?= $item->quantity ?></td>
                                    <td class="text-end fw-bold">฿<?= number_format($item->subtotal) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>ที่อยู่จัดส่ง</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(Html::encode($order->shipping_address)) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>สรุปคำสั่งซื้อ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">วันที่สั่งซื้อ</small>
                        <div class="fw-bold"><?= Yii::$app->formatter->asDatetime($order->order_date, 'php:d/m/Y H:i') ?></div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>รวมสินค้า</span>
                        <span>฿<?= number_format($order->subtotal) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ค่าจัดส่ง</span>
                        <span><?= $order->shipping_cost > 0 ? '฿' . number_format($order->shipping_cost) : 'ฟรี' ?></span>
                    </div>
                    <?php if ($order->discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>ส่วนลด</span>
                        <span>-฿<?= number_format($order->discount) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <strong class="fs-5">รวมทั้งหมด</strong>
                        <strong class="fs-4 text-primary">฿<?= number_format($order->total_amount) ?></strong>
                    </div>
                </div>
            </div>
            
            <!-- Payment Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2 text-primary"></i>การชำระเงิน</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">วิธีชำระเงิน</small>
                        <div class="fw-bold">
                            <?php
                            $methods = [
                                'transfer' => 'โอนเงินธนาคาร',
                                'cod' => 'เก็บเงินปลายทาง',
                                'credit_card' => 'บัตรเครดิต',
                            ];
                            echo $methods[$order->payment_method] ?? $order->payment_method;
                            ?>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">สถานะการชำระ</small>
                        <div>
                            <?php
                            $paymentClass = [
                                Order::PAYMENT_PENDING => 'warning',
                                Order::PAYMENT_PAID => 'success',
                                Order::PAYMENT_PARTIAL => 'info',
                                Order::PAYMENT_REFUNDED => 'secondary',
                            ];
                            $paymentText = [
                                Order::PAYMENT_PENDING => 'รอชำระเงิน',
                                Order::PAYMENT_PAID => 'ชำระเงินแล้ว',
                                Order::PAYMENT_PARTIAL => 'ชำระบางส่วน',
                                Order::PAYMENT_REFUNDED => 'คืนเงินแล้ว',
                            ];
                            ?>
                            <span class="badge bg-<?= $paymentClass[$order->payment_status] ?? 'secondary' ?>">
                                <?= $paymentText[$order->payment_status] ?? $order->payment_status ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($order->payment_status == Order::PAYMENT_PENDING && $order->payment_method == 'transfer'): ?>
                    <hr>
                    <div class="alert alert-info mb-0">
                        <small>
                            <strong>ข้อมูลการโอนเงิน</strong><br>
                            ธนาคารกสิกรไทย<br>
                            ชื่อบัญชี: บจก. ดูนส์ ออโต้ พาร์ท<br>
                            เลขบัญชี: xxx-x-xxxxx-x<br>
                            <hr>
                            หลังโอนเงินแจ้งสลิปทาง Line: <?= Yii::$app->params['shopLine'] ?>
                        </small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
