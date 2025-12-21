<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = $part->name_th;
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/part/index']) ?>">สินค้า</a></li>
                <?php if ($part->category): ?>
                <li class="breadcrumb-item"><a href="<?= Url::to(['/part/category', 'id' => $part->category_id]) ?>"><?= Html::encode($part->category->name) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= Html::encode($part->name_th) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <!-- Product Image -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?php if ($part->image): ?>
                    <img src="<?= $part->image ?>" alt="<?= Html::encode($part->name_th) ?>" class="img-fluid w-100 rounded">
                    <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3"><?= Html::encode($part->name_th) ?></h1>
                    
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">
                            <i class="bi bi-upc me-1"></i><?= Html::encode($part->part_number) ?>
                        </span>
                        <?php if ($part->oem_number): ?>
                        <span class="badge bg-outline-secondary border">
                            OEM: <?= Html::encode($part->oem_number) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <span class="display-6 fw-bold text-primary">฿<?= number_format($part->sell_price) ?></span>
                    </div>
                    
                    <!-- Stock Status -->
                    <div class="mb-4">
                        <?php if ($part->stock_qty > 10): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>มีสินค้า (<?= $part->stock_qty ?> ชิ้น)</span>
                        <?php elseif ($part->stock_qty > 0): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>เหลือ <?= $part->stock_qty ?> ชิ้น</span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>สินค้าหมด</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Add to Cart -->
                    <?php if ($part->stock_qty > 0): ?>
                    <?= Html::beginForm(['/cart/add'], 'post') ?>
                    <input type="hidden" name="part_id" value="<?= $part->id ?>">
                    <div class="row g-3 mb-4">
                        <div class="col-auto">
                            <label class="form-label">จำนวน</label>
                            <div class="input-group" style="width: 140px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty(-1)">-</button>
                                <input type="number" name="qty" id="qty" value="1" min="1" max="<?= $part->stock_qty ?>" class="form-control text-center">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty(1)">+</button>
                            </div>
                        </div>
                        <div class="col-auto align-self-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus me-2"></i>หยิบใส่ตะกร้า
                            </button>
                        </div>
                    </div>
                    <?= Html::endForm() ?>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>สินค้าหมดชั่วคราว กรุณาติดต่อสอบถาม
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Info -->
                    <div class="border-top pt-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="bi bi-truck fs-4 text-primary"></i>
                                <p class="small mb-0">จัดส่งทั่วประเทศ</p>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-shield-check fs-4 text-success"></i>
                                <p class="small mb-0">รับประกันคุณภาพ</p>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-arrow-repeat fs-4 text-warning"></i>
                                <p class="small mb-0">เปลี่ยนคืนได้</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <?php if ($part->description): ?>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>รายละเอียดสินค้า</h5>
                </div>
                <div class="card-body">
                    <?= nl2br(Html::encode($part->description)) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Inquiry Form -->
    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-dots me-2"></i>สอบถามสินค้านี้</h5>
                </div>
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <?= $form->field($inquiryModel, 'customer_name')->textInput(['placeholder' => 'ชื่อ-นามสกุล'])->label('ชื่อ <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <?= $form->field($inquiryModel, 'customer_phone')->textInput(['placeholder' => 'เบอร์โทรศัพท์'])->label('เบอร์โทร <span class="text-danger">*</span>') ?>
                        </div>
                    </div>
                    <?= $form->field($inquiryModel, 'customer_email')->textInput(['placeholder' => 'อีเมล (ถ้ามี)'])->label('อีเมล') ?>
                    <?= $form->field($inquiryModel, 'message')->textarea(['rows' => 4, 'placeholder' => 'ข้อความสอบถาม...'])->label('ข้อความ <span class="text-danger">*</span>') ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>ส่งคำถาม
                    </button>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedParts)): ?>
    <div class="mt-5">
        <h4 class="mb-4"><i class="bi bi-grid me-2 text-primary"></i>สินค้าที่เกี่ยวข้อง</h4>
        <div class="row">
            <?php foreach ($relatedParts as $related): ?>
            <div class="col-6 col-md-3 mb-4">
                <div class="card product-card h-100">
                    <div class="position-relative">
                        <?php if ($related->image): ?>
                        <img src="<?= $related->image ?>" class="card-img-top" alt="<?= Html::encode($related->name_th) ?>">
                        <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                            <i class="bi bi-box-seam fs-2 text-muted"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title text-truncate"><?= Html::encode($related->name_th) ?></h6>
                        <p class="price mb-2">฿<?= number_format($related->sell_price) ?></p>
                        <a href="<?= Url::to(['/part/view', 'id' => $related->id]) ?>" class="btn btn-outline-primary btn-sm w-100">
                            ดูรายละเอียด
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function changeQty(delta) {
    var input = document.getElementById('qty');
    var val = parseInt(input.value) + delta;
    var max = parseInt(input.max);
    if (val >= 1 && val <= max) {
        input.value = val;
    }
}
</script>
