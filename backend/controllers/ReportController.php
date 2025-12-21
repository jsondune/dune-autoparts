<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Order;
use common\models\OrderItem;
use common\models\Part;
use common\models\Customer;
use common\models\Payment;
use common\models\StockMovement;
use yii\helpers\ArrayHelper;

/**
 * ReportController - รายงานยอดขายและสต็อกสินค้า
 */
class ReportController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'export-*' => ['post'],
                ],
            ],
        ];
    }

    /**
     * รายงานยอดขาย
     */
    public function actionSales()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        $groupBy = Yii::$app->request->get('group_by', 'day');
        
        $dateFromTs = strtotime($dateFrom);
        $dateToTs = strtotime($dateTo . ' 23:59:59');
        
        // ยอดขายรวม
        $totalSales = Order::find()
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->sum('{{%order}}.grand_total') ?: 0;
        
        // จำนวนคำสั่งซื้อ
        $totalOrders = Order::find()
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->count();
        
        // คำสั่งซื้อที่ยกเลิก
        $cancelledOrders = Order::find()
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['{{%order}}.order_status' => Order::STATUS_CANCELLED])
            ->count();
        
        // ยอดขายเฉลี่ยต่อคำสั่งซื้อ
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        // ชิ้นส่วนที่ขายได้
        $totalItemsSold = OrderItem::find()
            ->joinWith('order')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->sum('order_item.quantity') ?: 0;
        
        // ลูกค้าใหม่
        $newCustomers = Customer::find()
            ->where(['between', 'created_at', $dateFromTs, $dateToTs])
            ->count();
        
        // ยอดรับชำระ
        $totalPayments = Payment::find()
            ->where(['between', 'created_at', $dateFromTs, $dateToTs])
            ->andWhere(['payment_status' => Payment::STATUS_VERIFIED])
            ->sum('amount') ?: 0;
        
        // ข้อมูลกราฟยอดขายตามช่วงเวลา
        $salesChartData = $this->getSalesChartData($dateFromTs, $dateToTs, $groupBy);
        
        // สินค้าขายดี
        $topProducts = $this->getTopProducts($dateFromTs, $dateToTs, 10);
        
        // หมวดหมู่ขายดี
        $topCategories = $this->getTopCategories($dateFromTs, $dateToTs);
        
        // ยอดขายตามช่องทาง
        $salesByChannel = $this->getSalesByChannel($dateFromTs, $dateToTs);
        
        // ยอดขายตามพนักงาน (ถ้ามี)
        $salesByStaff = $this->getSalesByStaff($dateFromTs, $dateToTs);
        
        // รายการคำสั่งซื้อล่าสุด
        $recentOrders = Order::find()
            ->with(['customer', 'items'])
            ->where(['between', 'created_at', $dateFromTs, $dateToTs])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();
        
        return $this->render('sales', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'groupBy' => $groupBy,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'cancelledOrders' => $cancelledOrders,
            'avgOrderValue' => $avgOrderValue,
            'totalItemsSold' => $totalItemsSold,
            'newCustomers' => $newCustomers,
            'totalPayments' => $totalPayments,
            'salesChartData' => $salesChartData,
            'topProducts' => $topProducts,
            'topCategories' => $topCategories,
            'salesByChannel' => $salesByChannel,
            'salesByStaff' => $salesByStaff,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * รายงานสต็อกสินค้า
     */
    public function actionInventory()
    {
        $categoryId = Yii::$app->request->get('category_id');
        $partType = Yii::$app->request->get('part_type');
        $stockStatus = Yii::$app->request->get('stock_status');
        
        // สรุปสต็อก
        $totalParts = Part::find()->andWhere(['is_active' => 1])->count();
        $totalStock = Part::find()->andWhere(['is_active' => 1])->sum('stock_quantity') ?: 0;
        $totalValue = Part::find()->andWhere(['is_active' => 1])->sum('stock_quantity * cost_price') ?: 0;
        $totalRetailValue = Part::find()->andWhere(['is_active' => 1])->sum('stock_quantity * selling_price') ?: 0;
        
        // สินค้าหมด
        $outOfStock = Part::find()
            ->andWhere(['is_active' => 1])
            ->andWhere(['<=', 'stock_quantity', 0])
            ->count();
        
        // สินค้าใกล้หมด
        $lowStock = Part::find()
            ->andWhere(['is_active' => 1])
            ->andWhere(['>', 'stock_quantity', 0])
            ->andWhere('stock_quantity <= min_stock_level')
            ->count();
        
        // สินค้าเกินสต็อก (มากกว่า 3 เท่าของ min_stock_level)
        $overStock = Part::find()
            ->andWhere(['is_active' => 1])
            ->andWhere('stock_quantity > min_stock_level * 3')
            ->andWhere(['>', 'min_stock_level', 0])
            ->count();
        
        // สินค้าไม่เคลื่อนไหว (ไม่มีการขายใน 90 วัน)
        $deadStock = $this->getDeadStockCount(90);
        
        // Query สำหรับรายการสินค้า
        $query = Part::find()
            ->with(['category', 'supplier'])
            ->andWhere(['is_active' => 1]);
        
        if ($categoryId) {
            $query->andWhere(['category_id' => $categoryId]);
        }
        
        if ($partType) {
            $query->andWhere(['part_type' => $partType]);
        }
        
        if ($stockStatus === 'out') {
            $query->andWhere(['<=', 'stock_quantity', 0]);
        } elseif ($stockStatus === 'low') {
            $query->andWhere(['>', 'stock_quantity', 0])
                  ->andWhere('stock_quantity <= min_stock_level');
        } elseif ($stockStatus === 'normal') {
            $query->andWhere(['>', 'stock_quantity', 'min_stock_level']);
        } elseif ($stockStatus === 'over') {
            $query->andWhere('stock_quantity > min_stock_level * 3')
                  ->andWhere(['>', 'min_stock_level', 0]);
        }
        
        // สต็อกตามหมวดหมู่
        $stockByCategory = $this->getStockByCategory();
        
        // สต็อกตามประเภทสินค้า
        $stockByType = $this->getStockByType();
        
        // การเคลื่อนไหวสต็อกล่าสุด
        $recentMovements = StockMovement::find()
            ->with(['part', 'user'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();
        
        // สินค้าที่ต้องสั่งซื้อ
        $reorderList = Part::find()
            ->with(['category', 'supplier'])
            ->andWhere(['is_active' => 1])
            ->andWhere('stock_quantity <= min_stock_level')
            ->orderBy(['stock_quantity' => SORT_ASC])
            ->limit(50)
            ->all();
        
        // สินค้าตามมูลค่า (ABC Analysis)
        $abcAnalysis = $this->getABCAnalysis();
        
        return $this->render('inventory', [
            'categoryId' => $categoryId,
            'partType' => $partType,
            'stockStatus' => $stockStatus,
            'totalParts' => $totalParts,
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
            'totalRetailValue' => $totalRetailValue,
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock,
            'overStock' => $overStock,
            'deadStock' => $deadStock,
            'stockByCategory' => $stockByCategory,
            'stockByType' => $stockByType,
            'recentMovements' => $recentMovements,
            'reorderList' => $reorderList,
            'abcAnalysis' => $abcAnalysis,
        ]);
    }

    /**
     * Top Products - สินค้าขายดี
     */
    public function actionTopProducts()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        
        $dateFromTs = strtotime($dateFrom);
        $dateToTs = strtotime($dateTo . ' 23:59:59');
        
        // สินค้าขายดีทั้งหมด
        $topProducts = $this->getTopProducts($dateFromTs, $dateToTs, 100);
        
        // สรุปยอดรวม
        $totalQuantity = array_sum(array_column($topProducts, 'total_quantity'));
        $totalRevenue = array_sum(array_column($topProducts, 'total_revenue'));
        
        return $this->render('top-products', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'topProducts' => $topProducts,
            'totalQuantity' => $totalQuantity,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    /**
     * Top Customers - ลูกค้าชั้นนำ
     */
    public function actionTopCustomers()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        
        $dateFromTs = strtotime($dateFrom);
        $dateToTs = strtotime($dateTo . ' 23:59:59');
        
        // ลูกค้าที่มียอดซื้อสูงสุด
        $topCustomers = (new \yii\db\Query())
            ->select([
                '{{customer}}.id',
                '{{customer}}.customer_code',
                '{{customer}}.full_name',
                '{{customer}}.phone',
                '{{customer}}.customer_type',
                'COUNT({{%order}}.id) as order_count',
                'SUM({{%order}}.grand_total) as total_spent'
            ])
            ->from('{{%order}}')
            ->innerJoin('{{customer}}', '{{customer}}.id = {{%order}}.customer_id')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', Order::STATUS_CANCELLED])
            ->groupBy('{{customer}}.id')
            ->orderBy(['total_spent' => SORT_DESC])
            ->limit(100)
            ->all();
        
        // สรุปยอดรวม
        $totalOrders = array_sum(array_column($topCustomers, 'order_count'));
        $totalSpent = array_sum(array_column($topCustomers, 'total_spent'));
        
        return $this->render('top-customers', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'topCustomers' => $topCustomers,
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent,
        ]);
    }
        
    /**
     * ข้อมูลกราฟยอดขาย
     */
    protected function getSalesChartData($dateFromTs, $dateToTs, $groupBy)
    {
        $format = $groupBy === 'month' ? '%Y-%m' : ($groupBy === 'week' ? '%Y-%u' : '%Y-%m-%d');
        $displayFormat = $groupBy === 'month' ? 'M Y' : ($groupBy === 'week' ? 'สัปดาห์ที่ W' : 'd M');
        
        $query = (new \yii\db\Query())
            ->select([
                "DATE_FORMAT(FROM_UNIXTIME(created_at), '{$format}') as period",
                'SUM(grand_total) as total',
                'COUNT(*) as count'
            ])
            ->from('{{%order}}')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->groupBy('period')
            ->orderBy('period')
            ->all();
        
        $labels = [];
        $sales = [];
        $orders = [];
        
        foreach ($query as $row) {
            $labels[] = $row['period'];
            $sales[] = (float) $row['total'];
            $orders[] = (int) $row['count'];
        }
        
        return [
            'labels' => $labels,
            'sales' => $sales,
            'orders' => $orders,
        ];
    }

    /**
     * สินค้าขายดี
     */
    protected function getTopProducts($dateFromTs, $dateToTs, $limit = 10)
    {
        return (new \yii\db\Query())
            ->select([
                'part.id',
                'part.sku',
                'part.name_th',
                'part.name_en',
                'part.selling_price',
                'SUM(order_item.quantity) as total_quantity',
                'SUM(order_item.quantity * order_item.unit_price) as total_revenue'
            ])
            ->from('order_item')
            ->innerJoin('{{%order}}', '{{%order}}.id = order_item.order_id')
            ->innerJoin('part', 'part.id = order_item.part_id')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->groupBy('part.id')
            ->orderBy(['total_quantity' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * หมวดหมู่ขายดี
     */
    protected function getTopCategories($dateFromTs, $dateToTs)
    {
        return (new \yii\db\Query())
            ->select([
                'part_category.id',
                'part_category.name_th',
                'SUM(order_item.quantity) as total_quantity',
                'SUM(order_item.quantity * order_item.unit_price) as total_revenue'
            ])
            ->from('order_item')
            ->innerJoin('{{%order}}', '{{%order}}.id = order_item.order_id')
            ->innerJoin('part', 'part.id = order_item.part_id')
            ->innerJoin('part_category', 'part_category.id = part.category_id')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->groupBy('part_category.id')
            ->orderBy(['total_revenue' => SORT_DESC])
            ->all();
    }

    /**
     * ยอดขายตามช่องทาง
     */
    protected function getSalesByChannel($dateFromTs, $dateToTs)
    {
        return (new \yii\db\Query())
            ->select([
                '{{%order}}.order_number',
                'COUNT(*) as count',
                'SUM({{%order}}.grand_total) as total'
            ])
            ->from('{{%order}}')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->groupBy('{{%order}}.order_number')
            ->orderBy(['total' => SORT_DESC])
            ->all();
    }

    /**
     * ยอดขายตามพนักงาน
     */
    protected function getSalesByStaff($dateFromTs, $dateToTs)
    {
        return (new \yii\db\Query())
            ->select([
                'users.id',
                'users.username',
                'COUNT(*) as order_count',
                'SUM(order.grand_total) as total_sales'
            ])
            ->from('order')
            ->leftJoin('users', 'users.id = {{%order}}.created_by')
            ->where(['between', '{{%order}}.created_at', $dateFromTs, $dateToTs])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->groupBy('order.created_by')
            ->orderBy(['total_sales' => SORT_DESC])
            ->all();
    }

    /**
     * สต็อกตามหมวดหมู่
     */
    protected function getStockByCategory()
    {
        return (new \yii\db\Query())
            ->select([
                'part_category.id',
                'part_category.name_th',
                'COUNT(part.id) as part_count',
                'SUM(part.stock_quantity) as total_stock',
                'SUM(part.stock_quantity * part.cost_price) as total_value'
            ])
            ->from('part')
            ->innerJoin('part_category', 'part_category.id = part.category_id')
            ->where(['part.is_active' => 1])
            ->groupBy('part.category_id')
            ->orderBy(['total_value' => SORT_DESC])
            ->all();
    }

    /**
     * สต็อกตามประเภท
     */
    protected function getStockByType()
    {
        return (new \yii\db\Query())
            ->select([
                'part_type',
                'COUNT(*) as part_count',
                'SUM(stock_quantity) as total_stock',
                'SUM(stock_quantity * cost_price) as total_value'
            ])
            ->from('part')
            ->where(['is_active' => 1])
            ->groupBy('part_type')
            ->all();
    }

    /**
     * นับสินค้าไม่เคลื่อนไหว
     */
    protected function getDeadStockCount($days)
    {
        $cutoffDate = strtotime("-{$days} days");
        
        // หา part_id ที่มีการขายในช่วง N วันที่ผ่านมา
        $activeParts = (new \yii\db\Query())
            ->select('{{order_item}}.part_id')
            ->distinct()
            ->from('{{order_item}}')
            ->innerJoin('order', '{{%order}}.id = {{order_item}}.order_id')
            ->where(['>=', '{{%order}}.created_at', $cutoffDate])
            ->andWhere(['<>', '{{%order}}.order_status', [Order::STATUS_CANCELLED]])
            ->column();
        
        // นับสินค้าที่ไม่อยู่ในรายการข้างต้น
        $query = Part::find()
            ->andWhere(['is_active' => 1])
            ->andWhere(['>', 'stock_quantity', 0]);
        
        if (!empty($activeParts)) {
            $query->andWhere(['not in', 'id', $activeParts]);
        }
        
        return $query->count();
    }

    /**
     * ABC Analysis
     */
    protected function getABCAnalysis()
    {
        // คำนวณมูลค่าสินค้าคงคลังของแต่ละรายการ
        $parts = (new \yii\db\Query())
            ->select([
                'id',
                'sku',
                'name_th',
                'stock_quantity',
                'cost_price',
                '(stock_quantity * cost_price) as stock_value'
            ])
            ->from('part')
            ->where(['is_active' => 1])
            ->andWhere(['>', 'stock_quantity', 0])
            ->orderBy(['stock_value' => SORT_DESC])
            ->all();
        
        $totalValue = array_sum(array_column($parts, 'stock_value'));
        $runningTotal = 0;
        $analysis = ['A' => 0, 'B' => 0, 'C' => 0];
        
        foreach ($parts as $part) {
            $runningTotal += $part['stock_value'];
            $percentage = ($runningTotal / $totalValue) * 100;
            
            if ($percentage <= 80) {
                $analysis['A']++;
            } elseif ($percentage <= 95) {
                $analysis['B']++;
            } else {
                $analysis['C']++;
            }
        }
        
        return $analysis;
    }

    /**
     * Export รายงานยอดขาย
     */
    public function actionExportSales()
    {
        $dateFrom = Yii::$app->request->post('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->post('date_to', date('Y-m-d'));
        
        $dateFromTs = strtotime($dateFrom);
        $dateToTs = strtotime($dateTo . ' 23:59:59');
        
        $orders = Order::find()
            ->with(['customer', 'items', 'items.part'])
            ->where(['between', 'created_at', $dateFromTs, $dateToTs])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        $filename = "sales_report_{$dateFrom}_to_{$dateTo}.csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // Header
        fputcsv($output, [
            'เลขที่คำสั่งซื้อ',
            'วันที่',
            'ลูกค้า',
            'จำนวนรายการ',
            'ยอดรวม',
            'ส่วนลด',
            'ค่าจัดส่ง',
            'ยอดสุทธิ',
            'สถานะ',
            'การชำระเงิน',
        ]);
        
        foreach ($orders as $order) {
            fputcsv($output, [
                $order->order_number,
                date('Y-m-d H:i', $order->created_at),
                $order->customer ? $order->customer->full_name : '-',
                count($order->items),
                $order->subtotal,
                $order->discount_amount,
                $order->shipping_cost,
                $order->grand_total,
                $order->getStatusLabel(),
                $order->getPaymentStatusLabel(),
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export รายงานสต็อก
     */
    public function actionExportInventory()
    {
        $parts = Part::find()
            ->with(['category', 'supplier'])
            ->andWhere(['is_active' => 1])
            ->orderBy(['category_id' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();
        
        $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // Header
        fputcsv($output, [
            'รหัสสินค้า',
            'ชื่อสินค้า',
            'หมวดหมู่',
            'ประเภท',
            'ราคาทุน',
            'ราคาขาย',
            'สต็อกปัจจุบัน',
            'สต็อกขั้นต่ำ',
            'มูลค่าสต็อก (ทุน)',
            'มูลค่าสต็อก (ขาย)',
            'สถานะ',
            'ผู้จัดจำหน่าย',
        ]);
        
        foreach ($parts as $part) {
            $status = 'ปกติ';
            if ($part->stock_quantity <= 0) {
                $status = 'หมดสต็อก';
            } elseif ($part->stock_quantity <= $part->min_stock_level) {
                $status = 'ใกล้หมด';
            }
            
            fputcsv($output, [
                $part->sku,
                $part->name_th,
                $part->category ? $part->category->name_th : '-',
                $part->part_type === 'new' ? 'ของใหม่' : 'ของมือสอง',
                $part->cost_price,
                $part->selling_price,
                $part->stock_quantity,
                $part->min_stock_level,
                $part->stock_quantity * $part->cost_price,
                $part->stock_quantity * $part->selling_price,
                $status,
                $part->supplier ? $part->supplier->name : '-',
            ]);
        }
        
        fclose($output);
        exit;
    }
}
