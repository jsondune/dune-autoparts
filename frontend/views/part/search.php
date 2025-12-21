<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'ค้นหา: ' . $searchQuery;
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">ค้นหา: <?= Html::encode($searchQuery) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-search me-2"></i>ผลการค้นหา "<?= Html::encode($searchQuery) ?>"
            </h4>
            <small class="text-muted">พบ <?= $pages->totalCount ?> รายการ</small>
        </div>
    </div>
    
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
            <i class="bi bi-search fs-1 text-muted"></i>
            <h5 class="mt-3">ไม่พบสินค้าที่ค้นหา</h5>
            <p class="text-muted">ลองค้นหาด้วยคำอื่น หรือ</p>
            <a href="<?= Url::to(['/part/index']) ?>" class="btn btn-primary">
                <i class="bi bi-box me-2"></i>ดูสินค้าทั้งหมด
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?= LinkPager::widget([
        'pagination' => $pages,
        'options' => ['class' => 'pagination justify-content-center'],
        'linkOptions' => ['class' => 'page-link'],
    ]) ?>
</div>
