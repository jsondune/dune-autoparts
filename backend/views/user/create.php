<?php
use yii\helpers\Html;

$this->title = 'เพิ่มผู้ใช้งานใหม่';
$this->params['breadcrumbs'][] = ['label' => 'ผู้ใช้งาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-create">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-person-plus text-primary me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
