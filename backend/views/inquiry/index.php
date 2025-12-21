<?php
/**
 * Inquiry Index - รายการสอบถาม/แชท
 * @var yii\web\View $this
 * @var backend\models\InquirySearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $stats
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'จัดการสอบถาม';
?>

<div class="inquiry-index">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">ข้อความและการสอบถามจากลูกค้า</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col">
            <a href="<?= Url::to(['index', 'InquirySearch[status]' => 'new']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 <?= $stats['new'] > 0 ? 'border-danger border-2' : '' ?>">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-danger"><?= number_format($stats['new']) ?></h4>
                        <small class="text-muted">ใหม่</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'InquirySearch[status]' => 'in_progress']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-warning"><?= number_format($stats['in_progress']) ?></h4>
                        <small class="text-muted">กำลังดำเนินการ</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'InquirySearch[status]' => 'waiting']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-info"><?= number_format($stats['waiting']) ?></h4>
                        <small class="text-muted">รอลูกค้าตอบ</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="<?= Url::to(['index', 'InquirySearch[status]' => 'resolved']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-success"><?= number_format($stats['resolved']) ?></h4>
                        <small class="text-muted">แก้ไขแล้ว</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <h4 class="mb-0 text-primary"><?= number_format($stats['today']) ?></h4>
                    <small class="text-muted">วันนี้</small>
                </div>
            </div>
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
                        'placeholder' => 'เลขที่, ชื่อ, เบอร์โทร...',
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'channel', [
                    'line' => 'Line',
                    'facebook' => 'Facebook',
                    'phone' => 'โทรศัพท์',
                    'walk_in' => 'หน้าร้าน',
                    'web' => 'เว็บไซต์',
                    'ai_bot' => 'AI Bot',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- ช่องทางทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-2">
                <?= Html::activeDropDownList($searchModel, 'status', [
                    'new' => 'ใหม่',
                    'in_progress' => 'กำลังดำเนินการ',
                    'waiting' => 'รอลูกค้าตอบ',
                    'resolved' => 'แก้ไขแล้ว',
                    'closed' => 'ปิดแล้ว',
                ], [
                    'class' => 'form-select',
                    'prompt' => '-- สถานะทั้งหมด --',
                ]) ?>
            </div>
            
            <div class="col-md-3">
                <div class="input-group">
                    <?= Html::activeInput('date', $searchModel, 'date_from', [
                        'class' => 'form-control',
                    ]) ?>
                    <span class="input-group-text">-</span>
                    <?= Html::activeInput('date', $searchModel, 'date_to', [
                        'class' => 'form-control',
                    ]) ?>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-search"></i> ค้นหา', ['class' => 'btn btn-primary']) ?>
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
            
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Inquiries List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if ($dataProvider->getCount() == 0): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                    ไม่พบข้อความสอบถาม
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($dataProvider->getModels() as $inquiry): ?>
                        <a href="<?= Url::to(['view', 'id' => $inquiry->id]) ?>" 
                           class="list-group-item list-group-item-action <?= $inquiry->status == 'new' ? 'bg-light' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-start">
                                    <!-- Channel Icon -->
                                    <div class="me-3">
                                        <?php
                                        $channelIcons = [
                                            'line' => '<i class="bi bi-line text-success fs-4"></i>',
                                            'facebook' => '<i class="bi bi-facebook text-primary fs-4"></i>',
                                            'phone' => '<i class="bi bi-telephone text-info fs-4"></i>',
                                            'walk_in' => '<i class="bi bi-shop text-warning fs-4"></i>',
                                            'web' => '<i class="bi bi-globe text-secondary fs-4"></i>',
                                            'ai_bot' => '<i class="bi bi-robot text-purple fs-4"></i>',
                                        ];
                                        echo $channelIcons[$inquiry->channel] ?? '<i class="bi bi-chat fs-4"></i>';
                                        ?>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold"><?= Html::encode($inquiry->inquiry_number) ?></span>
                                            <?= $inquiry->getStatusBadge() ?>
                                            <?php if ($inquiry->status == 'new'): ?>
                                                <span class="badge bg-danger rounded-pill">NEW</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mb-1">
                                            <?php if ($inquiry->customer): ?>
                                                <span class="text-primary"><?= Html::encode($inquiry->customer->getDisplayName()) ?></span>
                                            <?php else: ?>
                                                <span><?= Html::encode($inquiry->customer_name ?: 'ไม่ระบุชื่อ') ?></span>
                                            <?php endif; ?>
                                            <?php if ($inquiry->customer_phone): ?>
                                                <small class="text-muted ms-2">
                                                    <i class="bi bi-telephone"></i> <?= Html::encode($inquiry->customer_phone) ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($inquiry->subject): ?>
                                            <p class="mb-1 text-dark"><?= Html::encode($inquiry->subject) ?></p>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        $lastMessage = $inquiry->getLastMessage();
                                        if ($lastMessage): 
                                        ?>
                                            <p class="text-muted small mb-0">
                                                <?php if ($lastMessage->sender_type == 'staff'): ?>
                                                    <i class="bi bi-reply"></i>
                                                <?php endif; ?>
                                                <?= Html::encode(mb_substr($lastMessage->message, 0, 100)) ?>
                                                <?= mb_strlen($lastMessage->message) > 100 ? '...' : '' ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Meta Info -->
                                <div class="text-end text-muted small flex-shrink-0 ms-3">
                                    <div><?= Yii::$app->formatter->asRelativeTime($inquiry->updated_at) ?></div>
                                    <div><?= Yii::$app->formatter->asDatetime($inquiry->created_at, 'php:d/m/Y') ?></div>
                                    <?php if ($inquiry->assignedUser): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-person"></i> <?= Html::encode($inquiry->assignedUser->username) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($inquiry->message_count > 0): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-chat-dots"></i> <?= $inquiry->message_count ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

<style>
.text-purple { color: #8b5cf6; }
</style>
