<?php
use yii\helpers\Html;

$this->title = 'เกี่ยวกับเรา';
?>

<div class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/site/index']) ?>">หน้าแรก</a></li>
                <li class="breadcrumb-item active">เกี่ยวกับเรา</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <h1 class="display-5 fw-bold mb-4">
                <i class="bi bi-car-front-fill text-primary me-2"></i>Dune's Auto Parts
            </h1>
            <p class="lead"><?= Yii::$app->params['shopSlogan'] ?></p>
            <p>เราเป็นร้านจำหน่ายอะไหล่รถยนต์ที่มีประสบการณ์มากกว่า 10 ปี มุ่งมั่นให้บริการลูกค้าด้วยสินค้าคุณภาพและราคาที่เป็นธรรม</p>
            
            <h5 class="mt-4 mb-3"><i class="bi bi-bullseye text-primary me-2"></i>วิสัยทัศน์</h5>
            <p>เป็นผู้นำด้านการจำหน่ายอะไหล่รถยนต์ออนไลน์ที่ลูกค้าไว้วางใจ</p>
            
            <h5 class="mt-4 mb-3"><i class="bi bi-rocket-takeoff text-primary me-2"></i>พันธกิจ</h5>
            <ul>
                <li>จำหน่ายอะไหล่คุณภาพในราคาที่เป็นธรรม</li>
                <li>บริการรวดเร็ว จัดส่งตรงเวลา</li>
                <li>ให้คำปรึกษาด้านอะไหล่อย่างมืออาชีพ</li>
                <li>สร้างความพึงพอใจสูงสุดให้ลูกค้า</li>
            </ul>
        </div>
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=600" 
                 alt="Auto Parts Shop" class="img-fluid rounded shadow">
        </div>
    </div>
    
    <!-- Why Choose Us -->
    <div class="row mt-5">
        <div class="col-12 text-center mb-4">
            <h2><i class="bi bi-patch-check text-primary me-2"></i>ทำไมต้องเลือกเรา</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-award fs-1 text-warning mb-3"></i>
                    <h5>สินค้าคุณภาพ</h5>
                    <p class="text-muted">คัดสรรเฉพาะอะไหล่คุณภาพดี ทั้งแท้และเทียบเท่า พร้อมรับประกัน</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-1 text-success mb-3"></i>
                    <h5>ราคาโรงงาน</h5>
                    <p class="text-muted">รับสินค้าตรงจากผู้ผลิต ไม่ผ่านคนกลาง ราคาถูกกว่าท้องตลาด</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                    <h5>จัดส่งรวดเร็ว</h5>
                    <p class="text-muted">จัดส่งทั่วประเทศ 1-3 วันทำการ ฟรีค่าส่งเมื่อซื้อครบ <?= number_format(Yii::$app->params['freeShippingMinimum']) ?> บาท</p>
                </div>
            </div>
        </div>
    </div>
</div>
