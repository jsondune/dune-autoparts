<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string $content */

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css');
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_END]);

// Get cart count
$cartCount = 0;
if (isset(Yii::$app->session['cart'])) {
    $cartCount = count(Yii::$app->session['cart']);
}

// Get categories for menu
$categories = \common\models\PartCategory::find()->orderBy('name_th')->all();
$brands = \common\models\VehicleBrand::find()->orderBy('name_th')->limit(10)->all();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?> - <?= Yii::$app->params['shopName'] ?></title>
    <?php $this->head() ?>
    <style>
        * { font-family: 'Sarabun', sans-serif; }
        
        /* Top Bar */
        .top-bar {
            background: #1a1a2e;
            color: #fff;
            padding: 8px 0;
            font-size: 0.85rem;
        }
        .top-bar a { color: #fff; text-decoration: none; }
        .top-bar a:hover { color: #ffc107; }
        
        /* Header */
        .main-header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #e63946;
            text-decoration: none;
        }
        .logo:hover { color: #d62839; }
        .logo i { color: #1a1a2e; }
        
        /* Search Box */
        .search-box {
            max-width: 500px;
        }
        .search-box .form-control {
            border-radius: 25px 0 0 25px;
            border-right: none;
        }
        .search-box .btn {
            border-radius: 0 25px 25px 0;
            background: #e63946;
            border-color: #e63946;
        }
        .search-box .btn:hover {
            background: #d62839;
        }
        
        /* Header Icons */
        .header-icon {
            text-decoration: none;
            color: #333;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .header-icon:hover { color: #e63946; }
        .header-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        /* Navigation */
        .main-nav {
            background: #1a1a2e;
        }
        .main-nav .nav-link {
            color: #fff !important;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .main-nav .nav-link:hover,
        .main-nav .nav-link.active {
            background: #e63946;
        }
        .main-nav .dropdown-menu {
            border-radius: 0;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            padding: 60px 0;
        }
        
        /* Cards */
        .product-card {
            border: none;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .product-card .price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #e63946;
        }
        .product-card .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }
        
        /* Category Card */
        .category-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            display: block;
        }
        .category-card:hover {
            background: #e63946;
            color: #fff;
            transform: scale(1.05);
        }
        .category-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        /* Buttons */
        .btn-primary {
            background: #e63946;
            border-color: #e63946;
        }
        .btn-primary:hover {
            background: #d62839;
            border-color: #d62839;
        }
        .btn-outline-primary {
            color: #e63946;
            border-color: #e63946;
        }
        .btn-outline-primary:hover {
            background: #e63946;
            border-color: #e63946;
        }
        
        /* Footer */
        .main-footer {
            background: #1a1a2e;
            color: #fff;
            padding: 50px 0 20px;
        }
        .main-footer h5 {
            color: #e63946;
            margin-bottom: 20px;
        }
        .main-footer a {
            color: #ccc;
            text-decoration: none;
            display: block;
            padding: 5px 0;
        }
        .main-footer a:hover { color: #fff; }
        .footer-bottom {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 20px;
        }
        
        /* Breadcrumb */
        .breadcrumb-section {
            background: #f8f9fa;
            padding: 15px 0;
        }
        
        /* Badge */
        .badge-sale {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e63946;
            color: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.75rem;
        }
        .badge-new {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.75rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .top-bar { display: none; }
            .header-icon span { display: none; }
            .search-box { margin: 15px 0; }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <i class="bi bi-telephone me-2"></i><?= Yii::$app->params['shopPhone'] ?>
                <span class="ms-3"><i class="bi bi-envelope me-2"></i><?= Yii::$app->params['shopEmail'] ?></span>
            </div>
            <div class="col-md-6 text-end">
                <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                <a href="#" class="me-3"><i class="bi bi-line"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="main-header py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <a href="<?= Url::to(['/site/index']) ?>" class="logo">
                    <i class="bi bi-car-front-fill me-2"></i>Dune's
                </a>
            </div>
            <div class="col-md-5">
                <form action="<?= Url::to(['/part/search']) ?>" method="get" class="search-box">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="ค้นหาอะไหล่... เช่น ผ้าเบรค, กรองอากาศ">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end align-items-center">
                    <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['/customer/login']) ?>" class="header-icon">
                        <i class="bi bi-person fs-4"></i>
                        <span>เข้าสู่ระบบ</span>
                    </a>
                    <?php else: ?>
                    <div class="dropdown">
                        <a href="#" class="header-icon dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-4"></i>
                            <span><?= Yii::$app->user->identity->full_name ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= Url::to(['/customer/profile']) ?>"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
                            <li><a class="dropdown-item" href="<?= Url::to(['/order/history']) ?>"><i class="bi bi-bag me-2"></i>ประวัติสั่งซื้อ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <?= Html::beginForm(['/customer/logout'], 'post') ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                                </button>
                                <?= Html::endForm() ?>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <a href="<?= Url::to(['/cart/index']) ?>" class="header-icon position-relative">
                        <i class="bi bi-cart3 fs-4"></i>
                        <span>ตะกร้า</span>
                        <?php if ($cartCount > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="main-nav">
    <div class="container">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['/site/index']) ?>">
                    <i class="bi bi-house me-1"></i>หน้าแรก
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-grid me-1"></i>หมวดหมู่สินค้า
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($categories as $cat): ?>
                    <li><a class="dropdown-item" href="<?= Url::to(['/part/category', 'id' => $cat->id]) ?>"><?= Html::encode($cat->name_th) ?></a></li>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                    <li><a class="dropdown-item" href="<?= Url::to(['/part/index']) ?>">ดูทั้งหมด</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="bi bi-car-front me-1"></i>ยี่ห้อรถ
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($brands as $brand): ?>
                    <li><a class="dropdown-item" href="<?= Url::to(['/part/brand', 'id' => $brand->id]) ?>"><?= Html::encode($brand->name_th) ?></a></li>
                    <?php endforeach; ?>
                    <?php if (empty($brands)): ?>
                    <li><a class="dropdown-item" href="<?= Url::to(['/part/index']) ?>">ดูทั้งหมด</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['/part/index']) ?>">
                    <i class="bi bi-box me-1"></i>สินค้าทั้งหมด
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['/site/about']) ?>">
                    <i class="bi bi-info-circle me-1"></i>เกี่ยวกับเรา
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['/site/contact']) ?>">
                    <i class="bi bi-telephone me-1"></i>ติดต่อเรา
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Flash Messages -->
<?php if (Yii::$app->session->hasFlash('success')): ?>
<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i><?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<main>
    <?= $content ?>
</main>

<!-- Footer -->
<footer class="main-footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><i class="bi bi-car-front-fill me-2"></i>Dune's Auto Parts</h5>
                <p class="text-muted"><?= Yii::$app->params['shopSlogan'] ?></p>
                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><?= Yii::$app->params['shopAddress'] ?></p>
                <p class="mb-1"><i class="bi bi-telephone me-2"></i><?= Yii::$app->params['shopPhone'] ?></p>
                <p class="mb-1"><i class="bi bi-envelope me-2"></i><?= Yii::$app->params['shopEmail'] ?></p>
            </div>
            <div class="col-md-2 mb-4">
                <h5>หมวดหมู่</h5>
                <?php foreach (array_slice($categories, 0, 6) as $cat): ?>
                <a href="<?= Url::to(['/part/category', 'id' => $cat->id]) ?>"><?= Html::encode($cat->name_th) ?></a>
                <?php endforeach; ?>
            </div>
            <div class="col-md-2 mb-4">
                <h5>ลูกค้า</h5>
                <a href="<?= Url::to(['/customer/login']) ?>">เข้าสู่ระบบ</a>
                <a href="<?= Url::to(['/customer/register']) ?>">สมัครสมาชิก</a>
                <a href="<?= Url::to(['/order/history']) ?>">ประวัติสั่งซื้อ</a>
                <a href="<?= Url::to(['/cart/index']) ?>">ตะกร้าสินค้า</a>
            </div>
            <div class="col-md-2 mb-4">
                <h5>ข้อมูล</h5>
                <a href="<?= Url::to(['/site/about']) ?>">เกี่ยวกับเรา</a>
                <a href="<?= Url::to(['/site/contact']) ?>">ติดต่อเรา</a>
                <a href="#">นโยบายความเป็นส่วนตัว</a>
                <a href="#">เงื่อนไขการใช้งาน</a>
            </div>
            <div class="col-md-2 mb-4">
                <h5>ติดตามเรา</h5>
                <a href="#"><i class="bi bi-facebook me-2"></i>Facebook</a>
                <a href="#"><i class="bi bi-line me-2"></i>Line</a>
                <a href="#"><i class="bi bi-instagram me-2"></i>Instagram</a>
                <a href="#"><i class="bi bi-youtube me-2"></i>YouTube</a>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p class="mb-0 text-muted">&copy; <?= date('Y') ?> Dune's Auto Parts. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
