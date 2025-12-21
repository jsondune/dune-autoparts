<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'เกิดข้อผิดพลาด';
$name = $exception->getName();
$code = $exception->statusCode;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h1 class="display-1 fw-bold text-primary"><?= $code ?></h1>
            <h2 class="mb-4"><?= Html::encode($name) ?></h2>
            <p class="text-muted mb-4">
                <?php if ($code == 404): ?>
                    ไม่พบหน้าที่คุณต้องการ กรุณาตรวจสอบ URL อีกครั้ง
                <?php else: ?>
                    เกิดข้อผิดพลาดบางอย่าง กรุณาลองใหม่อีกครั้ง
                <?php endif; ?>
            </p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="<?= Url::to(['/site/index']) ?>" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>กลับหน้าแรก
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>ย้อนกลับ
                </a>
            </div>
        </div>
    </div>
</div>
