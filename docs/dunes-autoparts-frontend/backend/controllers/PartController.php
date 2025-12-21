<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Part;
use common\models\PartCategory;
use common\models\VehicleBrand;
use common\models\VehicleModel;
use common\models\PartVehicle;
use common\models\StockMovement;

/**
 * PartController - จัดการสินค้า/อะไหล่
 */
class PartController extends Controller
{
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
                    'delete' => ['POST'],
                    'update-stock' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * รายการสินค้าทั้งหมด พร้อม Filter
     */
    public function actionIndex()
    {
        $searchModel = new \backend\models\PartSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Quick stats
        $stats = [
            'total' => Part::find()->where(['is_active' => 1])->count(),
            'new_parts' => Part::find()->where(['is_active' => 1, 'part_type' => 'new'])->count(),
            'used_parts' => Part::find()->where(['is_active' => 1, 'part_type' => 'used_imported'])->count(),
            'low_stock' => Part::find()->where(['is_active' => 1])->andWhere('stock_quantity <= min_stock_level')->count(),
            'out_of_stock' => Part::find()->where(['is_active' => 1, 'stock_quantity' => 0])->count(),
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'stats' => $stats,
            'categories' => PartCategory::getHierarchicalList(),
        ]);
    }

    /**
     * แสดงรายละเอียดสินค้า
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Stock movements history
        $stockMovements = StockMovement::find()
            ->where(['part_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();
        
        // Compatible vehicles
        $compatibleVehicles = PartVehicle::find()
            ->where(['part_id' => $id])
            ->with(['vehicleModel', 'vehicleModel.brand'])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'stockMovements' => $stockMovements,
            'compatibleVehicles' => $compatibleVehicles,
        ]);
    }

    /**
     * เพิ่มสินค้าใหม่
     */
    public function actionCreate()
    {
        $model = new Part();
        $model->is_active = 1;
        $model->stock_quantity = 0;
        $model->min_stock_level = 5;

        if ($model->load(Yii::$app->request->post())) {
            // Generate SKU
            if (empty($model->sku)) {
                $model->sku = Part::generateSku($model->category_id, $model->part_type);
            }
            
            // Handle image uploads
            $this->handleImageUploads($model);
            
            // Handle specifications (JSON)
            $specs = Yii::$app->request->post('specifications', []);
            if (!empty($specs)) {
                $model->specifications = json_encode($specs, JSON_UNESCAPED_UNICODE);
            }
            
            // Handle tags
            $tags = Yii::$app->request->post('tags', '');
            if (!empty($tags)) {
                $model->tags = json_encode(array_map('trim', explode(',', $tags)), JSON_UNESCAPED_UNICODE);
            }
            
            if ($model->save()) {
                // Save compatible vehicles
                $this->saveCompatibleVehicles($model->id, Yii::$app->request->post('compatible_vehicles', []));
                
                // Add initial stock if provided
                $initialStock = (int)Yii::$app->request->post('initial_stock', 0);
                if ($initialStock > 0) {
                    $model->updateStock($initialStock, 'in', 'สต็อกเริ่มต้น', null, 
                        Yii::$app->request->post('initial_cost_per_unit'));
                }
                
                Yii::$app->session->setFlash('success', 'เพิ่มสินค้าใหม่เรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'categories' => PartCategory::getHierarchicalList(),
            'brands' => VehicleBrand::find()->where(['is_active' => 1])->all(),
        ]);
    }

    /**
     * แก้ไขสินค้า
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImages = $model->images;

        if ($model->load(Yii::$app->request->post())) {
            // Handle image uploads
            $this->handleImageUploads($model, $oldImages);
            
            // Handle specifications (JSON)
            $specs = Yii::$app->request->post('specifications', []);
            if (!empty($specs)) {
                $model->specifications = json_encode($specs, JSON_UNESCAPED_UNICODE);
            }
            
            // Handle tags
            $tags = Yii::$app->request->post('tags', '');
            if (!empty($tags)) {
                $model->tags = json_encode(array_map('trim', explode(',', $tags)), JSON_UNESCAPED_UNICODE);
            }
            
            if ($model->save()) {
                // Update compatible vehicles
                $this->saveCompatibleVehicles($model->id, Yii::$app->request->post('compatible_vehicles', []));
                
                Yii::$app->session->setFlash('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // Get current compatible vehicles
        $compatibleVehicleIds = PartVehicle::find()
            ->select('vehicle_model_id')
            ->where(['part_id' => $id])
            ->column();

        return $this->render('update', [
            'model' => $model,
            'categories' => PartCategory::getHierarchicalList(),
            'brands' => VehicleBrand::find()->where(['is_active' => 1])->all(),
            'compatibleVehicleIds' => $compatibleVehicleIds,
        ]);
    }

    /**
     * ลบสินค้า (Soft delete)
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_active = 0;
        $model->save(false);
        
        Yii::$app->session->setFlash('success', 'ลบสินค้าเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * ปรับปรุงสต็อก
     */
    public function actionUpdateStock($id)
    {
        $model = $this->findModel($id);
        
        $quantity = (int)Yii::$app->request->post('quantity', 0);
        $type = Yii::$app->request->post('type', 'in');
        $reason = Yii::$app->request->post('reason', '');
        $costPerUnit = Yii::$app->request->post('cost_per_unit');
        $referenceNo = Yii::$app->request->post('reference_no');
        
        if ($quantity <= 0) {
            Yii::$app->session->setFlash('error', 'กรุณาระบุจำนวนที่ถูกต้อง');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if ($model->updateStock($quantity, $type, $reason, $referenceNo, $costPerUnit)) {
            $typeText = [
                'in' => 'รับเข้า',
                'out' => 'เบิกออก', 
                'adjustment' => 'ปรับปรุง',
                'return' => 'รับคืน',
                'damaged' => 'เสียหาย',
            ];
            Yii::$app->session->setFlash('success', $typeText[$type] . "สต็อก {$quantity} ชิ้น เรียบร้อยแล้ว");
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถปรับปรุงสต็อกได้');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * รายงานสินค้าใกล้หมด
     */
    public function actionLowStock()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Part::find()
                ->where(['is_active' => 1])
                ->andWhere('stock_quantity <= min_stock_level')
                ->orderBy(['stock_quantity' => SORT_ASC]),
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('low-stock', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * รายงานประวัติสต็อก
     */
    public function actionStockHistory($id = null)
    {
        $query = StockMovement::find()
            ->with(['part', 'user'])
            ->orderBy(['created_at' => SORT_DESC]);
        
        if ($id) {
            $query->where(['part_id' => $id]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('stock-history', [
            'dataProvider' => $dataProvider,
            'partId' => $id,
        ]);
    }

    /**
     * AJAX: Get vehicle models by brand
     */
    public function actionGetModels($brand_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $models = VehicleModel::find()
            ->where(['brand_id' => $brand_id, 'is_active' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        
        $result = [];
        foreach ($models as $model) {
            $result[] = [
                'id' => $model->id,
                'name' => $model->name_th . ' ' . $model->generation . ' (' . $model->year_start . '-' . ($model->year_end ?: 'ปัจจุบัน') . ')',
            ];
        }
        
        return $result;
    }

    /**
     * Export to Excel
     */
    public function actionExport()
    {
        $parts = Part::find()
            ->where(['is_active' => 1])
            ->with(['category', 'supplier'])
            ->orderBy(['sku' => SORT_ASC])
            ->all();
        
        // Generate CSV
        $filename = 'parts_export_' . date('Ymd_His') . '.csv';
        $headers = ['SKU', 'ชื่อสินค้า', 'หมวดหมู่', 'ประเภท', 'เกรด', 'ราคาทุน', 'ราคาขาย', 'ส่วนลด', 'สต็อก', 'Min Stock', 'สถานะ'];
        
        $content = implode(',', $headers) . "\n";
        foreach ($parts as $part) {
            $row = [
                $part->sku,
                '"' . str_replace('"', '""', $part->name_th) . '"',
                $part->category ? $part->category->name_th : '',
                $part->part_type == 'new' ? 'ของใหม่' : 'มือสองนำเข้า',
                $part->condition_grade ?: '-',
                $part->cost_price,
                $part->selling_price,
                $part->discount_price ?: '-',
                $part->stock_quantity,
                $part->min_stock_level,
                $part->stock_quantity > $part->min_stock_level ? 'ปกติ' : ($part->stock_quantity == 0 ? 'หมด' : 'ใกล้หมด'),
            ];
            $content .= implode(',', $row) . "\n";
        }
        
        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'text/csv',
            'inline' => false,
        ]);
    }

    /**
     * Handle image uploads
     */
    protected function handleImageUploads($model, $oldImages = null)
    {
        $uploadPath = Yii::getAlias('@webroot/uploads/parts/');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        // Main image
        $mainImage = UploadedFile::getInstance($model, 'main_image');
        if ($mainImage) {
            $filename = 'part_' . time() . '_main.' . $mainImage->extension;
            if ($mainImage->saveAs($uploadPath . $filename)) {
                $model->main_image = $filename;
            }
        }
        
        // Multiple images
        $images = UploadedFile::getInstances($model, 'images');
        if (!empty($images)) {
            $imageList = [];
            if ($oldImages) {
                $imageList = is_array($oldImages) ? $oldImages : (json_decode($oldImages, true) ?: []);
            }
            
            foreach ($images as $index => $image) {
                $filename = 'part_' . time() . '_' . $index . '.' . $image->extension;
                if ($image->saveAs($uploadPath . $filename)) {
                    $imageList[] = $filename;
                }
            }
            $model->images = json_encode($imageList);
        }
    }

    /**
     * Save compatible vehicles
     */
    protected function saveCompatibleVehicles($partId, $vehicleModelIds)
    {
        // Delete existing
        PartVehicle::deleteAll(['part_id' => $partId]);
        
        // Insert new
        foreach ($vehicleModelIds as $modelId) {
            $pv = new PartVehicle();
            $pv->part_id = $partId;
            $pv->vehicle_model_id = $modelId;
            $pv->save();
        }
    }

    /**
     * Find model by ID
     */
    protected function findModel($id)
    {
        if (($model = Part::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('ไม่พบสินค้าที่ต้องการ');
    }
}
