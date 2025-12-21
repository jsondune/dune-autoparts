<?php
/**
 * Part Form - ฟอร์มเพิ่ม/แก้ไขสินค้า
 * @var yii\web\View $this
 * @var common\models\Part $model
 * @var array $categories
 * @var array $brands
 * @var array $compatibleVehicleIds (optional)
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$isNewRecord = $model->isNewRecord;
$compatibleVehicleIds = $compatibleVehicleIds ?? [];
?>

<?php $form = ActiveForm::begin([
    'id' => 'part-form',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="row g-4">
    <!-- Main Form -->
    <div class="col-xl-8">
        <!-- Basic Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลพื้นฐาน</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'part_type')->dropDownList([
                            'new' => 'ของใหม่ (แท้/OEM)',
                            'used_imported' => 'มือสองนำเข้า',
                        ], ['class' => 'form-select', 'prompt' => '-- เลือกประเภท --']) ?>
                    </div>
                    <div class="col-md-6" id="condition-grade-wrapper" style="<?= $model->part_type != 'used_imported' ? 'display:none;' : '' ?>">
                        <?= $form->field($model, 'condition_grade')->dropDownList([
                            'A+' => 'Grade A+ (สภาพดีเยี่ยม)',
                            'A' => 'Grade A (สภาพดี)',
                            'B' => 'Grade B (สภาพปานกลาง)',
                            'C' => 'Grade C (ใช้ได้)',
                        ], ['class' => 'form-select', 'prompt' => '-- เลือกเกรด --']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'sku')->textInput([
                            'maxlength' => true, 
                            'class' => 'form-control',
                            'placeholder' => $isNewRecord ? 'ปล่อยว่างเพื่อสร้างอัตโนมัติ' : '',
                        ])->hint($isNewRecord ? 'ระบบจะสร้างให้อัตโนมัติหากไม่กรอก' : '') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'oem_number')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'เช่น 04465-42200',
                        ]) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'name_th')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'ชื่อสินค้าภาษาไทย',
                        ]) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'name_en')->textInput([
                            'maxlength' => true,
                            'class' => 'form-control',
                            'placeholder' => 'Product name in English',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'category_id')->dropDownList($categories, [
                            'class' => 'form-select',
                            'prompt' => '-- เลือกหมวดหมู่ --',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'supplier_id')->dropDownList(
                            ArrayHelper::map(\common\models\Supplier::find()->where(['is_active' => 1])->all(), 'id', 'name'),
                            ['class' => 'form-select', 'prompt' => '-- เลือก Supplier --']
                        ) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'description')->textarea([
                            'rows' => 4,
                            'class' => 'form-control',
                            'placeholder' => 'รายละเอียดเพิ่มเติม...',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-tag me-2"></i>ราคา</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <?= $form->field($model, 'cost_price')->textInput([
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'class' => 'form-control',
                            'placeholder' => '0.00',
                        ])->label('ราคาทุน (บาท)') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'selling_price')->textInput([
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'class' => 'form-control',
                            'placeholder' => '0.00',
                        ])->label('ราคาขาย (บาท)') ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'discount_price')->textInput([
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'class' => 'form-control',
                            'placeholder' => 'ไม่บังคับ',
                        ])->label('ราคาลด (บาท)') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock (New Record Only) -->
        <?php if ($isNewRecord): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>สต็อกเริ่มต้น</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">จำนวนสต็อกเริ่มต้น</label>
                        <input type="number" name="initial_stock" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ต้นทุนต่อชิ้น</label>
                        <input type="number" name="initial_cost_per_unit" class="form-control" step="0.01" placeholder="ใช้ราคาทุนถ้าไม่ระบุ">
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'min_stock_level')->textInput([
                            'type' => 'number',
                            'min' => '0',
                            'class' => 'form-control',
                        ])->label('สต็อกขั้นต่ำ (แจ้งเตือน)') ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Compatible Vehicles -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>รถที่ใช้ได้</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label">ยี่ห้อรถ</label>
                        <select id="brand-select" class="form-select">
                            <option value="">-- เลือกยี่ห้อ --</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand->id ?>"><?= Html::encode($brand->_th) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">รุ่น</label>
                        <select id="model-select" class="form-select" disabled>
                            <option value="">-- เลือกยี่ห้อก่อน --</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="add-vehicle-btn" class="btn btn-outline-primary w-100" disabled>
                            <i class="bi bi-plus-lg"></i> เพิ่ม
                        </button>
                    </div>
                </div>
                
                <div id="selected-vehicles">
                    <?php foreach ($compatibleVehicleIds as $vehicleId): 
                        $vehicle = \common\models\VehicleModel::findOne($vehicleId);
                        if ($vehicle):
                    ?>
                        <div class="badge bg-light text-dark border me-1 mb-1 p-2">
                            <?= Html::encode($vehicle->brand->name_th . ' ' . $vehicle->name_th . ' ' . $vehicle->generation) ?>
                            <input type="hidden" name="compatible_vehicles[]" value="<?= $vehicleId ?>">
                            <button type="button" class="btn-close btn-close-sm ms-1" onclick="this.parentElement.remove()"></button>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <small class="text-muted">เลือกรถที่สินค้านี้ใช้ได้</small>
            </div>
        </div>

        <!-- Specifications -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>สเปค</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-spec-btn">
                    <i class="bi bi-plus-lg"></i> เพิ่มสเปค
                </button>
            </div>
            <div class="card-body">
                <div id="specifications-container">
                    <?php 
                    $specs = is_array($model->specifications) ? $model->specifications : json_decode($model->specifications, true);
                    if (!empty($specs)):
                        foreach ($specs as $key => $value): 
                    ?>
                        <div class="row g-2 mb-2 spec-row">
                            <div class="col-5">
                                <input type="text" name="specifications[keys][]" class="form-control form-control-sm" placeholder="ชื่อสเปค" value="<?= Html::encode($key) ?>">
                            </div>
                            <div class="col-6">
                                <input type="text" name="specifications[values][]" class="form-control form-control-sm" placeholder="ค่า" value="<?= Html::encode($value) ?>">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.spec-row').remove()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-xl-4">
        <!-- Images -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-images me-2"></i>รูปภาพ</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">รูปหลัก</label>
                    <?php if ($model->main_image): ?>
                        <div class="mb-2">
                            <img src="<?= Yii::getAlias('@web/uploads/parts/' . $model->main_image) ?>" 
                                 class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    <?php endif; ?>
                    <?= $form->field($model, 'main_image')->fileInput([
                        'class' => 'form-control',
                        'accept' => 'image/*',
                    ])->label(false) ?>
                </div>
                <div>
                    <label class="form-label">รูปเพิ่มเติม</label>
                    <?= $form->field($model, 'images[]')->fileInput([
                        'class' => 'form-control',
                        'accept' => 'image/*',
                        'multiple' => true,
                    ])->label(false) ?>
                    <small class="text-muted">เลือกได้หลายรูป</small>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>ตั้งค่า</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <?= $form->field($model, 'warranty_days')->textInput([
                        'type' => 'number',
                        'min' => '0',
                        'class' => 'form-control',
                        'placeholder' => 'ระบุจำนวนวัน',
                    ])->hint('ของใหม่: 180 วัน, มือสอง: 7 วัน (ค่าเริ่มต้น)') ?>
                </div>
                <div class="mb-3">
                    <?= $form->field($model, 'is_active')->checkbox([
                        'class' => 'form-check-input',
                    ]) ?>
                </div>
                <div class="mb-3">
                    <?= $form->field($model, 'is_featured')->checkbox([
                        'class' => 'form-check-input',
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- Tags -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Tags</h5>
            </div>
            <div class="card-body">
                <?php 
                $tags = is_array($model->tags) ? $model->tags : json_decode($model->tags, true);
                $tagsStr = $tags ? implode(', ', $tags) : '';
                ?>
                <input type="text" name="tags" class="form-control" value="<?= Html::encode($tagsStr) ?>" placeholder="แยกด้วยเครื่องหมาย ,">
                <small class="text-muted">เช่น: ของแท้, ญี่ปุ่น, Toyota</small>
            </div>
        </div>

        <!-- Submit -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?= Html::submitButton(
                        $isNewRecord ? '<i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้า' : '<i class="bi bi-check-lg me-1"></i> บันทึก',
                        ['class' => 'btn btn-primary btn-lg']
                    ) ?>
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">ยกเลิก</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$getModelsUrl = Url::to(['get-models']);
$js = <<<JS
// Show/hide condition grade based on part type
$('#part-part_type').change(function() {
    if ($(this).val() == 'used_imported') {
        $('#condition-grade-wrapper').show();
    } else {
        $('#condition-grade-wrapper').hide();
    }
});

// Load vehicle models on brand change
$('#brand-select').change(function() {
    var brandId = $(this).val();
    var modelSelect = $('#model-select');
    
    if (!brandId) {
        modelSelect.html('<option value="">-- เลือกยี่ห้อก่อน --</option>').prop('disabled', true);
        $('#add-vehicle-btn').prop('disabled', true);
        return;
    }
    
    $.getJSON('{$getModelsUrl}', {brand_id: brandId}, function(data) {
        var options = '<option value="">-- เลือกรุ่น --</option>';
        $.each(data, function(i, model) {
            options += '<option value="' + model.id + '">' + model.name + '</option>';
        });
        modelSelect.html(options).prop('disabled', false);
    });
});

// Enable add button when model selected
$('#model-select').change(function() {
    $('#add-vehicle-btn').prop('disabled', !$(this).val());
});

// Add vehicle to list
$('#add-vehicle-btn').click(function() {
    var modelId = $('#model-select').val();
    var modelName = $('#model-select option:selected').text();
    var brandName = $('#brand-select option:selected').text();
    
    if (!modelId) return;
    
    // Check if already added
    if ($('input[name="compatible_vehicles[]"][value="' + modelId + '"]').length) {
        alert('รถรุ่นนี้ถูกเพิ่มไปแล้ว');
        return;
    }
    
    var badge = '<div class="badge bg-light text-dark border me-1 mb-1 p-2">' +
        brandName + ' ' + modelName +
        '<input type="hidden" name="compatible_vehicles[]" value="' + modelId + '">' +
        '<button type="button" class="btn-close btn-close-sm ms-1" onclick="this.parentElement.remove()"></button>' +
        '</div>';
    
    $('#selected-vehicles').append(badge);
    
    // Reset selects
    $('#model-select').val('');
    $('#add-vehicle-btn').prop('disabled', true);
});

// Add specification row
$('#add-spec-btn').click(function() {
    var row = '<div class="row g-2 mb-2 spec-row">' +
        '<div class="col-5"><input type="text" name="specifications[keys][]" class="form-control form-control-sm" placeholder="ชื่อสเปค"></div>' +
        '<div class="col-6"><input type="text" name="specifications[values][]" class="form-control form-control-sm" placeholder="ค่า"></div>' +
        '<div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest(\'.spec-row\').remove()"><i class="bi bi-x"></i></button></div>' +
        '</div>';
    $('#specifications-container').append(row);
});
JS;
$this->registerJs($js);
?>
