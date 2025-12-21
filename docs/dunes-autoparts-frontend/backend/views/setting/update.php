<?php
use yii\helpers\Html;

$this->title = 'แก้ไขการตั้งค่า: ' . $model->setting_label;
$this->params['breadcrumbs'][] = ['label' => 'การตั้งค่าระบบ', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="setting-update">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-pencil me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
