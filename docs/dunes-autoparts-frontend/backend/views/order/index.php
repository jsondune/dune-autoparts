<?php
/**
 * Order Index - รายการคำสั่งซื้อ
 * @var yii\web\View $this
 * @var backend\models\OrderSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $stats
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'จัดการคำสั่งซื้อ';
?>

<div class="order-index">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">รายการคำสั่งซื้อทั้งหมด</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['export']) ?>" class="btn btn-outline-success">
                <i class="bi bi-download me-1"></i> Export
            </a>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> สร้างคำสั่งซื้อ
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col">
            <a href="<?= Url::to(['index', 'OrderSearch[status]' => 'pending']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 <?= Yii::$app->request->get('OrderSearch')['status'] ?? '' == 'pending' ? 'border-warning' : '' ?>">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-warning"><?= $stats['pending'] ?></h4>
                        <small class="text-muted">รอยืนยัน</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'OrderSearch[status]' => 'confirmed']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-info"><?= $stats['confirmed'] ?></h4>
                        <small class="text-muted">ยืนยันแล้ว</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'OrderSearch[status]' => 'preparing']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-primary"><?= $stats['preparing'] ?></h4>
                        <small class="text-muted">กำลังเตรียม</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'OrderSearch[status]' => 'shipped']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-success"><?= $stats['shipped'] ?></h4>
                        <small class="text-muted">จัดส่งแล้ว</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'OrderSearch[payment_status]' => 'unpaid']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 <?= $stats['unpaid'] > 0 ? 'border-danger' : '' ?>">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-danger"><?= $stats['unpaid'] ?></h4>
                        <small class="text-muted">รอชำระ</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <?php $form = \yii\widgets\ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'row g-3'],
            ]); ?>
            
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <?= Html::activeTextInput($searchModel, 'search', [
                        'class' => 'form-control',
                        'placeholder' => 'เลขที่, เลขพัสดุ, ชื่อ, เบอร์...',
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'status', [
                    'pending' => 'รอยืนยัน',
                    'confirmed' => 'ยืนยันแล้ว',
                    'preparing' => 'กำลังเตรียม',
                    'shipped' => 'จัดส่งแล้ว',
                    'delivered' => 'ส่งถึงแล้ว',
                    'cancelled' => 'ยกเลิก',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- สถานะทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'payment_status', [
                    'unpaid' => 'ยังไม่ชำระ',
                    'partial' => 'ชำระบางส่วน',
                    'paid' => 'ชำระแล้ว',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- การชำระทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeInput('date', $searchModel, 'date_from', [
                    'class' => 'form-control',
                    'placeholder' => 'วันที่เริ่ม',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeInput('date', $searchModel, 'date_to', [
                    'class' => 'form-control',
                    'placeholder' => 'วันที่สิ้นสุด',
                ]) ?>
            </div>
            
            <div class="col-md-1">
                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'btn btn-primary']) ?>
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
            
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?= $dataProvider->sort->link('order_number', ['label' => 'เลขที่']) ?></th>
                            <th><?= $dataProvider->sort->link('created_at', ['label' => 'วันที่']) ?></th>
                            <th>ลูกค้า</th>
                            <th>รายการ</th>
                            <th class="text-end"><?= $dataProvider->sort->link('grand_total', ['label' => 'ยอดรวม']) ?></th>
                            <th class="text-center"><?= $dataProvider->sort->link('status', ['label' => 'สถานะ']) ?></th>
                            <th class="text-center"><?= $dataProvider->sort->link('payment_status', ['label' => 'ชำระ']) ?></th>
                            <th class="text-center" style="width: 100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dataProvider->getCount() == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    ไม่พบคำสั่งซื้อ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->getModels() as $order): ?>
                                <tr>
                                    <td>
                                        <a href="<?= Url::to(['view', 'id' => $order->id]) ?>" class="fw-bold">
                                            <?= Html::encode($order->order_number) ?>
                                        </a>
                                        <?php if ($order->tracking_number): ?>
                                            <br><small class="text-muted"><i class="bi bi-truck"></i> <?= Html::encode($order->tracking_number) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d/m/Y') ?>
                                        <br><small class="text-muted"><?= Yii::$app->formatter->asDatetime($order->created_at, 'php:H:i') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($order->customer): ?>
                                            <a href="<?= Url::to(['/customer/view', 'id' => $order->customer_id]) ?>">
                                                <?= Html::encode($order->customer->getDisplayName()) ?>
                                            </a>
                                        <?php else: ?>
                                            <?= Html::encode($order->shipping_name) ?: '<span class="text-muted">-</span>' ?>
                                        <?php endif; ?>
                                        <?php if ($order->shipping_phone): ?>
                                            <br><small class="text-muted"><i class="bi bi-telephone"></i> <?= Html::encode($order->shipping_phone) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $itemCount = count($order->items); ?>
                                        <span class="badge bg-light text-dark"><?= $itemCount ?> รายการ</span>
                                        <?php if ($itemCount > 0): ?>
                                            <br><small class="text-muted"><?= Html::encode(mb_substr($order->items[0]->part->name_th ?? '', 0, 20)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong><?= Yii::$app->formatter->asCurrency($order->grand_total, 'THB') ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?= $order->getStatusBadge() ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $order->getPaymentStatusBadge() ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= Url::to(['view', 'id' => $order->id]) ?>" 
                                               class="btn btn-outline-primary" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= Url::to(['print', 'id' => $order->id]) ?>" 
                                               class="btn btn-outline-secondary" title="พิมพ์" target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($dataProvider->getTotalCount() > $dataProvider->pagination->pageSize): ?>
        <div class="card-footer bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= number_format($dataProvider->getTotalCount()) ?> รายการ
                </div>
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
