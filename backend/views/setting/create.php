<?php
use yii\helpers\Html;

$this->title = 'เพิ่มการตั้งค่า';
$this->params['breadcrumbs'][] = ['label' => 'การตั้งค่าระบบ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="setting-create">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-plus-circle me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
