<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'ติดต่อเรา';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">ติดต่อเรา</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-5 mb-4">
            <h1 class="mb-4"><i class="bi bi-telephone text-primary me-2"></i>ติดต่อเรา</h1>
            <p class="lead">มีคำถามหรือต้องการสอบถามสินค้า? ติดต่อเราได้เลย</p>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt fs-4 text-primary me-3"></i>
                        <div>
                            <strong>ที่อยู่</strong><br>
                            <span class="text-muted"><?= Yii::$app->params['shopAddress'] ?></span>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="bi bi-telephone fs-4 text-primary me-3"></i>
                        <div>
                            <strong>โทรศัพท์</strong><br>
                            <a href="tel:<?= Yii::$app->params['shopPhone'] ?>" class="text-decoration-none">
                                <?= Yii::$app->params['shopPhone'] ?>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <i class="bi bi-envelope fs-4 text-primary me-3"></i>
                        <div>
                            <strong>อีเมล</strong><br>
                            <a href="mailto:<?= Yii::$app->params['shopEmail'] ?>" class="text-decoration-none">
                                <?= Yii::$app->params['shopEmail'] ?>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex">
                        <i class="bi bi-line fs-4 text-success me-3"></i>
                        <div>
                            <strong>Line</strong><br>
                            <span class="text-muted"><?= Yii::$app->params['shopLine'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-primary btn-lg flex-fill">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="btn btn-success btn-lg flex-fill">
                    <i class="bi bi-line"></i>
                </a>
                <a href="#" class="btn btn-danger btn-lg flex-fill">
                    <i class="bi bi-instagram"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card border-0 shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>ส่งข้อความถึงเรา</h5>
                </div>
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin(); ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'customer_name')->textInput([
                                'placeholder' => 'ชื่อ-นามสกุล',
                                'class' => 'form-control',
                            ])->label('ชื่อ-นามสกุล <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($model, 'customer_phone')->textInput([
                                'placeholder' => 'เบอร์โทรศัพท์',
                                'class' => 'form-control',
                            ])->label('เบอร์โทรศัพท์ <span class="text-danger">*</span>') ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <?= $form->field($model, 'customer_email')->textInput([
                            'type' => 'email',
                            'placeholder' => 'อีเมล (ถ้ามี)',
                            'class' => 'form-control',
                        ])->label('อีเมล') ?>
                    </div>
                    
                    <div class="mb-3">
                        <?= $form->field($model, 'subject')->textInput([
                            'placeholder' => 'หัวข้อ',
                            'class' => 'form-control',
                        ])->label('หัวข้อ <span class="text-danger">*</span>') ?>
                    </div>
                    
                    <div class="mb-3">
                        <?= $form->field($model, 'message')->textarea([
                            'rows' => 5,
                            'placeholder' => 'รายละเอียด...',
                            'class' => 'form-control',
                        ])->label('ข้อความ <span class="text-danger">*</span>') ?>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>ส่งข้อความ
                        </button>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
