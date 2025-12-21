<?php
/**
 * Order Update - แก้ไขคำสั่งซื้อ
 * @var yii\web\View $this
 * @var common\models\Order $model
 * @var array $customers
 * @var common\models\Customer $selectedCustomer
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'แก้ไขคำสั่งซื้อ #' . $model->order_number;
?>

<div class="order-update">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">คำสั่งซื้อ</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['view', 'id' => $model->id]) ?>">#<?= $model->order_number ?></a></li>
                    <li class="breadcrumb-item active">แก้ไข</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
        <div>
            <span class="badge bg-<?= $model->getStatusClass() ?> fs-6"><?= $model->getStatusLabel() ?></span>
        </div>
    </div>

    <?php if ($model->order_status !== 'pending'): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>หมายเหตุ:</strong> คำสั่งซื้อนี้อยู่ในสถานะ "<?= $model->getStatusLabel() ?>" การแก้ไขอาจส่งผลต่อการจัดส่งหรือสต็อกสินค้า
    </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers ?? [],
        'selectedCustomer' => $model->customer,
    ]) ?>
</div>
