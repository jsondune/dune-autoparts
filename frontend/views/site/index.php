<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Part[] $featuredParts */
/** @var common\models\Part[] $bestSellers */
/** @var common\models\PartCategory[] $categories */
/** @var common\models\VehicleBrand[] $brands */

$this->title = 'หน้าแรก';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">อะไหล่รถยนต์คุณภาพ<br><span class="text-warning">ราคาโรงงาน</span></h1>
                <p class="lead mb-4">จำหน่ายอะไหล่แท้ อะไหล่เทียบ คุณภาพดี ราคาถูก พร้อมจัดส่งทั่วประเทศ</p>
                <div class="d-flex gap-3">
                    <a href="<?= Url::to(['/part/index']) ?>" class="btn btn-warning btn-lg px-4">
                        <i class="bi bi-box me-2"></i>ดูสินค้าทั้งหมด
                    </a>
                    <a href="<?= Url::to(['/site/contact']) ?>" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-telephone me-2"></i>ติดต่อเรา
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=600" 
                     alt="Auto Parts" class="img-fluid rounded shadow" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-truck fs-2 text-primary me-3"></i>
                    <div class="text-start">
                        <strong>จัดส่งฟรี</strong><br>
                        <small class="text-muted">เมื่อซื้อครบ <?= number_format(Yii::$app->params['freeShippingMinimum']) ?> บาท</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-shield-check fs-2 text-primary me-3"></i>
                    <div class="text-start">
                        <strong>สินค้าคุณภาพ</strong><br>
                        <small class="text-muted">รับประกันทุกชิ้น</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-credit-card fs-2 text-primary me-3"></i>
                    <div class="text-start">
                        <strong>ชำระเงินปลอดภัย</strong><br>
                        <small class="text-muted">หลายช่องทาง</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-headset fs-2 text-primary me-3"></i>
                    <div class="text-start">
                        <strong>บริการดี</strong><br>
                        <small class="text-muted">ตอบคำถามรวดเร็ว</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">
            <i class="bi bi-grid me-2 text-primary"></i>หมวดหมู่สินค้า
        </h2>
        <div class="row">
            <?php 
            $categoryIcons = [
                'bi-wrench', 'bi-gear', 'bi-lightning', 'bi-droplet',
                'bi-speedometer2', 'bi-fan', 'bi-lamp', 'bi-battery-charging'
            ];
            foreach ($categories as $i => $category): 
                $icon = $categoryIcons[$i % count($categoryIcons)];
            ?>
            <div class="col-6 col-md-3 mb-4">
                <a href="<?= Url::to(['/part/category', 'id' => $category->id]) ?>" class="category-card">
                    <i class="bi <?= $icon ?>"></i>
                    <h6 class="mb-0"><?= Html::encode($category->name_th) ?></h6>
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($categories)): ?>
            <div class="col-12 text-center text-muted">
                <p>ยังไม่มีหมวดหมู่สินค้า</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-star me-2 text-warning"></i>สินค้าแนะนำ
            </h2>
            <a href="<?= Url::to(['/part/index']) ?>" class="btn btn-outline-primary">
                ดูทั้งหมด <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row">
            <?php foreach ($featuredParts as $part): ?>
            <div class="col-6 col-md-3 mb-4">
                <div class="card product-card">
                    <div class="position-relative">
                        <?php if ($part->image): ?>
                        <img src="<?= $part->image ?>" class="card-img-top" alt="<?= Html::encode($part->name_th) ?>">
                        <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-box-seam fs-1 text-white"></i>
                        </div>
                        <?php endif; ?>
                        <span class="badge-new">NEW</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title text-truncate"><?= Html::encode($part->name_th) ?></h6>
                        <p class="text-muted small mb-2"><?= Html::encode($part->part_number) ?></p>
                        <p class="price mb-2">฿<?= number_format($part->sell_price) ?></p>
                        <a href="<?= Url::to(['/part/view', 'id' => $part->id]) ?>" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($featuredParts)): ?>
            <div class="col-12 text-center text-muted py-5">
                <i class="bi bi-box fs-1"></i>
                <p>ยังไม่มีสินค้า</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Brands -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">
            <i class="bi bi-car-front me-2 text-primary"></i>ยี่ห้อรถที่รองรับ
        </h2>
        <div class="row justify-content-center">
            <?php foreach ($brands as $brand): ?>
            <div class="col-4 col-md-2 mb-3">
                <a href="<?= Url::to(['/part/brand', 'id' => $brand->id]) ?>" 
                   class="d-block text-center text-decoration-none text-dark p-3 border rounded hover-shadow">
                    <i class="bi bi-car-front fs-3 d-block mb-2 text-primary"></i>
                    <small><?= Html::encode($brand->name_th) ?></small>
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($brands)): ?>
            <div class="col-12 text-center text-muted">
                <p>ยังไม่มียี่ห้อรถ</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h2 class="mb-3">ต้องการอะไหล่แต่หาไม่เจอ?</h2>
        <p class="lead mb-4">ติดต่อเราได้เลย เราพร้อมช่วยหาอะไหล่ให้คุณ</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= Url::to(['/site/contact']) ?>" class="btn btn-warning btn-lg">
                <i class="bi bi-chat-dots me-2"></i>สอบถามสินค้า
            </a>
            <a href="tel:<?= Yii::$app->params['shopPhone'] ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-telephone me-2"></i><?= Yii::$app->params['shopPhone'] ?>
            </a>
        </div>
    </div>
</section>

<style>
.hover-shadow:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    transition: all 0.3s;
}
</style>
