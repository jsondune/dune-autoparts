<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'อะไหล่ ' . $brand->name_th;
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/part/index']) ?>">สินค้า</a></li>
                <li class="breadcrumb-item active">อะไหล่ <?= Html::encode($brand->name_th) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-car-front me-2 text-primary"></i>อะไหล่ <?= Html::encode($brand->name_th) ?>
            </h4>
            <small class="text-muted">พบ <?= $pages->totalCount ?> รายการ</small>
        </div>
    </div>
    
    <!-- Models -->
    <?php if (!empty($models)): ?>
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-secondary">รุ่นรถ:</span>
            <?php foreach ($models as $model): ?>
            <span class="badge bg-light text-dark border"><?= Html::encode($model->name_th) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <?php foreach ($parts as $part): ?>
        <div class="col-6 col-md-3 mb-4">
            <div class="card product-card h-100">
                <div class="position-relative">
                    <?php if ($part->image): ?>
                    <img src="<?= $part->image ?>" class="card-img-top" alt="<?= Html::encode($part->name_th) ?>">
                    <?php else: ?>
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-box-seam fs-1 text-muted"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title"><?= Html::encode($part->name_th) ?></h6>
                    <p class="text-muted small mb-2"><?= Html::encode($part->part_number) ?></p>
                    <p class="price mt-auto mb-2">฿<?= number_format($part->sell_price) ?></p>
                    <a href="<?= Url::to(['/part/view', 'id' => $part->id]) ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($parts)): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-muted mt-3">ไม่พบอะไหล่สำหรับยี่ห้อนี้</p>
        </div>
        <?php endif; ?>
    </div>
    
    <?= LinkPager::widget([
        'pagination' => $pages,
        'options' => ['class' => 'pagination justify-content-center'],
        'linkOptions' => ['class' => 'page-link'],
    ]) ?>
</div>
