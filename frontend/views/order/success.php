<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'สั่งซื้อสำเร็จ';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="mb-3">สั่งซื้อสำเร็จ!</h2>
                    <p class="lead mb-4">ขอบคุณที่สั่งซื้อสินค้ากับเรา</p>
                    
                    <div class="bg-light rounded p-4 mb-4">
                        <p class="mb-2">หมายเลขคำสั่งซื้อ</p>
                        <h3 class="text-primary mb-3"><?= Html::encode($order->order_number) ?></h3>
                        <p class="mb-1"><strong>วันที่สั่งซื้อ:</strong> <?= Yii::$app->formatter->asDatetime($order->order_date, 'php:d/m/Y H:i') ?></p>
                        <p class="mb-1"><strong>ยอดรวม:</strong> ฿<?= number_format($order->total_amount) ?></p>
                        <p class="mb-0"><strong>สถานะ:</strong> 
                            <span class="badge bg-warning text-dark">รอชำระเงิน</span>
                        </p>
                    </div>
                    
                    <?php if ($order->payment_method == 'transfer'): ?>
                    <div class="alert alert-info text-start">
                        <h6><i class="bi bi-bank me-2"></i>ข้อมูลการโอนเงิน</h6>
                        <p class="mb-1"><strong>ธนาคาร:</strong> กสิกรไทย</p>
                        <p class="mb-1"><strong>ชื่อบัญชี:</strong> บจก. ดูนส์ ออโต้ พาร์ท</p>
                        <p class="mb-1"><strong>เลขบัญชี:</strong> xxx-x-xxxxx-x</p>
                        <p class="mb-0"><strong>ยอดโอน:</strong> ฿<?= number_format($order->total_amount) ?></p>
                        <hr>
                        <small class="text-muted">หลังโอนเงินแล้ว กรุณาแจ้งสลิปทาง Line: <?= Yii::$app->params['shopLine'] ?></small>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success text-start">
                        <h6><i class="bi bi-cash me-2"></i>เก็บเงินปลายทาง</h6>
                        <p class="mb-0">กรุณาเตรียมเงินสด ฿<?= number_format($order->total_amount + 50) ?> (รวมค่าบริการ COD ฿50)</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?= Url::to(['/site/index']) ?>" class="btn btn-primary">
                            <i class="bi bi-house me-2"></i>กลับหน้าแรก
                        </a>
                        <?php if (!Yii::$app->user->isGuest): ?>
                        <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>ดูรายละเอียด
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
