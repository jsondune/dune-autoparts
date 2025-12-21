<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Part;
use common\models\PartCategory;
use common\models\VehicleBrand;
use common\models\Inquiry;

class SiteController extends Controller
{
    /**
     * หน้าแรก
     */
    public function actionIndex()
    {
        // สินค้าแนะนำ (ล่าสุด)
        $featuredParts = Part::find()
            ->where(['is_active' => Part::STATUS_ACTIVE])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(8)
            ->all();
        
        // สินค้าขายดี
        $bestSellers = Part::find()
            ->where(['is_active' => Part::STATUS_ACTIVE])
            ->orderBy(['sold_count' => SORT_DESC])
            ->limit(4)
            ->all();
        
        // หมวดหมู่
        $categories = PartCategory::find()
            ->orderBy(['name_th' => SORT_ASC])
            ->all();
        
        // ยี่ห้อรถ
        $brands = VehicleBrand::find()
            ->orderBy(['name_th' => SORT_ASC])
            ->limit(12)
            ->all();
        
        return $this->render('index', [
            'featuredParts' => $featuredParts,
            'bestSellers' => $bestSellers,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
    
    /**
     * เกี่ยวกับเรา
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    /**
     * ติดต่อเรา
     */
    public function actionContact()
    {
        $model = new Inquiry();
        $model->type = 'contact';
        $model->status = Inquiry::STATUS_NEW;
        
        if ($model->load(Yii::$app->request->post())) {
            // ถ้าล็อกอินอยู่ ใส่ customer_id
            if (!Yii::$app->user->isGuest) {
                $model->customer_id = Yii::$app->user->id;
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'ส่งข้อความเรียบร้อยแล้ว เราจะติดต่อกลับโดยเร็วที่สุด');
                return $this->refresh();
            }
        }
        
        return $this->render('contact', [
            'model' => $model,
        ]);
    }
    
    /**
     * Error page
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }
}
