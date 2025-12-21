<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Customer;
use common\models\CustomerVehicle;
use common\models\Order;
use common\models\Inquiry;

/**
 * CustomerController - จัดการข้อมูลลูกค้า
 */
class CustomerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * รายการลูกค้าทั้งหมด
     */
    public function actionIndex()
    {
        $searchModel = new \backend\models\CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Stats
        $stats = [
            'total' => Customer::find()->where(['is_active' => 1])->count(),
            'retail' => Customer::find()->where(['is_active' => 1, 'customer_type' => 'retail'])->count(),
            'wholesale' => Customer::find()->where(['is_active' => 1, 'customer_type' => 'wholesale'])->count(),
            'garage' => Customer::find()->where(['is_active' => 1, 'customer_type' => 'garage'])->count(),
            'new_this_month' => Customer::find()->where(['>=', 'created_at', strtotime('first day of this month')])->count(),
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stats' => $stats,
        ]);
    }

    /**
     * แสดงรายละเอียดลูกค้า
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Customer's vehicles
        $vehicles = CustomerVehicle::find()
            ->where(['customer_id' => $id])
            ->with(['vehicleModel', 'vehicleModel.brand'])
            ->all();
        
        // Order history
        $orders = Order::find()
            ->where(['customer_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();
        
        // Inquiry history
        $inquiries = Inquiry::find()
            ->where(['customer_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();
        
        // Purchase stats
        $purchaseStats = [
            'total_orders' => Order::find()->where(['customer_id' => $id])->andWhere(['not', ['status' => 'cancelled']])->count(),
            'total_spent' => Order::find()->where(['customer_id' => $id, 'status' => 'delivered'])->sum('grand_total') ?? 0,
            'avg_order' => Order::find()->where(['customer_id' => $id, 'status' => 'delivered'])->average('grand_total') ?? 0,
            'last_order' => Order::find()->where(['customer_id' => $id])->orderBy(['created_at' => SORT_DESC])->one(),
        ];

        return $this->render('view', [
            'model' => $model,
            'vehicles' => $vehicles,
            'orders' => $orders,
            'inquiries' => $inquiries,
            'purchaseStats' => $purchaseStats,
        ]);
    }

    /**
     * เพิ่มลูกค้าใหม่
     */
    public function actionCreate()
    {
        $model = new Customer();
        $model->is_active = 1;
        $model->customer_type = 'retail';

        if ($model->load(Yii::$app->request->post())) {
            // Generate customer code
            $model->customer_code = Customer::generateCustomerCode($model->customer_type);
            
            if ($model->save()) {
                // Save vehicles if provided
                $this->saveCustomerVehicles($model->id, Yii::$app->request->post('CustomerVehicle', []));
                
                Yii::$app->session->setFlash('success', 'เพิ่มลูกค้าใหม่เรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * แก้ไขข้อมูลลูกค้า
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // Update vehicles
                $this->saveCustomerVehicles($model->id, Yii::$app->request->post('CustomerVehicle', []), true);
                
                Yii::$app->session->setFlash('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * ลบลูกค้า (Soft delete)
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_active = 0;
        $model->save(false);
        
        Yii::$app->session->setFlash('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * เพิ่มรถของลูกค้า
     */
    public function actionAddVehicle($id)
    {
        $customer = $this->findModel($id);
        $vehicle = new CustomerVehicle();
        $vehicle->customer_id = $id;

        if ($vehicle->load(Yii::$app->request->post()) && $vehicle->save()) {
            Yii::$app->session->setFlash('success', 'เพิ่มข้อมูลรถเรียบร้อยแล้ว');
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('add-vehicle', [
            'customer' => $customer,
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * ลบรถของลูกค้า
     */
    public function actionDeleteVehicle($id, $vehicle_id)
    {
        CustomerVehicle::deleteAll(['id' => $vehicle_id, 'customer_id' => $id]);
        
        Yii::$app->session->setFlash('success', 'ลบข้อมูลรถเรียบร้อยแล้ว');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * ปรับวงเงินเครดิต
     */
    public function actionUpdateCredit($id)
    {
        $model = $this->findModel($id);
        
        $creditLimit = Yii::$app->request->post('credit_limit');
        if ($creditLimit !== null) {
            $model->credit_limit = $creditLimit;
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'ปรับวงเงินเครดิตเรียบร้อยแล้ว');
            }
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Export to Excel
     */
    public function actionExport()
    {
        $customers = Customer::find()
            ->where(['is_active' => 1])
            ->orderBy(['customer_code' => SORT_ASC])
            ->all();
        
        $filename = 'customers_export_' . date('Ymd_His') . '.csv';
        $headers = ['รหัส', 'ชื่อ', 'นามสกุล', 'บริษัท', 'ประเภท', 'โทร', 'อีเมล', 'Line ID', 'ยอดซื้อรวม', 'จำนวนออเดอร์'];
        
        $content = "\xEF\xBB\xBF"; // UTF-8 BOM
        $content .= implode(',', $headers) . "\n";
        
        $typeLabels = [
            'retail' => 'รายย่อย',
            'wholesale' => 'ขายส่ง',
            'garage' => 'อู่/ร้าน',
        ];
        
        foreach ($customers as $customer) {
            $row = [
                $customer->customer_code,
                '"' . $customer->full_name . '"',
                '"' . ($customer->company_name ?: '-') . '"',
                $typeLabels[$customer->customer_type] ?? $customer->customer_type,
                $customer->phone,
                $customer->email ?: '-',
                $customer->line_id ?: '-',
                number_format($customer->total_purchases, 2),
                $customer->total_orders,
            ];
            $content .= implode(',', $row) . "\n";
        }
        
        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'text/csv',
            'inline' => false,
        ]);
    }

    /**
     * AJAX: Search customers
     */
    public function actionSearch($q)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $customers = Customer::find()
            ->where(['is_active' => 1])
            ->andWhere(['or',
                ['like', 'customer_code', $q],
                ['like', 'first_name', $q],
                ['like', 'last_name', $q],
                ['like', 'company_name', $q],
                ['like', 'phone', $q],
                ['like', 'line_id', $q],
                ['like', 'facebok', $q],                
            ])
            ->limit(20)
            ->all();
        
        $result = [];
        foreach ($customers as $customer) {
            $result[] = [
                'id' => $customer->id,
                'code' => $customer->customer_code,
                'name' => $customer->getDisplayName(),
                'phone' => $customer->phone,
                'type' => $customer->customer_type,
            ];
        }
        
        return $result;
    }

    /**
     * Save customer vehicles
     */
    protected function saveCustomerVehicles($customerId, $vehicles, $deleteExisting = false)
    {
        if ($deleteExisting) {
            CustomerVehicle::deleteAll(['customer_id' => $customerId]);
        }
        
        foreach ($vehicles as $vehicleData) {
            if (empty($vehicleData['vehicle_model_id'])) continue;
            
            $vehicle = new CustomerVehicle();
            $vehicle->customer_id = $customerId;
            $vehicle->vehicle_model_id = $vehicleData['vehicle_model_id'];
            $vehicle->year = $vehicleData['year'] ?? null;
            $vehicle->license_plate = $vehicleData['license_plate'] ?? null;
            $vehicle->vin = $vehicleData['vin'] ?? null;
            $vehicle->engine_code = $vehicleData['engine_code'] ?? null;
            $vehicle->color = $vehicleData['color'] ?? null;
            $vehicle->notes = $vehicleData['notes'] ?? null;
            $vehicle->is_primary = $vehicleData['is_primary'] ?? 0;
            $vehicle->save();
        }
    }

    /**
     * Find model by ID
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('ไม่พบข้อมูลลูกค้าที่ต้องการ');
    }
}
