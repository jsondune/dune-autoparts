<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\Part;

class CartController extends Controller
{
    /**
     * ดูตะกร้าสินค้า
     */
    public function actionIndex()
    {
        $cart = Yii::$app->session->get('cart', []);
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $partId => $qty) {
            $part = Part::findOne($partId);
            if ($part) {
                $subtotal = $part->sell_price * $qty;
                $cartItems[] = [
                    'part' => $part,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }
        
        return $this->render('index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }
    
    /**
     * เพิ่มสินค้าลงตะกร้า
     */
    public function actionAdd()
    {
        $partId = Yii::$app->request->post('part_id');
        $qty = Yii::$app->request->post('qty', 1);
        
        $part = Part::findOne(['id' => $partId, 'status' => Part::STATUS_ACTIVE]);
        
        if (!$part) {
            Yii::$app->session->setFlash('error', 'ไม่พบสินค้าที่ต้องการ');
            return $this->redirect(Yii::$app->request->referrer ?: ['/site/index']);
        }
        
        // Check stock
        if ($part->stock_qty < $qty) {
            Yii::$app->session->setFlash('error', 'สินค้าในคลังไม่เพียงพอ');
            return $this->redirect(Yii::$app->request->referrer ?: ['/part/view', 'id' => $partId]);
        }
        
        $cart = Yii::$app->session->get('cart', []);
        
        if (isset($cart[$partId])) {
            $cart[$partId] += $qty;
        } else {
            $cart[$partId] = $qty;
        }
        
        Yii::$app->session->set('cart', $cart);
        Yii::$app->session->setFlash('success', 'เพิ่ม "' . $part->name_th . '" ลงตะกร้าเรียบร้อยแล้ว');
        
        // AJAX request
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => true,
                'cartCount' => count($cart),
                'message' => 'เพิ่มสินค้าลงตะกร้าเรียบร้อยแล้ว',
            ];
        }
        
        return $this->redirect(Yii::$app->request->referrer ?: ['/cart/index']);
    }
    
    /**
     * อัปเดตจำนวนสินค้า
     */
    public function actionUpdate()
    {
        $partId = Yii::$app->request->post('part_id');
        $qty = (int)Yii::$app->request->post('qty', 1);
        
        $cart = Yii::$app->session->get('cart', []);
        
        if ($qty <= 0) {
            unset($cart[$partId]);
            Yii::$app->session->setFlash('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
        } else {
            $part = Part::findOne($partId);
            if ($part && $part->stock_qty >= $qty) {
                $cart[$partId] = $qty;
                Yii::$app->session->setFlash('success', 'อัปเดตจำนวนสินค้าเรียบร้อยแล้ว');
            } else {
                Yii::$app->session->setFlash('error', 'สินค้าในคลังไม่เพียงพอ');
            }
        }
        
        Yii::$app->session->set('cart', $cart);
        
        return $this->redirect(['/cart/index']);
    }
    
    /**
     * ลบสินค้าออกจากตะกร้า
     */
    public function actionRemove($id)
    {
        $cart = Yii::$app->session->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Yii::$app->session->set('cart', $cart);
            Yii::$app->session->setFlash('success', 'ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
        }
        
        return $this->redirect(['/cart/index']);
    }
    
    /**
     * ล้างตะกร้า
     */
    public function actionClear()
    {
        Yii::$app->session->remove('cart');
        Yii::$app->session->setFlash('success', 'ล้างตะกร้าสินค้าเรียบร้อยแล้ว');
        
        return $this->redirect(['/cart/index']);
    }
    
    /**
     * ดึงจำนวนสินค้าในตะกร้า (AJAX)
     */
    public function actionCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $cart = Yii::$app->session->get('cart', []);
        
        return [
            'count' => count($cart),
        ];
    }
}
