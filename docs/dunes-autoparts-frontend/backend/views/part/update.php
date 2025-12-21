<?php
/**
 * Update Part - แก้ไขสินค้า
 * @var yii\web\View $this
 * @var common\models\Part $model
 * @var array $categories
 * @var array $brands
 * @var array $compatibleVehicleIds
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'แก้ไข: ' . $model->sku;
?>

<div class="part-update">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">สินค้า</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['view', 'id' => $model->id]) ?>"><?= Html::encode($model->sku) ?></a></li>
                    <li class="breadcrumb-item active">แก้ไข</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
        <div>
            <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'brands' => $brands,
        'compatibleVehicleIds' => $compatibleVehicleIds,
    ]) ?>
</div>
