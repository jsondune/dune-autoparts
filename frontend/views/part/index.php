<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'สินค้าทั้งหมด';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">สินค้าทั้งหมด</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>กรองสินค้า</h6>
                </div>
                <div class="card-body">
                    <!-- Categories -->
                    <h6 class="mb-3">หมวดหมู่</h6>
                    <div class="list-group list-group-flush mb-4">
                        <a href="<?= Url::to(['/part/index']) ?>" 
                           class="list-group-item list-group-item-action <?= !$currentCategory ? 'active' : '' ?>">
                            ทั้งหมด
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?= Url::to(['/part/index', 'category' => $cat->id]) ?>" 
                           class="list-group-item list-group-item-action <?= $currentCategory == $cat->id ? 'active' : '' ?>">
                            <?= Html::encode($cat->name_th) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Price Range -->
                    <h6 class="mb-3">ราคา</h6>
                    <form method="get">
                        <?php if ($currentCategory): ?>
                        <input type="hidden" name="category" value="<?= $currentCategory ?>">
                        <?php endif; ?>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input type="number" name="price_min" class="form-control form-control-sm" placeholder="ต่ำสุด">
                            </div>
                            <div class="col-6">
                                <input type="number" name="price_max" class="form-control form-control-sm" placeholder="สูงสุด">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-search me-1"></i>กรอง
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">สินค้าทั้งหมด</h4>
                    <small class="text-muted">พบ <?= $pages->totalCount ?> รายการ</small>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" onchange="location.href=this.value" style="width: auto;">
                        <option value="<?= Url::current(['sort' => 'newest']) ?>" <?= $currentSort == 'newest' ? 'selected' : '' ?>>ล่าสุด</option>
                        <option value="<?= Url::current(['sort' => 'price_low']) ?>" <?= $currentSort == 'price_low' ? 'selected' : '' ?>>ราคา: ต่ำ-สูง</option>
                        <option value="<?= Url::current(['sort' => 'price_high']) ?>" <?= $currentSort == 'price_high' ? 'selected' : '' ?>>ราคา: สูง-ต่ำ</option>
                        <option value="<?= Url::current(['sort' => 'popular']) ?>" <?= $currentSort == 'popular' ? 'selected' : '' ?>>ขายดี</option>
                        <option value="<?= Url::current(['sort' => 'name']) ?>" <?= $currentSort == 'name' ? 'selected' : '' ?>>ชื่อ ก-ฮ</option>
                    </select>
                </div>
            </div>
            
            <!-- Product Grid -->
            <div class="row">
                <?php foreach ($parts as $part): ?>
                <div class="col-6 col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <?php if ($part->image): ?>
                            <img src="<?= $part->image ?>" class="card-img-top" alt="<?= Html::encode($part->name_th) ?>">
                            <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-box-seam fs-1 text-muted"></i>
                            </div>
                            <?php endif; ?>
                            <?php if ($part->stock_qty <= 0): ?>
                            <span class="badge-sale">หมด</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= Html::encode($part->name_th) ?></h6>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-upc me-1"></i><?= Html::encode($part->part_number) ?>
                            </p>
                            <p class="price mt-auto mb-2">฿<?= number_format($part->sell_price) ?></p>
                            <div class="d-grid gap-2">
                                <a href="<?= Url::to(['/part/view', 'id' => $part->id]) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                                </a>
                                <?php if ($part->stock_qty > 0): ?>
                                <?= Html::beginForm(['/cart/add'], 'post', ['class' => 'd-grid']) ?>
                                <input type="hidden" name="part_id" value="<?= $part->id ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-cart-plus me-1"></i>หยิบใส่ตะกร้า
                                </button>
                                <?= Html::endForm() ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($parts)): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">ไม่พบสินค้า</p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?= LinkPager::widget([
                'pagination' => $pages,
                'options' => ['class' => 'pagination justify-content-center'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['class' => 'page-link'],
            ]) ?>
        </div>
    </div>
</div>
