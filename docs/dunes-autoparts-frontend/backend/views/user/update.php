<?php
use yii\helpers\Html;

$this->title = 'แก้ไขผู้ใช้งาน: ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'ผู้ใช้งาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="user-update">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-pencil text-warning me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div>
            <?= Html::a('<i class="bi bi-eye me-1"></i>ดูข้อมูล', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-info me-2']) ?>
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
