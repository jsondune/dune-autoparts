<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string $content */

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);

$currentController = Yii::$app->controller->id;
$currentAction = Yii::$app->controller->action->id;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - Dune's Auto Parts</title>
    <?php $this->head() ?>
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
        }
        
        * { font-family: 'Sarabun', 'Segoe UI', sans-serif; }
        body { background: var(--light-bg); font-size: 16px; }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--dark-color) 0%, #0f172a 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.25rem;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu .menu-header {
            padding: 1rem 1.5rem 0.5rem;
            color: rgba(255,255,255,0.4);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.85rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        
        .sidebar-menu a.active {
            background: var(--primary-color);
            color: #fff;
            font-weight: 500;
        }
        
        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        
        .sidebar-menu .badge {
            margin-left: auto;
            font-size: 0.7rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Top Navigation */
        .top-nav {
            background: #fff;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .top-nav .breadcrumb {
            margin: 0;
            background: none;
            padding: 0;
        }
        
        .top-nav .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .top-nav .user-menu .notifications {
            position: relative;
        }
        
        .top-nav .user-menu .notifications .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        /* Page Content */
        .page-content {
            padding: 1.5rem;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .card-body { padding: 1.25rem; }
        
        /* Stat Cards */
        .stat-card {
            padding: 1.25rem;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-card .stat-icon.primary { background: rgba(37,99,235,0.1); color: var(--primary-color); }
        .stat-card .stat-icon.success { background: rgba(16,185,129,0.1); color: var(--success-color); }
        .stat-card .stat-icon.warning { background: rgba(245,158,11,0.1); color: var(--warning-color); }
        .stat-card .stat-icon.danger { background: rgba(239,68,68,0.1); color: var(--danger-color); }
        
        .stat-card .stat-content h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-color);
        }
        
        .stat-card .stat-content p {
            margin: 0;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }
        
        /* Tables */
        .table { font-size: 0.95rem; }
        .table th { 
            font-weight: 600; 
            color: var(--secondary-color);
            border-bottom-width: 1px;
            white-space: nowrap;
        }
        .table td { vertical-align: middle; }
        
        /* Status Badges */
        .badge { font-weight: 500; padding: 0.4em 0.8em; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-preparing { background: #e0e7ff; color: #4338ca; }
        .badge-shipped { background: #d1fae5; color: #065f46; }
        .badge-delivered { background: #10b981; color: #fff; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-paid { background: #10b981; color: #fff; }
        .badge-unpaid { background: #ef4444; color: #fff; }
        .badge-partial { background: #f59e0b; color: #fff; }
        
        /* Buttons */
        .btn { font-weight: 500; padding: 0.5rem 1rem; border-radius: 8px; }
        .btn-primary { background: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        
        /* Forms */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-label { font-weight: 500; margin-bottom: 0.4rem; }
        
        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block !important; }
        }
        
        .sidebar-toggle { display: none; }
        
        /* Alert Flash */
        .alert { border-radius: 8px; border: none; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4>🔧 Dune's Auto Parts</h4>
        <small>ระบบจัดการร้านอะไหล่</small>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-header">หน้าหลัก</div>
        <a href="<?= Url::to(['/site/index']) ?>" class="<?= $currentController == 'site' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        
        <div class="menu-header">การขาย</div>
        <a href="<?= Url::to(['/order/index']) ?>" class="<?= $currentController == 'order' ? 'active' : '' ?>">
            <i class="bi bi-cart3"></i> คำสั่งซื้อ
            <?php if (($pendingOrders ?? 0) > 0): ?>
            <span class="badge bg-danger"><?= $pendingOrders ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= Url::to(['/inquiry/index']) ?>" class="<?= $currentController == 'inquiry' ? 'active' : '' ?>">
            <i class="bi bi-chat-dots"></i> สอบถาม/แชท
            <?php if (($openInquiries ?? 0) > 0): ?>
            <span class="badge bg-warning"><?= $openInquiries ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= Url::to(['/customer/index']) ?>" class="<?= $currentController == 'customer' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> ลูกค้า
        </a>
        
        <div class="menu-header">สินค้า</div>
        <a href="<?= Url::to(['/part/index']) ?>" class="<?= $currentController == 'part' && $currentAction == 'index' ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> อะไหล่ทั้งหมด
        </a>
        <a href="<?= Url::to(['/part/low-stock']) ?>" class="<?= $currentAction == 'low-stock' ? 'active' : '' ?>">
            <i class="bi bi-exclamation-triangle"></i> สินค้าใกล้หมด
            <?php if (($lowStockCount ?? 0) > 0): ?>
            <span class="badge bg-warning"><?= $lowStockCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= Url::to(['/part/stock-history']) ?>" class="<?= $currentAction == 'stock-history' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i> ประวัติสต็อก
        </a>
        
        <div class="menu-header">รายงาน</div>
        <a href="<?= Url::to(['/report/sales']) ?>">
            <i class="bi bi-graph-up"></i> รายงานยอดขาย
        </a>
        <a href="<?= Url::to(['/report/inventory']) ?>">
            <i class="bi bi-clipboard-data"></i> รายงานสินค้า
        </a>
        
        <div class="menu-header">ตั้งค่า</div>
        <a href="<?= Url::to(['/setting/index']) ?>">
            <i class="bi bi-gear"></i> ตั้งค่าระบบ
        </a>
        <a href="<?= Url::to(['/user/index']) ?>">
            <i class="bi bi-person-gear"></i> จัดการผู้ใช้
        </a>
    </nav>
</aside>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Navigation -->
    <header class="top-nav">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link sidebar-toggle p-0" onclick="document.querySelector('.sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-4"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>">หน้าหลัก</a></li>
                    <?php if ($this->title): ?>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        
        <div class="user-menu">
            <div class="dropdown">
                <button class="btn btn-link text-dark dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= Yii::$app->user->isGuest ? 'Guest' : Yii::$app->user->identity->username ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= Url::to(['/user/profile']) ?>"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
                    <li><a class="dropdown-item" href="<?= Url::to(['/setting/index']) ?>"><i class="bi bi-gear me-2"></i>ตั้งค่า</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <?= Html::beginForm(['/site/logout'], 'post') ?>
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                        </button>
                        <?= Html::endForm() ?>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    
    <!-- Page Content -->
    <div class="page-content">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endforeach; ?>
        
        <?= $content ?>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
