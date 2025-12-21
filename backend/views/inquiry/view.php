<?php
/**
 * Inquiry View - หน้ารายละเอียดและแชทสอบถาม
 * @var yii\web\View $this
 * @var common\models\Inquiry $model
 * @var array $messages
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'สอบถาม #' . $model->inquiry_number;

$channelLabels = [
    'line' => ['Line', 'bi-line', 'success'],
    'facebook' => ['Facebook', 'bi-facebook', 'primary'],
    'phone' => ['โทรศัพท์', 'bi-telephone', 'info'],
    'walk_in' => ['หน้าร้าน', 'bi-shop', 'warning'],
    'web' => ['เว็บไซต์', 'bi-globe', 'secondary'],
    'ai_bot' => ['AI Bot', 'bi-robot', 'purple'],
];
$channel = $channelLabels[$model->channel] ?? ['อื่นๆ', 'bi-chat', 'secondary'];

$statusOptions = [
    'new' => ['ใหม่', 'warning'],
    'in_progress' => ['กำลังดำเนินการ', 'info'],
    'waiting' => ['รอลูกค้าตอบ', 'secondary'],
    'resolved' => ['แก้ไขแล้ว', 'success'],
    'closed' => ['ปิดแล้ว', 'dark'],
];
?>

<div class="inquiry-view">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">สอบถาม</a></li>
                    <li class="breadcrumb-item active">#<?= Html::encode($model->inquiry_number) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="bi <?= $channel[1] ?> text-<?= $channel[2] ?> me-2"></i>
                <?= Html::encode($model->subject ?: 'สอบถาม #' . $model->inquiry_number) ?>
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Chat Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-<?= $channel[2] ?> me-2">
                            <i class="bi <?= $channel[1] ?>"></i> <?= $channel[0] ?>
                        </span>
                        <?= $model->getStatusBadge() ?>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> จัดการ
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($model->status != 'in_progress'): ?>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="in_progress">
                                    <i class="bi bi-play-circle text-info"></i> เริ่มดำเนินการ
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if ($model->status != 'waiting'): ?>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="waiting">
                                    <i class="bi bi-hourglass text-secondary"></i> รอลูกค้าตอบ
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if ($model->status != 'resolved'): ?>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="resolved">
                                    <i class="bi bi-check-circle text-success"></i> แก้ไขแล้ว
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($model->status != 'closed'): ?>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#closeModal">
                                    <i class="bi bi-x-circle"></i> ปิดการสอบถาม
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Chat Messages -->
                <div class="card-body chat-messages p-3" id="chatMessages" style="height: 450px; overflow-y: auto;">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                            ยังไม่มีข้อความ
                        </div>
                    <?php else: ?>
                        <?php 
                        $currentDate = null;
                        foreach ($messages as $message): 
                            $messageDate = date('Y-m-d', $message->created_at);
                            if ($currentDate !== $messageDate):
                                $currentDate = $messageDate;
                        ?>
                            <div class="text-center my-3">
                                <span class="badge bg-light text-muted px-3 py-2">
                                    <?= Yii::$app->formatter->asDate($message->created_at, 'php:d F Y') ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex mb-3 <?= $message->sender_type == 'staff' ? 'justify-content-end' : 'justify-content-start' ?>">
                            <?php if ($message->sender_type != 'staff'): ?>
                            <div class="flex-shrink-0 me-2">
                                <div class="rounded-circle bg-<?= $channel[2] ?> d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="message-bubble <?= $message->sender_type == 'staff' ? 'message-staff' : 'message-customer' ?>" style="max-width: 75%;">
                                <?php if ($message->sender_type == 'ai_bot'): ?>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-robot"></i> AI Bot
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($message->message_type == 'image' && $message->attachment): ?>
                                    <div class="mb-2">
                                        <a href="<?= $message->attachment ?>" target="_blank">
                                            <img src="<?= $message->attachment ?>" class="img-fluid rounded" style="max-width: 250px;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($message->message): ?>
                                    <div class="message-text"><?= nl2br(Html::encode($message->message)) ?></div>
                                <?php endif; ?>
                                
                                <div class="message-time text-end">
                                    <?= Yii::$app->formatter->asDatetime($message->created_at, 'php:H:i') ?>
                                    <?php if ($message->sender_type == 'staff' && $message->is_read): ?>
                                        <i class="bi bi-check2-all text-primary"></i>
                                    <?php elseif ($message->sender_type == 'staff'): ?>
                                        <i class="bi bi-check2"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($message->sender_type == 'staff'): ?>
                            <div class="flex-shrink-0 ms-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-headset text-white"></i>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Reply Form -->
                <?php if ($model->status != 'closed'): ?>
                <div class="card-footer bg-white border-top">
                    <?php $form = ActiveForm::begin([
                        'action' => ['send-message', 'id' => $model->id],
                        'options' => ['id' => 'replyForm', 'enctype' => 'multipart/form-data'],
                    ]); ?>
                    
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <textarea name="message" class="form-control" rows="2" 
                                      placeholder="พิมพ์ข้อความตอบกลับ..." required
                                      id="messageInput"></textarea>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <label class="btn btn-outline-secondary btn-sm" title="แนบรูป">
                                <i class="bi bi-image"></i>
                                <input type="file" name="attachment" accept="image/*" class="d-none">
                            </label>
                            <button type="submit" class="btn btn-primary btn-sm" title="ส่ง">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Replies -->
                    <div class="mt-2">
                        <span class="small text-muted me-2">ตอบด่วน:</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-text="สวัสดีครับ ขอบคุณที่ติดต่อ ดูน ออโต้ พาร์ท">
                            สวัสดี
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-text="กรุณารอสักครู่ครับ กำลังตรวจสอบข้อมูลให้">
                            รอสักครู่
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-text="สินค้ามีในสต็อกครับ พร้อมจัดส่งได้เลย">
                            มีสินค้า
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-text="ขออภัยครับ ขณะนี้สินค้าหมดชั่วคราว">
                            สินค้าหมด
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-text="ขอบคุณที่ใช้บริการครับ 🙏">
                            ขอบคุณ
                        </button>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
                <?php else: ?>
                <div class="card-footer bg-light text-center text-muted">
                    <i class="bi bi-lock"></i> การสอบถามนี้ถูกปิดแล้ว
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person"></i> ข้อมูลลูกค้า</h6>
                </div>
                <div class="card-body">
                    <?php if ($model->customer): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <span class="text-white fs-5"><?= mb_substr($model->customer->full_name, 0, 1) ?></span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0"><?= Html::encode($model->customer->full_name) ?></h6>
                                <small class="text-muted"><?= Html::encode($model->customer->customer_code) ?></small>
                            </div>
                        </div>
                        
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="100">เบอร์โทร</td>
                                <td>
                                    <a href="tel:<?= $model->customer->phone ?>">
                                        <?= Html::encode($model->customer->phone) ?>
                                    </a>
                                </td>
                            </tr>
                            <?php if ($model->customer->email): ?>
                            <tr>
                                <td class="text-muted">อีเมล</td>
                                <td>
                                    <a href="mailto:<?= $model->customer->email ?>">
                                        <?= Html::encode($model->customer->email) ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="text-muted">ออเดอร์</td>
                                <td><?= number_format($model->customer->total_orders) ?> ครั้ง</td>
                            </tr>
                            <tr>
                                <td class="text-muted">ยอดซื้อ</td>
                                <td><?= Yii::$app->formatter->asCurrency($model->customer->total_spent) ?></td>
                            </tr>
                        </table>
                        
                        <div class="mt-3">
                            <a href="<?= Url::to(['customer/view', 'id' => $model->customer_id]) ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-person"></i> ดูข้อมูลลูกค้า
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <div class="text-muted mb-1">ชื่อ</div>
                            <div><?= Html::encode($model->customer_name ?: '-') ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted mb-1">เบอร์โทร</div>
                            <div>
                                <?php if ($model->customer_phone): ?>
                                    <a href="tel:<?= $model->customer_phone ?>"><?= Html::encode($model->customer_phone) ?></a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($model->customer_email): ?>
                        <div class="mb-3">
                            <div class="text-muted mb-1">อีเมล</div>
                            <div><a href="mailto:<?= $model->customer_email ?>"><?= Html::encode($model->customer_email) ?></a></div>
                        </div>
                        <?php endif; ?>
                        
                        <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#linkCustomerModal">
                            <i class="bi bi-link-45deg"></i> เชื่อมโยงกับลูกค้า
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Vehicle Info (if related) -->
            <?php if ($model->vehicle): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-car-front"></i> รถที่สอบถาม</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <?php if ($model->vehicle->brand && $model->vehicle->brand->logo): ?>
                                <img src="<?= $model->vehicle->brand->logo ?>" style="height: 40px;">
                            <?php else: ?>
                                <i class="bi bi-car-front fs-1 text-muted"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">
                                <?= Html::encode($model->vehicle->brand->name_th ?? '') ?> 
                                <?= Html::encode($model->vehicle->model->name_th ?? '') ?>
                            </h6>
                            <?php if ($model->vehicle->year): ?>
                                <small class="text-muted">ปี <?= $model->vehicle->year ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Related Part (if any) -->
            <?php if ($model->part): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-box"></i> สินค้าที่สอบถาม</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <?php if ($model->part->image): ?>
                                <img src="<?= $model->part->image ?>" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><?= Html::encode($model->part->name_th) ?></h6>
                            <div class="small text-muted mb-1"><?= Html::encode($model->part->sku) ?></div>
                            <div class="fw-bold text-primary"><?= Yii::$app->formatter->asCurrency($model->part->selling_price) ?></div>
                            <?php if ($model->part->stock_quantity > 0): ?>
                                <span class="badge bg-success">มีสินค้า <?= $model->part->stock_quantity ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">สินค้าหมด</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="<?= Url::to(['part/view', 'id' => $model->part_id]) ?>" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-box"></i> ดูสินค้า
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Inquiry Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> ข้อมูลการสอบถาม</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="100">เลขที่</td>
                            <td><code><?= Html::encode($model->inquiry_number) ?></code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ช่องทาง</td>
                            <td>
                                <span class="badge bg-<?= $channel[2] ?>">
                                    <i class="bi <?= $channel[1] ?>"></i> <?= $channel[0] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">สถานะ</td>
                            <td><?= $model->getStatusBadge() ?></td>
                        </tr>
                        <?php if ($model->assignedUser): ?>
                        <tr>
                            <td class="text-muted">ผู้รับผิดชอบ</td>
                            <td><?= Html::encode($model->assignedUser->username) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">สร้างเมื่อ</td>
                            <td><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">อัพเดท</td>
                            <td><?= Yii::$app->formatter->asRelativeTime($model->updated_at) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ข้อความ</td>
                            <td><?= number_format($model->message_count) ?> ข้อความ</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Assign User -->
            <?php if ($model->status != 'closed'): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person-check"></i> มอบหมายงาน</h6>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin([
                        'action' => ['assign', 'id' => $model->id],
                        'options' => ['class' => 'd-flex gap-2'],
                    ]); ?>
                    
                    <?= Html::dropDownList('assigned_to', $model->assigned_to, $staffList ?? [], [
                        'class' => 'form-select form-select-sm',
                        'prompt' => '-- เลือกพนักงาน --',
                    ]) ?>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check"></i>
                    </button>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Create Order -->
            <?php if ($model->customer && !$model->order_id): ?>
            <div class="d-grid">
                <a href="<?= Url::to(['order/create', 'customer_id' => $model->customer_id, 'inquiry_id' => $model->id]) ?>" 
                   class="btn btn-success">
                    <i class="bi bi-cart-plus"></i> สร้างออเดอร์
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['action' => ['update-status', 'id' => $model->id]]); ?>
            <div class="modal-header">
                <h5 class="modal-title">เปลี่ยนสถานะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="status" id="statusInput">
                <p>ต้องการเปลี่ยนสถานะเป็น "<strong id="statusLabel"></strong>" หรือไม่?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary">ยืนยัน</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- Close Inquiry Modal -->
<div class="modal fade" id="closeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['action' => ['close', 'id' => $model->id]]); ?>
            <div class="modal-header">
                <h5 class="modal-title">ปิดการสอบถาม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    หลังจากปิดแล้วจะไม่สามารถตอบกลับข้อความได้อีก
                </div>
                <div class="mb-3">
                    <label class="form-label">หมายเหตุ (ถ้ามี)</label>
                    <textarea name="close_note" class="form-control" rows="3" 
                              placeholder="เช่น ลูกค้าได้รับสินค้าแล้ว, แก้ไขปัญหาเรียบร้อย..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-danger">ปิดการสอบถาม</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<!-- Link Customer Modal -->
<div class="modal fade" id="linkCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['action' => ['link-customer', 'id' => $model->id]]); ?>
            <div class="modal-header">
                <h5 class="modal-title">เชื่อมโยงกับลูกค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ค้นหาลูกค้า</label>
                    <input type="text" id="customerSearch" class="form-control" 
                           placeholder="รหัส, ชื่อ หรือเบอร์โทร...">
                </div>
                <div id="customerSearchResults" style="max-height: 250px; overflow-y: auto;">
                    <!-- Results loaded via AJAX -->
                </div>
                <input type="hidden" name="customer_id" id="selectedCustomerId">
                
                <hr>
                <div class="text-center">
                    <a href="<?= Url::to(['customer/create', 
                        'name' => $model->customer_name,
                        'phone' => $model->customer_phone,
                        'email' => $model->customer_email,
                        'return' => Url::to(['inquiry/view', 'id' => $model->id]),
                    ]) ?>" class="btn btn-outline-success">
                        <i class="bi bi-person-plus"></i> สร้างลูกค้าใหม่
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" id="linkCustomerBtn" disabled>เชื่อมโยง</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<style>
.chat-messages {
    background: #f8f9fa;
}

.message-bubble {
    padding: 10px 14px;
    border-radius: 16px;
    position: relative;
}

.message-customer {
    background: white;
    border: 1px solid #e9ecef;
    border-bottom-left-radius: 4px;
}

.message-staff {
    background: #0d6efd;
    color: white;
    border-bottom-right-radius: 4px;
}

.message-time {
    font-size: 11px;
    margin-top: 4px;
    opacity: 0.7;
}

.message-staff .message-time {
    color: rgba(255,255,255,0.8);
}

.quick-reply {
    font-size: 12px;
    padding: 2px 8px;
}

.text-purple { color: #8b5cf6; }
.bg-purple { background-color: #8b5cf6; }

/* Customer search result item */
.customer-result {
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.15s;
}

