<?php
/**
 * Customer Form - ฟอร์มลูกค้า
 * @var yii\web\View $this
 * @var common\models\Customer $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$customerTypes = [
    'retail' => 'ลูกค้าทั่วไป (ขายปลีก)',
    'wholesale' => 'ลูกค้าขายส่ง',
    'garage' => 'อู่ซ่อมรถ / ศูนย์บริการ',
];
?>

<div class="customer-form">
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'needs-validation', 'novalidate' => true],
    ]); ?>

    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-xl-8">
            <!-- Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>ข้อมูลพื้นฐาน</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'customer_type')->dropDownList($customerTypes, [
                                'class' => 'form-select form-select-lg',
                            ])->label('ประเภทลูกค้า') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'customer_code')->textInput([
                                'class' => 'form-control form-control-lg',
                                'readonly' => !$model->isNewRecord,
                                'placeholder' => $model->isNewRecord ? 'จะสร้างอัตโนมัติ' : '',
                            ])->label('รหัสลูกค้า') ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'first_name')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'ชื่อ',
                            ])->label('ชื่อ') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'last_name')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'นามสกุล',
                            ])->label('นามสกุล') ?>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-8">
                            <?= $form->field($model, 'company_name')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'ชื่อบริษัท/ร้าน (ถ้ามี)',
                            ])->label('ชื่อบริษัท/ร้าน') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'tax_id')->textInput([
                                'class' => 'form-control',
                                'maxlength' => 13,
                                'placeholder' => '13 หลัก',
                            ])->label('เลขประจำตัวผู้เสียภาษี') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>ข้อมูลติดต่อ</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone')->textInput([
                                'class' => 'form-control',
                                'maxlength' => 20,
                                'placeholder' => '0xx-xxx-xxxx',
                            ])->label('เบอร์โทรศัพท์') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'email')->textInput([
                                'type' => 'email',
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'email@example.com',
                            ])->label('อีเมล') ?>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <?= $form->field($model, 'line_id')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'Line ID',
                            ])->label('<i class="bi bi-line text-success me-1"></i>Line ID') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'facebook')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'Facebook',
                            ])->label('<i class="bi bi-facebook text-primary me-1"></i>Facebook') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>ที่อยู่</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'address')->textarea([
                        'class' => 'form-control',
                        'rows' => 3,
                        'placeholder' => 'บ้านเลขที่ หมู่ ซอย ถนน',
                    ])->label('ที่อยู่') ?>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <?= $form->field($model, 'district')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'แขวง/ตำบล',
                            ])->label('แขวง/ตำบล') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'province')->textInput([
                                'class' => 'form-control',
                                'maxlength' => true,
                                'placeholder' => 'จังหวัด',
                            ])->label('จังหวัด') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'postal_code')->textInput([
                                'class' => 'form-control',
                                'maxlength' => 5,
                                'placeholder' => 'รหัสไปรษณีย์',
                            ])->label('รหัสไปรษณีย์') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>หมายเหตุ</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'notes')->textarea([
                        'class' => 'form-control',
                        'rows' => 4,
                        'placeholder' => 'หมายเหตุเพิ่มเติม...',
                    ])->label(false) ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4">
            <!-- Credit Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>การเงิน</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'credit_limit')->textInput([
                        'type' => 'number',
                        'class' => 'form-control',
                        'min' => 0,
                        'step' => 1000,
                        'placeholder' => '0',
                    ])->label('วงเงินเครดิต (บาท)') ?>
                    
                    <?php if (!$model->isNewRecord): ?>
                    <div class="mb-3">
                        <label class="form-label">ยอดค้างชำระ</label>
                        <input type="text" class="form-control" 
                               value="<?= Yii::$app->formatter->asCurrency($model->credit_balance, 'THB') ?>" 
                               readonly>
                    </div>
                    <?php endif; ?>
                    
                    <?= $form->field($model, 'discount_percent')->textInput([
                        'type' => 'number',
                        'class' => 'form-control',
                        'min' => 0,
                        'max' => 50,
                        'step' => 0.5,
                        'placeholder' => '0',
                    ])->label('ส่วนลดพิเศษ (%)') ?>
                    <small class="text-muted">ส่วนลดที่ใช้กับทุกคำสั่งซื้อของลูกค้ารายนี้</small>
                </div>
            </div>

            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i>สถานะ</h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'is_active', [
                        'template' => '<div class="form-check form-switch">{input} {label}</div>{error}'
                    ])->checkbox([
                        'class' => 'form-check-input',
                        'label' => 'ใช้งาน',
                        'labelOptions' => ['class' => 'form-check-label'],
                    ]) ?>
                    <small class="text-muted d-block">ปิดการใช้งานเพื่อระงับลูกค้ารายนี้ชั่วคราว</small>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                            $model->isNewRecord 
                                ? '<i class="bi bi-plus-lg me-1"></i> เพิ่มลูกค้า' 
                                : '<i class="bi bi-check-lg me-1"></i> บันทึกการเปลี่ยนแปลง',
                            ['class' => 'btn btn-lg ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]
                        ) ?>
                        
                        <?php if (!$model->isNewRecord): ?>
                            <a href="<?= \yii\helpers\Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> ยกเลิก
                            </a>
                        <?php else: ?>
                            <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> ยกเลิก
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (!$model->isNewRecord): ?>
            <!-- Stats -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>สถิติ</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">คำสั่งซื้อทั้งหมด</td>
                            <td class="text-end fw-bold"><?= number_format($model->total_orders) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ยอดซื้อรวม</td>
                            <td class="text-end fw-bold"><?= Yii::$app->formatter->asCurrency($model->total_spent, 'THB') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">สมัครเมื่อ</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">แก้ไขล่าสุด</td>
                            <td class="text-end"><?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
