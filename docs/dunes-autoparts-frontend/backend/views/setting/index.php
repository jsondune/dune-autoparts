<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Setting;

$this->title = 'การตั้งค่าระบบ';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="setting-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-gear me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div>
            <a href="<?= Url::to(['setting/initialize']) ?>" class="btn btn-outline-secondary" onclick="return confirm('ต้องการสร้างการตั้งค่าเริ่มต้นหรือไม่?')">
                <i class="bi bi-arrow-repeat me-1"></i>สร้างค่าเริ่มต้น
            </a>
            <a href="<?= Url::to(['setting/create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>เพิ่มการตั้งค่า
            </a>
        </div>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-0">
                    <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                        <?php $first = true; ?>
                        <?php foreach ($settingGroups as $groupKey => $groupInfo): ?>
                        <button class="nav-link text-start rounded-0 <?= $first ? 'active' : '' ?>" 
                                id="<?= $groupKey ?>-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#<?= $groupKey ?>-pane" 
                                type="button" 
                                role="tab">
                            <i class="bi <?= $groupInfo['icon'] ?> me-2"></i>
                            <?= $groupInfo['label'] ?>
                            <?php if (isset($groupedSettings[$groupKey])): ?>
                            <span class="badge bg-secondary float-end"><?= count($groupedSettings[$groupKey]) ?></span>
                            <?php endif; ?>
                        </button>
                        <?php $first = false; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-md-9">
            <?= Html::beginForm(['setting/save'], 'post', ['id' => 'settings-form']) ?>
            
            <div class="tab-content" id="settings-tabContent">
                <?php $first = true; ?>
                <?php foreach ($settingGroups as $groupKey => $groupInfo): ?>
                <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" 
                     id="<?= $groupKey ?>-pane" 
                     role="tabpanel">
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi <?= $groupInfo['icon'] ?> me-2"></i>
                                <?= $groupInfo['label'] ?>
                            </h5>
                            <small class="text-muted"><?= $groupInfo['description'] ?></small>
                        </div>
                        <div class="card-body">
                            <?php if (isset($groupedSettings[$groupKey]) && !empty($groupedSettings[$groupKey])): ?>
                                <?php foreach ($groupedSettings[$groupKey] as $setting): ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <label class="form-label fw-medium" for="setting-<?= $setting->setting_key ?>">
                                            <?= Html::encode($setting->setting_label) ?>
                                            <?php if ($setting->is_system): ?>
                                            <span class="badge bg-info ms-1">ระบบ</span>
                                            <?php endif; ?>
                                        </label>
                                        <?php if (!$setting->is_system): ?>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= Url::to(['setting/update', 'id' => $setting->id]) ?>" class="btn btn-outline-secondary" title="แก้ไข">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= Url::to(['setting/delete', 'id' => $setting->id]) ?>" class="btn btn-outline-danger" title="ลบ" onclick="return confirm('ต้องการลบการตั้งค่านี้หรือไม่?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($setting->setting_description): ?>
                                    <small class="text-muted d-block mb-2"><?= Html::encode($setting->setting_description) ?></small>
                                    <?php endif; ?>
                                    
                                    <?php
                                    // Render input based on type
                                    $inputId = 'setting-' . $setting->setting_key;
                                    $inputName = "Setting[{$setting->setting_key}]";
                                    
                                    switch ($setting->setting_type):
                                        case Setting::TYPE_BOOLEAN:
                                    ?>
                                    <div class="form-check form-switch">
                                        <?= Html::checkbox($inputName, (bool)$setting->setting_value, [
                                            'class' => 'form-check-input',
                                            'id' => $inputId,
                                            'value' => '1',
                                            'uncheck' => '0',
                                        ]) ?>
                                        <label class="form-check-label" for="<?= $inputId ?>">
                                            <?= $setting->setting_value ? 'เปิดใช้งาน' : 'ปิดใช้งาน' ?>
                                        </label>
                                    </div>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_TEXTAREA:
                                    ?>
                                    <?= Html::textarea($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                        'rows' => 3,
                                    ]) ?>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_NUMBER:
                                    ?>
                                    <?= Html::textInput($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                        'type' => 'number',
                                        'step' => 'any',
                                    ]) ?>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_PASSWORD:
                                    ?>
                                    <div class="input-group">
                                        <?= Html::passwordInput($inputName, $setting->setting_value, [
                                            'class' => 'form-control',
                                            'id' => $inputId,
                                        ]) ?>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="<?= $inputId ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_EMAIL:
                                    ?>
                                    <?= Html::textInput($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                        'type' => 'email',
                                    ]) ?>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_URL:
                                    ?>
                                    <?= Html::textInput($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                        'type' => 'url',
                                        'placeholder' => 'https://',
                                    ]) ?>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_TIME:
                                    ?>
                                    <?= Html::textInput($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                        'type' => 'time',
                                    ]) ?>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_DATE:
                                    ?>
                                    <?= Html::textInput($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                        'type' => 'date',
                                    ]) ?>
                                    <?php
                                        break;
                                        
                                        case Setting::TYPE_JSON:
                                    ?>
                                    <?= Html::textarea($inputName, $setting->setting_value, [
                                        'class' => 'form-control font-monospace',
                                        'id' => $inputId,
                                        'rows' => 4,
                                        'style' => 'font-size: 13px;',
                                    ]) ?>
                                    <small class="text-muted">รูปแบบ JSON</small>
                                    <?php
                                        break;
                                        
                                        default:
                                    ?>
                                    <?= Html::textInput($inputName, $setting->setting_value, [
                                        'class' => 'form-control',
                                        'id' => $inputId,
                                    ]) ?>
                                    <?php
                                        break;
                                    endswitch;
                                    ?>
                                    
                                    <small class="text-muted d-block mt-1">
                                        <code><?= Html::encode($setting->setting_key) ?></code>
                                    </small>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">ยังไม่มีการตั้งค่าในกลุ่มนี้</p>
                                <a href="<?= Url::to(['setting/create', 'group' => $groupKey]) ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus me-1"></i>เพิ่มการตั้งค่า
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php $first = false; ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Save Button -->
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save me-2"></i>บันทึกการตั้งค่า
                    </button>
                    <a href="<?= Url::to(['site/index']) ?>" class="btn btn-outline-secondary btn-lg ms-2">
                        ยกเลิก
                    </a>
                </div>
            </div>
            
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<style>
.nav-pills .nav-link {
    color: #495057;
    border-bottom: 1px solid #dee2e6;
    padding: 12px 16px;
}
.nav-pills .nav-link:last-child {
    border-bottom: none;
}
.nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
}
.nav-pills .nav-link:hover:not(.active) {
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
    
    // Restore active tab from URL hash
    const hash = window.location.hash;
    if (hash) {
        const tabId = hash.replace('#', '') + '-tab';
        const tab = document.getElementById(tabId);
        if (tab) {
            tab.click();
        }
    }
    
    // Update URL hash when tab changes
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            const id = e.target.id.replace('-tab', '');
            history.replaceState(null, null, '#' + id);
        });
    });
});
</script>
