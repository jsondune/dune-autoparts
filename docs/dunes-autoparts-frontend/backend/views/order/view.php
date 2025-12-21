<?php
/**
 * Order View - รายละเอียดคำสั่งซื้อ
 * @var yii\web\View $this
 * @var common\models\Order $model
 * @var array $payments
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'คำสั่งซื้อ #' . $model->order_number;
?>

<div class="order-view">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">คำสั่งซื้อ</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($model->order_number) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <?= Html::encode($model->order_number) ?>
                <?= $model->getStatusBadge() ?>
                <?= $model->getPaymentStatusBadge() ?>
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['print', 'id' => $model->id]) ?>" class="btn btn-outline-secondary" target="_blank">
                <i class="bi bi-printer me-1"></i> พิมพ์
            </a>
            <?php if (in_array($model->status, ['pending', 'confirmed'])): ?>
                <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> แก้ไข
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Timeline -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center position-relative px-4">
                <div class="position-absolute" style="left: 60px; right: 60px; top: 50%; height: 3px; background: #e9ecef; z-index: 0;"></div>
                
                <?php 
                $statuses = ['pending', 'confirmed', 'preparing', 'shipped', 'delivered'];
                $statusLabels = [
                    'pending' => 'รอยืนยัน',
                    'confirmed' => 'ยืนยันแล้ว',
                    'preparing' => 'เตรียมสินค้า',
                    'shipped' => 'จัดส่งแล้ว',
                    'delivered' => 'สำเร็จ',
                ];
                $currentIndex = array_search($model->status, $statuses);
                if ($model->status == 'cancelled') $currentIndex = -1;
                
                foreach ($statuses as $i => $status):
                    $isActive = ($i <= $currentIndex);
                    $isCurrent = ($model->status == $status);
                ?>
                <div class="text-center position-relative" style="z-index: 1;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 
                         <?= $isActive ? 'bg-success text-white' : 'bg-light text-muted' ?> 
                         <?= $isCurrent ? 'border border-3 border-success' : '' ?>"
                         style="width: 40px; height: 40px;">
                        <?php if ($isActive): ?>
                            <i class="bi bi-check-lg"></i>
                        <?php else: ?>
                            <?= $i + 1 ?>
                        <?php endif; ?>
                    </div>
                    <small class="<?= $isActive ? 'text-success fw-medium' : 'text-muted' ?>">
                        <?= $statusLabels[$status] ?>
                    </small>
                </div>
                <?php endforeach; ?>
                
                <?php if ($model->status == 'cancelled'): ?>
                <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.9); z-index: 2;">
                    <div class="text-center">
                        <span class="badge bg-danger fs-5 px-4 py-2">ยกเลิกแล้ว</span>
                        <?php if ($model->cancel_reason): ?>
                            <p class="text-muted mt-2 mb-0">เหตุผล: <?= Html::encode($model->cancel_reason) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-xl-8">
            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>รายการสินค้า (<?= count($model->items) ?>)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;"></th>
                                    <th>สินค้า</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">ราคา/ชิ้น</th>
                                    <th class="text-end">ส่วนลด</th>
                                    <th class="text-end">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($model->items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php if ($item->part && $item->part->main_image): ?>
                                                <img src="<?= Yii::getAlias('@web/uploads/parts/' . $item->part->main_image) ?>" 
                                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item->part): ?>
                                                <a href="<?= Url::to(['/part/view', 'id' => $item->part_id]) ?>">
                                                    <?= Html::encode($item->part->sku) ?>
                                                </a>
                                                <br><span class="text-muted"><?= Html::encode($item->part->name_th ?: $item->part->name_en) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">สินค้าถูกลบ</span>
                                            <?php endif; ?>
                                            <?php if ($item->warranty_days > 0): ?>
                                                <br><small class="badge bg-info">รับประกัน <?= $item->warranty_days ?> วัน</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $item->quantity ?></td>
                                        <td class="text-end"><?= Yii::$app->formatter->asCurrency($item->unit_price, 'THB') ?></td>
                                        <td class="text-end">
                                            <?= $item->discount > 0 ? '-' . Yii::$app->formatter->asCurrency($item->discount, 'THB') : '-' ?>
                                        </td>
                                        <td class="text-end fw-medium"><?= Yii::$app->formatter->asCurrency($item->line_total, 'THB') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end">ยอดสินค้า:</td>
                                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->subtotal, 'THB') ?></td>
                                </tr>
                                <?php if ($model->discount_amount > 0): ?>
                                <tr>
                                    <td colspan="5" class="text-end text-danger">ส่วนลด:</td>
                                    <td class="text-end text-danger">-<?= Yii::$app->formatter->asCurrency($model->discount_amount, 'THB') ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="5" class="text-end">ค่าจัดส่ง:</td>
                                    <td class="text-end"><?= Yii::$app->formatter->asCurrency($model->shipping_fee, 'THB') ?></td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">ยอดรวมทั้งหมด:</td>
                                    <td class="text-end text-primary fs-5"><?= Yii::$app->formatter->asCurrency($model->grand_total, 'THB') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>การชำระเงิน</h5>
                    <?php if ($model->status != 'cancelled' && $model->payment_status != 'paid'): ?>
                        <a href="<?= Url::to(['add-payment', 'id' => $model->id]) ?>" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg me-1"></i> บันทึกการชำระ
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <!-- Payment Summary -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 text-center">
                            <div class="text-muted small">ยอดที่ต้องชำระ</div>
                            <div class="h4 mb-0"><?= Yii::$app->formatter->asCurrency($model->grand_total, 'THB') ?></div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-muted small">ชำระแล้ว</div>
                            <div class="h4 mb-0 text-success"><?= Yii::$app->formatter->asCurrency($model->getTotalPaid(), 'THB') ?></div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="text-muted small">คงเหลือ</div>
                            <div class="h4 mb-0 <?= $model->getRemainingAmount() > 0 ? 'text-danger' : 'text-success' ?>">
                                <?= Yii::$app->formatter->asCurrency($model->getRemainingAmount(), 'THB') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Payment List -->
                    <?php if (empty($payments)): ?>
                        <p class="text-muted text-center mb-0">ยังไม่มีการชำระเงิน</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>ช่องทาง</th>
                                        <th class="text-end">จำนวน</th>
                                        <th class="text-center">สลิป</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td>
                                                <?= Yii::$app->formatter->asDatetime($payment->payment_date ?: $payment->created_at, 'php:d/m/Y H:i') ?>
                                            </td>
                                            <td><?= Html::encode($payment->payment_method) ?></td>
                                            <td class="text-end fw-medium"><?= Yii::$app->formatter->asCurrency($payment->amount, 'THB') ?></td>
                                            <td class="text-center">
                                                <?php if ($payment->slip_image): ?>
                                                    <a href="<?= Yii::getAlias('@web/uploads/payments/' . $payment->slip_image) ?>" 
                                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-image"></i> ดูสลิป
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $payment->getStatusBadge() ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($payment->status == 'pending'): ?>
                                                    <?= Html::beginForm(['verify-payment', 'payment_id' => $payment->id], 'post', ['class' => 'd-inline']) ?>
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('ยืนยันอนุมัติการชำระเงินนี้?')">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <?= Html::endForm() ?>
                                                    
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectModal" 
                                                            data-payment-id="<?= $payment->id ?>">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes -->
            <?php if ($model->notes): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>หมายเหตุ</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(Html::encode($model->notes)) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Actions -->
            <?php if ($model->status != 'cancelled' && $model->status != 'delivered'): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>ดำเนินการ</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($model->status == 'pending'): ?>
                            <?= Html::beginForm(['confirm', 'id' => $model->id], 'post') ?>
                            <button type="submit" class="btn btn-success" onclick="return confirm('ยืนยันคำสั่งซื้อนี้?')">
                                <i class="bi bi-check-circle me-1"></i> ยืนยันคำสั่งซื้อ
                            </button>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                        
                        <?php if ($model->status == 'confirmed'): ?>
                            <?= Html::beginForm(['prepare', 'id' => $model->id], 'post') ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-seam me-1"></i> เริ่มเตรียมสินค้า
                            </button>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                        
                        <?php if ($model->status == 'preparing'): ?>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#shipModal">
                                <i class="bi bi-truck me-1"></i> จัดส่งสินค้า
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($model->status == 'shipped'): ?>
                            <?= Html::beginForm(['deliver', 'id' => $model->id], 'post') ?>
                            <button type="submit" class="btn btn-success" onclick="return confirm('ยืนยันว่าสินค้าถึงลูกค้าแล้ว?')">
                                <i class="bi bi-check-all me-1"></i> ยืนยันส่งถึงแล้ว
                            </button>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                        
                        <?php if (in_array($model->status, ['pending', 'confirmed'])): ?>
                            <hr class="my-2">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-1"></i> ยกเลิกคำสั่งซื้อ
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>ข้อมูลลูกค้า</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->customer): ?>
                        <p class="mb-2">
                            <a href="<?= Url::to(['/customer/view', 'id' => $model->customer_id]) ?>" class="fw-medium">
                                <?= Html::encode($model->customer->getDisplayName()) ?>
                            </a>
                        </p>
                        <?php if ($model->customer->phone): ?>
                            <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i><?= Html::encode($model->customer->phone) ?></p>
                        <?php endif; ?>
                        <?php if ($model->customer->email): ?>
                            <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i><?= Html::encode($model->customer->email) ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">ไม่มีข้อมูลลูกค้า</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-truck me-2"></i>ข้อมูลจัดส่ง</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong><?= Html::encode($model->shipping_name) ?: '-' ?></strong></p>
                    <?php if ($model->shipping_phone): ?>
                        <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i><?= Html::encode($model->shipping_phone) ?></p>
                    <?php endif; ?>
                    <?php if ($model->shipping_address): ?>
                        <p class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i><?= nl2br(Html::encode($model->shipping_address)) ?></p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">ขนส่ง</small>
                            <span class="badge bg-light text-dark"><?= Html::encode($model->shipping_method) ?: 'ไม่ระบุ' ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">ค่าส่ง</small>
                            <?= Yii::$app->formatter->asCurrency($model->shipping_fee, 'THB') ?>
                        </div>
                    </div>
                    
                    <?php if ($model->tracking_number): ?>
                        <hr>
                        <div>
                            <small class="text-muted d-block">เลขพัสดุ</small>
                            <span class="fs-5 fw-bold text-primary"><?= Html::encode($model->tracking_number) ?></span>
                        </div>
                        <?php if ($model->shipped_at): ?>
                            <small class="text-muted">ส่งเมื่อ <?= Yii::$app->formatter->asDatetime($model->shipped_at, 'php:d/m/Y H:i') ?></small>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลระบบ</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">สร้างเมื่อ</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                        <?php if ($model->confirmed_at): ?>
                        <tr>
                            <td class="text-muted">ยืนยันเมื่อ</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->confirmed_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($model->shipped_at): ?>
                        <tr>
                            <td class="text-muted">จัดส่งเมื่อ</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->shipped_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($model->delivered_at): ?>
                        <tr>
                            <td class="text-muted">ส่งถึงเมื่อ</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->delivered_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">แก้ไขล่าสุด</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ship Modal -->
<div class="modal fade" id="shipModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['ship', 'id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">บันทึกการจัดส่ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ขนส่ง</label>
                    <select name="shipping_method" class="form-select" required>
                        <option value="">เลือกขนส่ง</option>
                        <option value="Kerry">Kerry Express</option>
                        <option value="Flash">Flash Express</option>
                        <option value="J&T">J&T Express</option>
                        <option value="EMS">ไปรษณีย์ EMS</option>
                        <option value="ลงทะเบียน">ไปรษณีย์ลงทะเบียน</option>
                        <option value="รับเอง">ลูกค้ารับเอง</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">เลขพัสดุ</label>
                    <input type="text" name="tracking_number" class="form-control" required placeholder="กรอกเลขพัสดุ">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-truck me-1"></i> บันทึกการจัดส่ง
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['cancel', 'id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">ยกเลิกคำสั่งซื้อ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    การยกเลิกจะไม่สามารถเปลี่ยนกลับได้
                </div>
                <div class="mb-3">
                    <label class="form-label">เหตุผลในการยกเลิก</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="ระบุเหตุผล..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i> ยืนยันยกเลิก
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reject-form" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="action" value="reject">
                
                <div class="modal-header">
                    <h5 class="modal-title">ปฏิเสธการชำระเงิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">เหตุผล</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="ระบุเหตุผล..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">ปฏิเสธ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$baseUrl = Url::to(['verify-payment', 'payment_id' => '']);
$js = <<<JS
$('#rejectModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var paymentId = button.data('payment-id');
    $(this).find('#reject-form').attr('action', '{$baseUrl}' + paymentId);
});
JS;
$this->registerJs($js);
?>
