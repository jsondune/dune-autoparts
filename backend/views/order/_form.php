<?php
/**
 * Order Form - ฟอร์มสร้าง/แก้ไขคำสั่งซื้อ
 * @var yii\web\View $this
 * @var common\models\Order $model
 * @var yii\widgets\ActiveForm $form
 * @var array $customers
 * @var common\models\Customer $selectedCustomer (optional)
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$isNewRecord = $model->isNewRecord;
?>

<div class="order-form">
    <?php $form = ActiveForm::begin([
        'id' => 'order-form',
        'options' => ['class' => 'needs-validation'],
    ]); ?>

    <div class="row">
        <!-- Left Column - Order Details -->
        <div class="col-lg-8">
            <!-- Customer Selection -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-person"></i> ลูกค้า</h6>
                </div>
                <div class="card-body">
                    <?php if (isset($selectedCustomer)): ?>
                        <!-- Pre-selected customer -->
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <span class="text-white fs-5"><?= mb_substr($selectedCustomer->full_name, 0, 1) ?></span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0"><?= Html::encode($selectedCustomer->full_name) ?></h6>
                                <small class="text-muted">
                                    <?= Html::encode($selectedCustomer->customer_code) ?> | 
                                    <?= Html::encode($selectedCustomer->phone) ?>
                                </small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="changeCustomerBtn">
                                    <i class="bi bi-arrow-repeat"></i> เปลี่ยน
                                </button>
                            </div>
                        </div>
                        <?= Html::activeHiddenInput($model, 'customer_id', ['value' => $selectedCustomer->id]) ?>
                        <div id="customerSelectSection" style="display: none;" class="mt-3">
                    <?php else: ?>
                        <div id="customerSelectSection">
                    <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">ค้นหาลูกค้า</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" id="customerSearchInput" class="form-control" 
                                           placeholder="รหัส, ชื่อ, เบอร์โทร...">
                                </div>
                                <div id="customerSearchResults" class="mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="<?= Url::to(['customer/create', 'return' => Url::current()]) ?>" 
                                       class="btn btn-outline-success w-100">
                                        <i class="bi bi-person-plus"></i> ลูกค้าใหม่
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!isset($selectedCustomer)): ?>
                            <?= $form->field($model, 'customer_id')->hiddenInput()->label(false) ?>
                        <?php endif; ?>
                        </div>
                    
                    <!-- Selected Customer Preview -->
                    <div id="selectedCustomerPreview" class="mt-3" style="display: none;">
                        <div class="alert alert-success mb-0 d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong id="previewName"></strong>
                                <div class="small" id="previewDetails"></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearCustomer">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-box"></i> รายการสินค้า</h6>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPartModal">
                        <i class="bi bi-plus-lg"></i> เพิ่มสินค้า
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="orderItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="60"></th>
                                    <th>สินค้า</th>
                                    <th width="100" class="text-center">จำนวน</th>
                                    <th width="120" class="text-end">ราคา/หน่วย</th>
                                    <th width="100" class="text-end">ส่วนลด</th>
                                    <th width="120" class="text-end">รวม</th>
                                    <th width="50"></th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsBody">
                                <?php if (!$isNewRecord && $model->items): ?>
                                    <?php foreach ($model->items as $index => $item): ?>
                                    <tr class="order-item-row" data-part-id="<?= $item->part_id ?>">
                                        <td class="align-middle">
                                            <?php if ($item->part && $item->part->image): ?>
                                                <img src="<?= $item->part->image ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <div class="fw-bold"><?= Html::encode($item->part->name_th ?? $item->part_name) ?></div>
                                            <small class="text-muted"><?= Html::encode($item->part->sku ?? '') ?></small>
                                            <input type="hidden" name="OrderItem[<?= $index ?>][part_id]" value="<?= $item->part_id ?>">
                                            <input type="hidden" name="OrderItem[<?= $index ?>][part_name]" value="<?= Html::encode($item->part_name) ?>">
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="number" name="OrderItem[<?= $index ?>][quantity]" 
                                                   value="<?= $item->quantity ?>" 
                                                   class="form-control form-control-sm text-center item-qty"
                                                   min="1" style="width: 70px;">
                                        </td>
                                        <td class="align-middle text-end">
                                            <input type="number" name="OrderItem[<?= $index ?>][unit_price]" 
                                                   value="<?= $item->unit_price ?>" 
                                                   class="form-control form-control-sm text-end item-price"
                                                   min="0" step="0.01">
                                        </td>
                                        <td class="align-middle text-end">
                                            <input type="number" name="OrderItem[<?= $index ?>][discount]" 
                                                   value="<?= $item->discount ?>" 
                                                   class="form-control form-control-sm text-end item-discount"
                                                   min="0" step="0.01">
                                        </td>
                                        <td class="align-middle text-end fw-bold item-subtotal">
                                            <?= Yii::$app->formatter->asCurrency($item->subtotal) ?>
                                        </td>
                                        <td class="align-middle text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr id="emptyItemsRow" <?= (!$isNewRecord && $model->items) ? 'style="display: none;"' : '' ?>>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        ยังไม่มีสินค้า คลิก "เพิ่มสินค้า" เพื่อเริ่มต้น
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-truck"></i> ข้อมูลจัดส่ง</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'shipping_name')->textInput([
                                'placeholder' => 'ชื่อผู้รับ',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'shipping_phone')->textInput([
                                'placeholder' => 'เบอร์โทรผู้รับ',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                        <div class="col-12">
                            <?= $form->field($model, 'shipping_address')->textarea([
                                'rows' => 3,
                                'placeholder' => 'ที่อยู่จัดส่งแบบเต็ม',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'shipping_method')->dropDownList([
                                'pickup' => 'รับหน้าร้าน',
                                'standard' => 'ส่งธรรมดา',
                                'express' => 'ส่งด่วน',
                                'kerry' => 'Kerry Express',
                                'flash' => 'Flash Express',
                                'jt' => 'J&T Express',
                                'thaipost' => 'ไปรษณีย์ไทย',
                            ], [
                                'class' => 'form-select',
                                'prompt' => '-- เลือกวิธีจัดส่ง --',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'shipping_cost')->textInput([
                                'type' => 'number',
                                'min' => 0,
                                'step' => '0.01',
                                'placeholder' => '0.00',
                                'class' => 'form-control',
                                'id' => 'shippingCostInput',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Notes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-chat-text"></i> หมายเหตุ</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'customer_notes')->textarea([
                        'rows' => 3,
                        'placeholder' => 'หมายเหตุสำหรับออเดอร์นี้...',
                        'class' => 'form-control',
                    ])->label(false) ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-calculator"></i> สรุปคำสั่งซื้อ</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>ยอดสินค้า</td>
                            <td class="text-end" id="subtotalDisplay">฿0.00</td>
                        </tr>
                        <tr>
                            <td>ส่วนลดรวม</td>
                            <td class="text-end text-danger" id="discountDisplay">-฿0.00</td>
                        </tr>
                        <tr>
                            <td>ค่าจัดส่ง</td>
                            <td class="text-end" id="shippingDisplay">฿0.00</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold fs-5">ยอดรวมทั้งสิ้น</td>
                            <td class="text-end fw-bold fs-5 text-primary" id="grandTotalDisplay">฿0.00</td>
                        </tr>
                    </table>
                    
                    <?= Html::activeHiddenInput($model, 'subtotal', ['id' => 'subtotalInput']) ?>
                    <?= Html::activeHiddenInput($model, 'discount_amount', ['id' => 'discountInput']) ?>
                    <?= Html::activeHiddenInput($model, 'grand_total', ['id' => 'grandTotalInput']) ?>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                            $isNewRecord 
                                ? '<i class="bi bi-cart-check"></i> สร้างคำสั่งซื้อ' 
                                : '<i class="bi bi-check-lg"></i> บันทึกการแก้ไข',
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                        <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">ยกเลิก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Add Part Modal -->
<div class="modal fade" id="addPartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box"></i> เพิ่มสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="partSearchInput" class="form-control" 
                               placeholder="ค้นหา SKU, ชื่อสินค้า, OEM...">
                    </div>
                </div>
                <div id="partSearchResults" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        พิมพ์เพื่อค้นหาสินค้า
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.customer-result, .part-result {
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.15s;
}

.customer-result:hover, .part-result:hover {
    background: #f8f9fa;
    border-color: #0d6efd;
}

.part-result.out-of-stock {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<?php
$js = <<<JS
var itemIndex = $('#orderItemsBody .order-item-row').length;

// Format currency
function formatCurrency(amount) {
    return '฿' + parseFloat(amount).toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Calculate totals
function calculateTotals() {
    var subtotal = 0;
    var totalDiscount = 0;
    
    $('.order-item-row').each(function() {
        var qty = parseFloat($(this).find('.item-qty').val()) || 0;
        var price = parseFloat($(this).find('.item-price').val()) || 0;
        var discount = parseFloat($(this).find('.item-discount').val()) || 0;
        var lineTotal = (qty * price) - discount;
        
        subtotal += qty * price;
        totalDiscount += discount;
        
        $(this).find('.item-subtotal').text(formatCurrency(lineTotal));
    });
    
    var shipping = parseFloat($('#shippingCostInput').val()) || 0;
    var grandTotal = subtotal - totalDiscount + shipping;
    
    $('#subtotalDisplay').text(formatCurrency(subtotal));
    $('#discountDisplay').text('-' + formatCurrency(totalDiscount));
    $('#shippingDisplay').text(formatCurrency(shipping));
    $('#grandTotalDisplay').text(formatCurrency(grandTotal));
    
    $('#subtotalInput').val(subtotal);
    $('#discountInput').val(totalDiscount);
    $('#grandTotalInput').val(grandTotal);
    
    // Show/hide empty row
    if ($('.order-item-row').length > 0) {
        $('#emptyItemsRow').hide();
    } else {
        $('#emptyItemsRow').show();
    }
}

// Customer search
var customerSearchTimeout;
$('#customerSearchInput').on('input', function() {
    var query = $(this).val();
    clearTimeout(customerSearchTimeout);
    
    if (query.length < 2) {
        $('#customerSearchResults').html('');
        return;
    }
    
    customerSearchTimeout = setTimeout(function() {
        $.get('/backend/web/customer/search-ajax', {q: query}, function(data) {
            var html = '';
            data.forEach(function(customer) {
                html += '<div class="customer-result" data-id="' + customer.id + '" data-name="' + customer.full_name + '" data-phone="' + customer.phone + '" data-address="' + (customer.address || '') + '">';
                html += '<div class="fw-bold">' + customer.full_name + '</div>';
                html += '<div class="small text-muted">' + customer.customer_code + ' | ' + customer.phone + '</div>';
                html += '</div>';
            });
            
            if (data.length === 0) {
                html = '<div class="text-center text-muted py-3">ไม่พบลูกค้า</div>';
            }
            
            $('#customerSearchResults').html(html);
        });
    }, 300);
});

// Select customer
$(document).on('click', '.customer-result', function() {
    var id = $(this).data('id');
    var full_name = $(this).data('full_name');
    var phone = $(this).data('phone');
    var address = $(this).data('address');
    
    $('#order-customer_id').val(id);
    $('#previewName').text(full_name);
    $('#previewDetails').text(phone);
    $('#selectedCustomerPreview').show();
    $('#customerSearchResults').html('');
    $('#customerSearchInput').val('');
    
    // Auto-fill shipping if empty
    if (!$('#order-shipping_name').val()) {
        $('#order-shipping_name').val(full_name);
    }
    if (!$('#order-shipping_phone').val()) {
        $('#order-shipping_phone').val(phone);
    }
    if (!$('#order-shipping_address').val() && address) {
        $('#order-shipping_address').val(address);
    }
});

// Clear customer
$('#clearCustomer, #changeCustomerBtn').on('click', function() {
    $('#order-customer_id').val('');
    $('#selectedCustomerPreview').hide();
    $('#customerSelectSection').show();
    $('#customerSearchInput').focus();
});

// Part search
var partSearchTimeout;
$('#partSearchInput').on('input', function() {
    var query = $(this).val();
    clearTimeout(partSearchTimeout);
    
    if (query.length < 2) {
        $('#partSearchResults').html('<div class="text-center text-muted py-5"><i class="bi bi-search fs-1 d-block mb-2"></i>พิมพ์เพื่อค้นหาสินค้า</div>');
        return;
    }
    
    partSearchTimeout = setTimeout(function() {
        $.get('/backend/web/part/search-ajax', {q: query}, function(data) {
            var html = '';
            data.forEach(function(part) {
                var stockClass = part.stock <= 0 ? 'out-of-stock' : '';
                var stockBadge = part.stock > 0 
                    ? '<span class="badge bg-success">คงเหลือ ' + part.stock + '</span>'
                    : '<span class="badge bg-danger">สินค้าหมด</span>';
                    
                html += '<div class="part-result d-flex align-items-center ' + stockClass + '" ';
                html += 'data-id="' + part.id + '" ';
                html += 'data-sku="' + part.sku + '" ';
                html += 'data-name="' + part.name + '" ';
                html += 'data-price="' + part.price + '" ';
                html += 'data-stock="' + part.stock + '" ';
                html += 'data-image="' + (part.image || '') + '">';
                
                html += '<div class="flex-shrink-0 me-3">';
                if (part.image) {
                    html += '<img src="' + part.image + '" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">';
                } else {
                    html += '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bi bi-image text-muted"></i></div>';
                }
                html += '</div>';
                
                html += '<div class="flex-grow-1">';
                html += '<div class="fw-bold">' + part.name + '</div>';
                html += '<div class="small text-muted">' + part.sku + '</div>';
                html += '</div>';
                
                html += '<div class="text-end">';
                html += '<div class="fw-bold text-primary">' + formatCurrency(part.price) + '</div>';
                html += stockBadge;
                html += '</div>';
                
                html += '</div>';
            });
            
            if (data.length === 0) {
                html = '<div class="text-center text-muted py-3">ไม่พบสินค้า</div>';
            }
            
            $('#partSearchResults').html(html);
        });
    }, 300);
});

// Add part to order
$(document).on('click', '.part-result', function() {
    if ($(this).hasClass('out-of-stock')) {
        alert('สินค้าหมด ไม่สามารถเพิ่มได้');
        return;
    }
    
    var partId = $(this).data('id');
    var sku = $(this).data('sku');
    var name = $(this).data('name');
    var price = $(this).data('price');
    var image = $(this).data('image');
    
    // Check if already in order
    if ($('#orderItemsBody .order-item-row[data-part-id="' + partId + '"]').length > 0) {
        alert('สินค้านี้มีในรายการแล้ว');
        return;
    }
    
    var imageHtml = image 
        ? '<img src="' + image + '" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">'
        : '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="bi bi-image text-muted"></i></div>';
    
    var rowHtml = '<tr class="order-item-row" data-part-id="' + partId + '">';
    rowHtml += '<td class="align-middle">' + imageHtml + '</td>';
    rowHtml += '<td class="align-middle">';
    rowHtml += '<div class="fw-bold">' + name + '</div>';
    rowHtml += '<small class="text-muted">' + sku + '</small>';
    rowHtml += '<input type="hidden" name="OrderItem[' + itemIndex + '][part_id]" value="' + partId + '">';
    rowHtml += '<input type="hidden" name="OrderItem[' + itemIndex + '][part_name]" value="' + name + '">';
    rowHtml += '</td>';
    rowHtml += '<td class="align-middle text-center">';
    rowHtml += '<input type="number" name="OrderItem[' + itemIndex + '][quantity]" value="1" class="form-control form-control-sm text-center item-qty" min="1" style="width: 70px;">';
    rowHtml += '</td>';
    rowHtml += '<td class="align-middle text-end">';
    rowHtml += '<input type="number" name="OrderItem[' + itemIndex + '][unit_price]" value="' + price + '" class="form-control form-control-sm text-end item-price" min="0" step="0.01">';
    rowHtml += '</td>';
    rowHtml += '<td class="align-middle text-end">';
    rowHtml += '<input type="number" name="OrderItem[' + itemIndex + '][discount]" value="0" class="form-control form-control-sm text-end item-discount" min="0" step="0.01">';
    rowHtml += '</td>';
    rowHtml += '<td class="align-middle text-end fw-bold item-subtotal">' + formatCurrency(price) + '</td>';
    rowHtml += '<td class="align-middle text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="bi bi-trash"></i></button></td>';
    rowHtml += '</tr>';
    
    $('#orderItemsBody').append(rowHtml);
    itemIndex++;
    
    calculateTotals();
    
    // Close modal
    $('#addPartModal').modal('hide');
    $('#partSearchInput').val('');
    $('#partSearchResults').html('<div class="text-center text-muted py-5"><i class="bi bi-search fs-1 d-block mb-2"></i>พิมพ์เพื่อค้นหาสินค้า</div>');
});

// Remove item
$(document).on('click', '.remove-item-btn', function() {
    $(this).closest('tr').remove();
    calculateTotals();
});

// Recalculate on input change
$(document).on('input', '.item-qty, .item-price, .item-discount, #shippingCostInput', function() {
    calculateTotals();
});

// Initial calculation
calculateTotals();
JS;
$this->registerJs($js);
?>
