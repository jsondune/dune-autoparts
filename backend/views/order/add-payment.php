<?php
/**
 * Payment Form - บันทึกการชำระเงิน
 * @var yii\web\View $this
 * @var common\models\Order $order
 * @var common\models\Payment $model
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'บันทึกการชำระเงิน #' . $order->order_number;

$remaining = $order->grand_total - $order->paid_amount;
?>

<div class="payment-form">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['order/index']) ?>">คำสั่งซื้อ</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['order/view', 'id' => $order->id]) ?>">#<?= $order->order_number ?></a></li>
                    <li class="breadcrumb-item active">บันทึกชำระเงิน</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-credit-card"></i> ข้อมูลการชำระเงิน</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'payment_method')->dropDownList([
                                'transfer' => 'โอนเงิน',
                                'cash' => 'เงินสด',
                                'credit_card' => 'บัตรเครดิต',
                                'promptpay' => 'พร้อมเพย์',
                                'qr_code' => 'QR Code',
                                'cod' => 'เก็บเงินปลายทาง',
                            ], [
                                'class' => 'form-select',
                                'prompt' => '-- เลือกวิธีชำระเงิน --',
                            ]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'amount')->textInput([
                                'type' => 'number',
                                'min' => 0.01,
                                'max' => $remaining,
                                'step' => '0.01',
                                'value' => $remaining,
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'payment_date')->input('datetime-local', [
                                'value' => date('Y-m-d\TH:i'),
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                        
                        <div class="col-md-6">
                            <?= $form->field($model, 'reference_number')->textInput([
                                'placeholder' => 'เลขอ้างอิง, เลขที่ใบเสร็จ...',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                        
                        <!-- Bank Account Selection for Transfer -->
                        <div class="col-12" id="bankAccountSection" style="display: none;">
                            <label class="form-label">บัญชีรับโอน</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <div class="form-check bank-account-option">
                                        <input class="form-check-input" type="radio" name="Payment[bank_account]" value="kbank" id="bankKbank">
                                        <label class="form-check-label d-flex align-items-center" for="bankKbank">
                                            <span class="badge bg-success me-2">กสิกร</span>
                                            xxx-x-xxxxx-x
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check bank-account-option">
                                        <input class="form-check-input" type="radio" name="Payment[bank_account]" value="scb" id="bankScb">
                                        <label class="form-check-label d-flex align-items-center" for="bankScb">
                                            <span class="badge bg-primary me-2">ไทยพาณิชย์</span>
                                            xxx-x-xxxxx-x
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check bank-account-option">
                                        <input class="form-check-input" type="radio" name="Payment[bank_account]" value="bbl" id="bankBbl">
                                        <label class="form-check-label d-flex align-items-center" for="bankBbl">
                                            <span class="badge bg-info me-2">กรุงเทพ</span>
                                            xxx-x-xxxxx-x
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <?= $form->field($model, 'slip_image')->fileInput([
                                'class' => 'form-control',
                                'accept' => 'image/*',
                            ])->label('แนบสลิปโอนเงิน (ถ้ามี)') ?>
                            
                            <!-- Slip Preview -->
                            <div id="slipPreview" class="mt-2" style="display: none;">
                                <img id="slipPreviewImg" src="" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <?= $form->field($model, 'notes')->textarea([
                                'rows' => 3,
                                'placeholder' => 'หมายเหตุ...',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <?= Html::submitButton('<i class="bi bi-check-lg"></i> บันทึกการชำระเงิน', [
                    'class' => 'btn btn-primary btn-lg',
                ]) ?>
                <a href="<?= Url::to(['order/view', 'id' => $order->id]) ?>" class="btn btn-outline-secondary btn-lg">
                    ยกเลิก
                </a>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-receipt"></i> สรุปคำสั่งซื้อ</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">เลขที่คำสั่งซื้อ</td>
                            <td class="text-end"><code><?= $order->order_number ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ลูกค้า</td>
                            <td class="text-end"><?= Html::encode($order->customer->full_name ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ยอดรวม</td>
                            <td class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($order->grand_total) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ชำระแล้ว</td>
                            <td class="text-end text-success"><?= Yii::$app->formatter->asCurrency($order->paid_amount) ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold">ยอดคงเหลือ</td>
                            <td class="text-end fw-bold fs-5 text-danger"><?= Yii::$app->formatter->asCurrency($remaining) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Previous Payments -->
            <?php if ($order->payments): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> ประวัติการชำระ</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($order->payments as $payment): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold"><?= Yii::$app->formatter->asCurrency($payment->amount) ?></div>
                                <small class="text-muted">
                                    <?= Yii::$app->formatter->asDatetime($payment->payment_date, 'php:d/m/Y H:i') ?>
                                </small>
                            </div>
                            <span class="badge bg-<?= $payment->status == 'approved' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') ?>">
                                <?= $payment->status == 'approved' ? 'อนุมัติ' : ($payment->status == 'pending' ? 'รอตรวจ' : 'ไม่อนุมัติ') ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Show bank account section for transfer
$('#payment-payment_method').on('change', function() {
    if ($(this).val() === 'transfer' || $(this).val() === 'promptpay') {
        $('#bankAccountSection').show();
    } else {
        $('#bankAccountSection').hide();
    }
});

// Slip preview
$('#payment-slip_image').on('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#slipPreviewImg').attr('src', e.target.result);
            $('#slipPreview').show();
        };
        reader.readAsDataURL(file);
    } else {
        $('#slipPreview').hide();
    }
});
JS;
$this->registerJs($js);
?>
