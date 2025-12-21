<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'ชำระเงิน';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/cart/index']) ?>">ตะกร้า</a></li>
                <li class="breadcrumb-item active">ชำระเงิน</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-credit-card text-primary me-2"></i>ชำระเงิน</h2>
    
    <?php $form = ActiveForm::begin(['id' => 'checkout-form']); ?>
    
    <div class="row">
        <div class="col-lg-7">
            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>ข้อมูลผู้สั่งซื้อ</h5>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->user->isGuest): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        มีบัญชีอยู่แล้ว? <a href="<?= Url::to(['/customer/login', 'redirect' => Url::to(['/order/checkout'])]) ?>">เข้าสู่ระบบ</a> เพื่อความสะดวก
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($customerModel, 'name')->textInput(['placeholder' => 'ชื่อ-นามสกุล'])->label('ชื่อ-นามสกุล <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($customerModel, 'phone')->textInput(['placeholder' => 'เบอร์โทรศัพท์'])->label('เบอร์โทร <span class="text-danger">*</span>') ?>
                        </div>
                    </div>
                    <?= $form->field($customerModel, 'email')->textInput(['type' => 'email', 'placeholder' => 'อีเมล'])->label('อีเมล <span class="text-danger">*</span>') ?>
                    <?php else: ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        สั่งซื้อในชื่อ: <strong><?= Html::encode(Yii::$app->user->identity->full_name) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>ที่อยู่จัดส่ง</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'shipping_address')->textarea([
                        'rows' => 3,
                        'placeholder' => 'บ้านเลขที่ ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์',
                    ])->label('ที่อยู่ <span class="text-danger">*</span>') ?>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2 text-primary"></i>วิธีชำระเงิน</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'payment_method')->radioList([
                        'transfer' => '<i class="bi bi-bank me-2"></i>โอนเงินผ่านธนาคาร',
                        'cod' => '<i class="bi bi-cash me-2"></i>เก็บเงินปลายทาง (+฿50)',
                    ], [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $checkedAttr = $index == 0 ? 'checked' : '';
                            return '<div class="form-check border rounded p-3 mb-2">
                                <input class="form-check-input" type="radio" name="' . $name . '" value="' . $value . '" id="payment_' . $value . '" ' . $checkedAttr . '>
                                <label class="form-check-label" for="payment_' . $value . '">' . $label . '</label>
                            </div>';
                        }
                    ])->label(false) ?>
                </div>
            </div>
            
            <!-- Note -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-chat-text me-2 text-primary"></i>หมายเหตุ</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'notes')->textarea([
                        'rows' => 2,
                        'placeholder' => 'หมายเหตุเพิ่มเติม (ถ้ามี)',
                    ])->label(false) ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>สรุปคำสั่งซื้อ</h5>
                </div>
                <div class="card-body">
                    <!-- Items -->
                    <div class="mb-3">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-2 position-relative">
                                <?php if ($item['part']->image): ?>
                                <img src="<?= $item['part']->image ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-box-seam text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                    <?= $item['qty'] ?>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <small class="d-block text-truncate" style="max-width: 200px;"><?= Html::encode($item['part']->name_th) ?></small>
                            </div>
                            <div class="text-end">
                                <small>฿<?= number_format($item['subtotal']) ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>รวมสินค้า</span>
                        <span>฿<?= number_format($subtotal) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ค่าจัดส่ง</span>
                        <?php if ($shippingCost == 0): ?>
                        <span class="text-success">ฟรี</span>
                        <?php else: ?>
                        <span>฿<?= number_format($shippingCost) ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <strong class="fs-5">รวมทั้งหมด</strong>
                        <strong class="fs-4 text-primary">฿<?= number_format($total) ?></strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>ยืนยันสั่งซื้อ
                        </button>
                        <a href="<?= Url::to(['/cart/index']) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>กลับไปตะกร้า
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>
