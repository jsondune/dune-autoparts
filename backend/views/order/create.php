<?php
/**
 * Order Create - สร้างคำสั่งซื้อใหม่
 * @var yii\web\View $this
 * @var common\models\Order $model
 * @var array $customers
 * @var common\models\Customer $selectedCustomer (optional)
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'สร้างคำสั่งซื้อ';
?>

<div class="order-create">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">คำสั่งซื้อ</a></li>
                    <li class="breadcrumb-item active">สร้างใหม่</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers ?? [],
        'selectedCustomer' => $selectedCustomer ?? null,
    ]) ?>
</div>
