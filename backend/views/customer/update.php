<?php
/**
 * Customer Update - แก้ไขลูกค้า
 * @var yii\web\View $this
 * @var common\models\Customer $model
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'แก้ไขลูกค้า: ' . $model->getDisplayName();
?>

<div class="customer-update">
    <!-- Page Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">ลูกค้า</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['view', 'id' => $model->id]) ?>"><?= Html::encode($model->customer_code) ?></a></li>
                <li class="breadcrumb-item active">แก้ไข</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