.customer-result:hover {
    background: #f8f9fa;
    border-color: #0d6efd;
}

.customer-result.selected {
    background: #e7f1ff;
    border-color: #0d6efd;
}
</style>

<?php
$js = <<<JS
// Scroll chat to bottom
function scrollChatToBottom() {
    var chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}
scrollChatToBottom();

// Quick replies
$('.quick-reply').on('click', function() {
    var text = $(this).data('text');
    $('#messageInput').val(text).focus();
});

// Status modal
$('#statusModal').on('show.bs.modal', function(e) {
    var status = $(e.relatedTarget).data('status');
    var labels = {
        'in_progress': 'กำลังดำเนินการ',
        'waiting': 'รอลูกค้าตอบ',
        'resolved': 'แก้ไขแล้ว'
    };
    $('#statusInput').val(status);
    $('#statusLabel').text(labels[status] || status);
});

// Customer search
var searchTimeout;
$('#customerSearch').on('input', function() {
    var query = $(this).val();
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        $('#customerSearchResults').html('');
        return;
    }
    
    searchTimeout = setTimeout(function() {
        $.get('/backend/web/customer/search-ajax', {q: query}, function(data) {
            var html = '';
            data.forEach(function(customer) {
                html += '<div class="customer-result" data-id="' + customer.id + '">';
                html += '<div class="fw-bold">' + customer.name + '</div>';
                html += '<div class="small text-muted">' + customer.customer_code + ' | ' + customer.phone + '</div>';
                html += '</div>';
            });
            
            if (data.length === 0) {
                html = '<div class="text-center text-muted py-3">ไม่พบลูกค้า</div>';
            }
            
            $('#customerSearchResults').html(html);
        });
    }, 300);
});

// Select customer
$(document).on('click', '.customer-result', function() {
    $('.customer-result').removeClass('selected');
    $(this).addClass('selected');
    $('#selectedCustomerId').val($(this).data('id'));
    $('#linkCustomerBtn').prop('disabled', false);
});

// Submit message with Ctrl+Enter
$('#messageInput').on('keydown', function(e) {
    if (e.ctrlKey && e.keyCode === 13) {
        $('#replyForm').submit();
    }
});
JS;
$this->registerJs($js);
?>
