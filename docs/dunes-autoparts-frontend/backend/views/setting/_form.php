<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Setting;

?>

<div class="setting-form">
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'needs-validation'],
    ]); ?>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'setting_group')->dropDownList(
                        Setting::getGroupList(),
                        ['class' => 'form-select', 'prompt' => '-- เลือกกลุ่ม --']
                    ) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'setting_type')->dropDownList(
                        Setting::getTypeList(),
                        ['class' => 'form-select']
                    ) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'setting_key')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control font-monospace',
                        'placeholder' => 'ตัวอย่าง: shop_name, min_order_amount',
                    ])->hint('ใช้ตัวอักษรภาษาอังกฤษ, ตัวเลข และ underscore เท่านั้น') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'setting_label')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'ชื่อที่จะแสดงให้ผู้ใช้เห็น',
                    ]) ?>
                </div>
            </div>

            <?= $form->field($model, 'setting_value')->textarea([
                'rows' => 3,
                'class' => 'form-control',
                'placeholder' => 'ค่าของการตั้งค่า',
            ])->hint('สำหรับ JSON ให้ใส่ในรูปแบบ {"key": "value"}') ?>

            <?= $form->field($model, 'setting_description')->textarea([
                'rows' => 2,
                'class' => 'form-control',
                'placeholder' => 'คำอธิบายเพิ่มเติม (ถ้ามี)',
            ]) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'sort_order')->textInput([
                        'type' => 'number',
                        'class' => 'form-control',
                        'min' => 0,
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'is_system')->checkbox([
                        'class' => 'form-check-input',
                    ])->hint('การตั้งค่าระบบจะไม่สามารถลบได้') ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <?= Html::submitButton(
                '<i class="bi bi-save me-1"></i>' . ($model->isNewRecord ? 'สร้างการตั้งค่า' : 'บันทึกการเปลี่ยนแปลง'),
                ['class' => 'btn btn-primary btn-lg']
            ) ?>
            <?= Html::a('ยกเลิก', ['index'], ['class' => 'btn btn-outline-secondary btn-lg ms-2']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
