<?php
/**
 * Create Part - เพิ่มสินค้าใหม่
 * @var yii\web\View $this
 * @var common\models\Part $model
 * @var array $categories
 * @var array $brands
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'เพิ่มสินค้าใหม่';
?>

<div class="part-create">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">สินค้า</a></li>
                    <li class="breadcrumb-item active">เพิ่มใหม่</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'brands' => $brands,
    ]) ?>
</div>
