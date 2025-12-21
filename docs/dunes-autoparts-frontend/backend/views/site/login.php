<?php
/**
 * Login Page
 * @var yii\web\View $this
 * @var common\models\LoginForm $model
 */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'เข้าสู่ระบบ - Dune\'s Auto Parts';
?>

<div class="login-page min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-cogs text-primary fa-2x"></i>
                            </div>
                            <h4 class="fw-bold text-primary mb-1">Dune's Auto Parts</h4>
                            <p class="text-muted small">ระบบจัดการร้านอะไหล่รถยนต์</p>
                        </div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => ['class' => 'form-floating-labels'],
                        ]); ?>

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="fas fa-user me-1"></i> ชื่อผู้ใช้
                            </label>
                            <?= $form->field($model, 'username', [
                                'options' => ['class' => ''],
                            ])->textInput([
                                'class' => 'form-control form-control-lg',
                                'placeholder' => 'กรอกชื่อผู้ใช้',
                                'autofocus' => true,
                            ])->label(false) ?>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">
                                <i class="fas fa-lock me-1"></i> รหัสผ่าน
                            </label>
                            <?= $form->field($model, 'password', [
                                'options' => ['class' => ''],
                            ])->passwordInput([
                                'class' => 'form-control form-control-lg',
                                'placeholder' => 'กรอกรหัสผ่าน',
                            ])->label(false) ?>
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4">
                            <?= $form->field($model, 'rememberMe')->checkbox([
                                'template' => '<div class="form-check">{input}{label}</div>{error}',
                                'class' => 'form-check-input',
                            ])->label('จดจำการเข้าสู่ระบบ') ?>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <?= Html::submitButton(
                                '<i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ',
                                ['class' => 'btn btn-primary btn-lg', 'name' => 'login-button']
                            ) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white-50 small mb-0">
                        &copy; <?= date('Y') ?> Dune's Auto Parts<br>
                        All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
