<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use common\models\Part;
use common\models\PartCategory;
use common\models\VehicleBrand;
use common\models\VehicleModel;
use common\models\Inquiry;

class PartController extends Controller
{
    /**
     * รายการสินค้าทั้งหมด
     */
    public function actionIndex()
    {
        $query = Part::find()->where(['is_active' => Part::STATUS_ACTIVE]);
        
        // Filters
        $categoryId = Yii::$app->request->get('category');
        $brandId = Yii::$app->request->get('brand');
        $priceMin = Yii::$app->request->get('price_min');
        $priceMax = Yii::$app->request->get('price_max');
        $sort = Yii::$app->request->get('sort', 'newest');
        
        if ($categoryId) {
            $query->andWhere(['category_id' => $categoryId]);
        }
        
        if ($priceMin) {
            $query->andWhere(['>=', 'sell_price', $priceMin]);
        }
        
        if ($priceMax) {
            $query->andWhere(['<=', 'sell_price', $priceMax]);
        }
        
        // Sorting
        switch ($sort) {
            case 'price_low':
                $query->orderBy(['sell_price' => SORT_ASC]);
                break;
            case 'price_high':
                $query->orderBy(['sell_price' => SORT_DESC]);
                break;
            case 'popular':
                $query->orderBy(['sold_count' => SORT_DESC]);
                break;
            case 'name':
                $query->orderBy(['name_th' => SORT_ASC]);
                break;
            default: // newest
                $query->orderBy(['created_at' => SORT_DESC]);
        }
        
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => Yii::$app->params['itemsPerPage'] ?? 12,
        ]);
        
        $parts = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        $categories = PartCategory::find()->orderBy('name_th')->all();
        
        return $this->render('index', [
            'parts' => $parts,
            'pages' => $pages,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'currentSort' => $sort,
        ]);
    }
    
    /**
     * ดูรายละเอียดสินค้า
     */
    public function actionView($id)
    {
        $part = Part::findOne(['id' => $id, 'is_active' => Part::STATUS_ACTIVE]);
        
        if (!$part) {
            throw new NotFoundHttpException('ไม่พบสินค้าที่ต้องการ');
        }
        
        // สินค้าที่เกี่ยวข้อง
        $relatedParts = Part::find()
            ->where(['is_active' => Part::STATUS_ACTIVE])
            ->andWhere(['category_id' => $part->category_id])
            ->andWhere(['!=', 'id', $part->id])
            ->limit(4)
            ->all();
        
        // Model สำหรับสอบถาม
        $inquiryModel = new Inquiry();
        $inquiryModel->type = 'product';
        $inquiryModel->part_id = $part->id;
        
        if ($inquiryModel->load(Yii::$app->request->post())) {
            $inquiryModel->is_active = Inquiry::STATUS_NEW;
            if (!Yii::$app->user->isGuest) {
                $inquiryModel->customer_id = Yii::$app->user->id;
            }
            
            if ($inquiryModel->save()) {
                Yii::$app->session->setFlash('success', 'ส่งคำถามเรียบร้อยแล้ว เราจะติดต่อกลับโดยเร็วที่สุด');
                return $this->refresh();
            }
        }
        
        return $this->render('view', [
            'part' => $part,
            'relatedParts' => $relatedParts,
            'inquiryModel' => $inquiryModel,
        ]);
    }
    
    /**
     * ค้นหาสินค้า
     */
    public function actionSearch()
    {
        $q = Yii::$app->request->get('q', '');
        
        $query = Part::find()
            ->where(['is_active' => Part::STATUS_ACTIVE])
            ->andWhere(['or',
                ['like', 'name_th', $q],
                ['like', 'part_number', $q],
                ['like', 'oem_number', $q],
                ['like', 'description', $q],
            ]);
        
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => Yii::$app->params['itemsPerPage'] ?? 12,
        ]);
        
        $parts = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('search', [
            'parts' => $parts,
            'pages' => $pages,
            'searchQuery' => $q,
        ]);
    }
    
    /**
     * สินค้าตามหมวดหมู่
     */
    public function actionCategory($id)
    {
        $category = PartCategory::findOne($id);
        
        if (!$category) {
            throw new NotFoundHttpException('ไม่พบหมวดหมู่ที่ต้องการ');
        }
        
        $query = Part::find()
            ->where(['is_active' => Part::STATUS_ACTIVE, 'category_id' => $id]);
        
        $sort = Yii::$app->request->get('sort', 'newest');
        
        switch ($sort) {
            case 'price_low':
                $query->orderBy(['sell_price' => SORT_ASC]);
                break;
            case 'price_high':
                $query->orderBy(['sell_price' => SORT_DESC]);
                break;
            case 'popular':
                $query->orderBy(['sold_count' => SORT_DESC]);
                break;
            default:
                $query->orderBy(['created_at' => SORT_DESC]);
        }
        
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => Yii::$app->params['itemsPerPage'] ?? 12,
        ]);
        
        $parts = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('category', [
            'category' => $category,
            'parts' => $parts,
            'pages' => $pages,
            'currentSort' => $sort,
        ]);
    }
    
    /**
     * สินค้าตามยี่ห้อรถ
     */
    public function actionBrand($id)
    {
        $brand = VehicleBrand::findOne($id);
        
        if (!$brand) {
            throw new NotFoundHttpException('ไม่พบยี่ห้อรถที่ต้องการ');
        }
        
        // หาสินค้าที่รองรับยี่ห้อนี้
        $query = Part::find()
            ->where(['is_active' => Part::STATUS_ACTIVE])
            ->joinWith('partVehicles.vehicleModel')
            ->andWhere(['vehicle_model.brand_id' => $id])
            ->distinct();
        
        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => Yii::$app->params['itemsPerPage'] ?? 12,
        ]);
        
        $parts = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['part.created_at' => SORT_DESC])
            ->all();
        
        $models = VehicleModel::find()
            ->where(['brand_id' => $id])
            ->orderBy('name_th')
            ->all();
        
        return $this->render('brand', [
            'brand' => $brand,
            'parts' => $parts,
            'pages' => $pages,
            'models' => $models,
        ]);
    }
}
