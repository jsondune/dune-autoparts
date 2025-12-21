<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Order;

$this->title = 'ประวัติการสั่งซื้อ';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">ประวัติการสั่งซื้อ</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-bag text-primary me-2"></i>ประวัติการสั่งซื้อ</h2>
    
    <?php if (!empty($orders)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>หมายเลข</th>
                            <th>วันที่</th>
                            <th class="text-center">รายการ</th>
                            <th class="text-end">ยอดรวม</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">การชำระ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>" class="fw-bold text-decoration-none">
                                    <?= Html::encode($order->order_number) ?>
                                </a>
                            </td>
                            <td><?= Yii::$app->formatter->asDate($order->order_date, 'php:d/m/Y') ?></td>
                            <td class="text-center"><?= count($order->orderItems) ?> รายการ</td>
                            <td class="text-end fw-bold">฿<?= number_format($order->total_amount) ?></td>
                            <td class="text-center">
                                <?php
                                $statusClass = [
                                    Order::STATUS_PENDING => 'warning',
                                    Order::STATUS_CONFIRMED => 'info',
                                    Order::STATUS_PROCESSING => 'primary',
                                    Order::STATUS_SHIPPED => 'info',
                                    Order::STATUS_DELIVERED => 'success',
                                    Order::STATUS_CANCELLED => 'danger',
                                ];
                                $statusText = [
                                    Order::STATUS_PENDING => 'รอดำเนินการ',
                                    Order::STATUS_CONFIRMED => 'ยืนยันแล้ว',
                                    Order::STATUS_PROCESSING => 'กำลังจัดเตรียม',
                                    Order::STATUS_SHIPPED => 'จัดส่งแล้ว',
                                    Order::STATUS_DELIVERED => 'ส่งถึงแล้ว',
                                    Order::STATUS_CANCELLED => 'ยกเลิก',
                                ];
                                ?>
                                <span class="badge bg-<?= $statusClass[$order->status] ?? 'secondary' ?>">
                                    <?= $statusText[$order->status] ?? $order->status ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $paymentClass = [
                                    Order::PAYMENT_PENDING => 'warning',
                                    Order::PAYMENT_PAID => 'success',
                                    Order::PAYMENT_PARTIAL => 'info',
                                    Order::PAYMENT_REFUNDED => 'secondary',
                                ];
                                $paymentText = [
                                    Order::PAYMENT_PENDING => 'รอชำระ',
                                    Order::PAYMENT_PAID => 'ชำระแล้ว',
                                    Order::PAYMENT_PARTIAL => 'ชำระบางส่วน',
                                    Order::PAYMENT_REFUNDED => 'คืนเงิน',
                                ];
                                ?>
                                <span class="badge bg-<?= $paymentClass[$order->payment_status] ?? 'secondary' ?>">
                                    <?= $paymentText[$order->payment_status] ?? $order->payment_status ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-bag-x display-1 text-muted"></i>
        <h4 class="mt-4">ยังไม่มีประวัติการสั่งซื้อ</h4>
        <p class="text-muted">เริ่มต้นสั่งซื้อสินค้าได้เลย!</p>
        <a href="<?= Url::to(['/part/index']) ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-box me-2"></i>เลือกซื้อสินค้า
        </a>
    </div>
    <?php endif; ?>
</div>
