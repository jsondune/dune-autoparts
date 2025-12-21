<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Part;
use common\models\Order;
use common\models\Customer;
use common\models\Inquiry;
use common\models\StockMovement;
use common\models\Payment;

/**
 * Site controller - Dashboard และหน้าหลักของ Backend
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'dashboard'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Dashboard หลัก - แสดง KPIs และสรุปข้อมูลสำคัญ
     */
    public function actionIndex()
    {
        // === KPI Cards ===
        $todayStart = strtotime('today');
        $monthStart = strtotime('first day of this month');
        $lastMonthStart = strtotime('first day of last month');
        $lastMonthEnd = strtotime('last day of last month');
        
        // ยอดขายวันนี้
        $todaySales = Order::find()
            ->where(['>=', 'created_at', $todayStart])
            ->andWhere(['not in', 'status', ['cancelled']])
            ->sum('grand_total') ?? 0;
        
        // ยอดขายเดือนนี้
        $monthSales = Order::find()
            ->where(['>=', 'created_at', $monthStart])
            ->andWhere(['not in', 'status', ['cancelled']])
            ->sum('grand_total') ?? 0;
            
        // ยอดขายเดือนที่แล้ว (สำหรับเปรียบเทียบ)
        $lastMonthSales = Order::find()
            ->where(['>=', 'created_at', $lastMonthStart])
            ->andWhere(['<=', 'created_at', $lastMonthEnd])
            ->andWhere(['not in', 'status', ['cancelled']])
            ->sum('grand_total') ?? 0;
        
        // % การเปลี่ยนแปลง
        $salesChange = $lastMonthSales > 0 
            ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) 
            : 0;
        
        // จำนวนออเดอร์วันนี้
        $todayOrders = Order::find()
            ->where(['>=', 'created_at', $todayStart])
            ->count();
        
        // ออเดอร์รอดำเนินการ
        $pendingOrders = Order::find()
            ->where(['status' => ['pending', 'confirmed', 'preparing']])
            ->count();
        
        // สินค้าใกล้หมด (Low Stock)
        $lowStockCount = Part::find()
            ->where(['is_active' => 1])
            ->andWhere('stock_quantity <= min_stock_level')
            ->count();
        
        // สินค้าหมด (Out of Stock)
        $outOfStockCount = Part::find()
            ->where(['is_active' => 1])
            ->andWhere(['stock_quantity' => 0])
            ->count();
        
        // ลูกค้าใหม่เดือนนี้
        $newCustomers = Customer::find()
            ->where(['>=', 'created_at', $monthStart])
            ->count();
        
        // Inquiry ที่รอตอบ
        $openInquiries = Inquiry::find()
            ->where(['status' => ['open', 'in_progress']])
            ->count();

        // === Recent Orders (5 รายการล่าสุด) ===
        $recentOrders = Order::find()
            ->with(['customer'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // === Low Stock Alert ===
        $lowStockParts = Part::find()
            ->where(['is_active' => 1])
            ->andWhere('stock_quantity <= min_stock_level')
            ->orderBy(['stock_quantity' => SORT_ASC])
            ->limit(10)
            ->all();

        // === Open Inquiries ===
        $recentInquiries = Inquiry::find()
            ->where(['status' => ['open', 'in_progress']])
            ->with(['customer', 'assignedTo'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // === Sales Chart Data (7 วันย้อนหลัง) ===
        $salesChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dayStart = strtotime("-$i days", strtotime('today'));
            $dayEnd = $dayStart + 86399;
            
            $daySales = Order::find()
                ->where(['>=', 'created_at', $dayStart])
                ->andWhere(['<=', 'created_at', $dayEnd])
                ->andWhere(['not in', 'status', ['cancelled']])
                ->sum('grand_total') ?? 0;
            
            $dayOrders = Order::find()
                ->where(['>=', 'created_at', $dayStart])
                ->andWhere(['<=', 'created_at', $dayEnd])
                ->count();
                
            $salesChartData[] = [
                'date' => date('d/m', $dayStart),
                'sales' => (float)$daySales,
                'orders' => (int)$dayOrders,
            ];
        }

        // === Order Status Summary ===
        $orderStatusSummary = [
            'pending' => Order::find()->where(['status' => 'pending'])->count(),
            'confirmed' => Order::find()->where(['status' => 'confirmed'])->count(),
            'preparing' => Order::find()->where(['status' => 'preparing'])->count(),
            'shipped' => Order::find()->where(['status' => 'shipped'])->count(),
            'delivered' => Order::find()->where(['status' => 'delivered'])->count(),
            'cancelled' => Order::find()->where(['status' => 'cancelled'])->count(),
        ];

        // === Top Selling Parts (เดือนนี้) ===
        $topSellingParts = (new \yii\db\Query())
            ->select(['{{part}}.*', 'SUM({{order_item}}.quantity) as total_sold'])
            ->from('{{part}}')
            ->leftJoin('{{order_item}}', '{{order_item}}.part_id = {{part}}.id')
            ->leftJoin('{{order}}', '{{order}}.id = {{order_item}}.order_id')
            ->where(['>=', '{{order}}.created_at', $monthStart])
            ->andWhere(['<>', '{{order}}.status', 'cancelled'])
            ->groupBy('{{part}}.id')
            ->orderBy(['total_sold' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'salesChange' => $salesChange,
            'todayOrders' => $todayOrders,
            'pendingOrders' => $pendingOrders,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'newCustomers' => $newCustomers,
            'openInquiries' => $openInquiries,
            'recentOrders' => $recentOrders,
            'lowStockParts' => $lowStockParts,
            'recentInquiries' => $recentInquiries,
            'salesChartData' => $salesChartData,
            'orderStatusSummary' => $orderStatusSummary,
            'topSellingParts' => $topSellingParts,
        ]);
    }

    /**
     * Login action
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'login';
        $model = new \common\models\LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
