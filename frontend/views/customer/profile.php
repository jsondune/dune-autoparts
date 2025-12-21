<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'โปรไฟล์';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">โปรไฟล์</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" 
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($model->full_name, 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= Html::encode($model->full_name) ?></h5>
                    <p class="text-muted small mb-0"><?= Html::encode($model->email_address) ?></p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= Url::to(['/customer/profile']) ?>" class="list-group-item list-group-item-action active">
                        <i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว
                    </a>
                    <a href="<?= Url::to(['/order/history']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-bag me-2"></i>ประวัติสั่งซื้อ
                    </a>
                    <a href="<?= Url::to(['/customer/change-password']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>ข้อมูลส่วนตัว</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'full_name')->textInput([
                                'class' => 'form-control',
                            ])->label('ชื่อ-นามสกุล') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'email_address')->textInput([
                                'type' => 'email',
                                'class' => 'form-control',
                                'readonly' => true,
                            ])->label('อีเมล') ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'phone_number')->textInput([
                                'class' => 'form-control',
                            ])->label('เบอร์โทรศัพท์') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'line_id')->textInput([
                                'class' => 'form-control',
                            ])->label('Line ID') ?>
                        </div>
                    </div>
                    
                    <?= $form->field($model, 'address')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                    ])->label('ที่อยู่จัดส่ง') ?>
                    
                    <div class="d-flex gap-2">
                        <?= Html::submitButton('<i class="bi bi-check-circle me-2"></i>บันทึกข้อมูล', [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <a href="<?= Url::to(['/customer/change-password']) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน
                        </a>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            
            <!-- Account Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>ข้อมูลบัญชี</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <span class="text-muted">วันที่สมัคร:</span> 
                                <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <span class="text-muted">ประเภทลูกค้า:</span> 
                                <span class="badge bg-<?= $model->type == 'wholesale' ? 'primary' : 'secondary' ?>">
                                    <?= $model->type == 'wholesale' ? 'ขายส่ง' : 'ลูกค้าทั่วไป' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
