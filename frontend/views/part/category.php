<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = $category->name_th;
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/part/index']) ?>">สินค้า</a></li>
                <li class="breadcrumb-item active"><?= Html::encode($category->name_th) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-grid me-2 text-primary"></i><?= Html::encode($category->name_th) ?>
            </h4>
            <small class="text-muted">พบ <?= $pages->totalCount ?> รายการ</small>
        </div>
        <select class="form-select form-select-sm" onchange="location.href=this.value" style="width: auto;">
            <option value="<?= Url::current(['sort' => 'newest']) ?>" <?= $currentSort == 'newest' ? 'selected' : '' ?>>ล่าสุด</option>
            <option value="<?= Url::current(['sort' => 'price_low']) ?>" <?= $currentSort == 'price_low' ? 'selected' : '' ?>>ราคา: ต่ำ-สูง</option>
            <option value="<?= Url::current(['sort' => 'price_high']) ?>" <?= $currentSort == 'price_high' ? 'selected' : '' ?>>ราคา: สูง-ต่ำ</option>
            <option value="<?= Url::current(['sort' => 'popular']) ?>" <?= $currentSort == 'popular' ? 'selected' : '' ?>>ขายดี</option>
        </select>
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
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="text-muted mt-3">ไม่พบสินค้าในหมวดหมู่นี้</p>
        </div>
        <?php endif; ?>
    </div>
    
    <?= LinkPager::widget([
        'pagination' => $pages,
        'options' => ['class' => 'pagination justify-content-center'],
        'linkOptions' => ['class' => 'page-link'],
    ]) ?>
</div>
