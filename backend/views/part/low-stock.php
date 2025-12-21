<?php
/**
 * Low Stock Parts - สินค้าใกล้หมด
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'สินค้าใกล้หมด';
?>

<div class="part-low-stock">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">รายการสินค้าที่มีจำนวนน้อยกว่าหรือเท่ากับ Min Stock</p>
        </div>
        <div>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>

    <!-- Alert -->
    <?php if ($dataProvider->getTotalCount() > 0): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            พบสินค้า <strong><?= $dataProvider->getTotalCount() ?></strong> รายการที่ต้องเติมสต็อก
        </div>
    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">รูป</th>
                            <th>SKU</th>
                            <th>ชื่อสินค้า</th>
                            <th>หมวดหมู่</th>
                            <th class="text-center">สต็อกคงเหลือ</th>
                            <th class="text-center">Min Stock</th>
                            <th class="text-center">ต้องเติม</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dataProvider->getCount() == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2 mb-0">สต็อกปกติทุกรายการ</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->getModels() as $part): ?>
                                <tr class="<?= $part->stock_quantity == 0 ? 'table-danger' : '' ?>">
                                    <td>
                                        <?php if ($part->main_image): ?>
                                            <img src="<?= Yii::getAlias('@web/uploads/parts/' . $part->main_image) ?>" 
                                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= Url::to(['view', 'id' => $part->id]) ?>" class="fw-medium">
                                            <?= Html::encode($part->sku) ?>
                                        </a>
                                    </td>
                                    <td><?= Html::encode($part->name_th ?: $part->name_en) ?></td>
                                    <td><?= $part->category ? Html::encode($part->category->name_th) : '-' ?></td>
                                    <td class="text-center">
                                        <?php if ($part->stock_quantity == 0): ?>
                                            <span class="badge bg-danger fs-6">หมด</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark fs-6"><?= $part->stock_quantity ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $part->min_stock_level ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info fs-6">
                                            <?= max(0, $part->min_stock_level - $part->stock_quantity + 5) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#stockModal"
                                                data-id="<?= $part->id ?>"
                                                data-sku="<?= Html::encode($part->sku) ?>"
                                                data-name="<?= Html::encode($part->name_th ?: $part->name_en) ?>">
                                            <i class="bi bi-plus-lg"></i> เติมสต็อก
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php if ($dataProvider->getTotalCount() > $dataProvider->pagination->pageSize): ?>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= $dataProvider->getTotalCount() ?> รายการ
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

<!-- Stock Update Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="stock-form" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="type" value="in">
                
                <div class="modal-header">
                    <h5 class="modal-title">เติมสต็อก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        <strong id="modal-sku"></strong><br>
                        <span id="modal-name" class="text-muted"></span>
                    </p>
                    <div class="mb-3">
                        <label class="form-label">จำนวนที่รับเข้า</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ต้นทุนต่อชิ้น (ถ้ามี)</label>
                        <input type="number" name="cost_per_unit" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เลขอ้างอิง/Invoice</label>
                        <input type="text" name="reference_no" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <input type="text" name="reason" class="form-control" value="เติมสต็อก">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$baseUrl = Url::to(['update-stock', 'id' => '']);
$js = <<<JS
$('#stockModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var sku = button.data('sku');
    var name = button.data('name');
    
    var modal = $(this);
    modal.find('#modal-sku').text(sku);
    modal.find('#modal-name').text(name);
    modal.find('#stock-form').attr('action', '{$baseUrl}' + id);
    modal.find('input[name="quantity"]').val('').focus();
});
JS;
$this->registerJs($js);
?>
