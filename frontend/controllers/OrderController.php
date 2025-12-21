<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use common\models\Part;
use common\models\Order;
use common\models\OrderItem;
use common\models\Customer;

class OrderController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['history', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * Checkout - สั่งซื้อสินค้า
     */
    public function actionCheckout()
    {
        $cart = Yii::$app->session->get('cart', []);
        
        if (empty($cart)) {
            Yii::$app->session->setFlash('error', 'ตะกร้าสินค้าว่างเปล่า');
            return $this->redirect(['/cart/index']);
        }
        
        // เตรียมข้อมูลตะกร้า
        $cartItems = [];
        $subtotal = 0;
        
        foreach ($cart as $partId => $qty) {
            $part = Part::findOne($partId);
            if ($part) {
                $itemTotal = $part->sell_price * $qty;
                $cartItems[] = [
                    'part' => $part,
                    'qty' => $qty,
                    'subtotal' => $itemTotal,
                ];
                $subtotal += $itemTotal;
            }
        }
        
        // คำนวณค่าจัดส่ง
        $shippingCost = $subtotal >= Yii::$app->params['freeShippingMinimum'] ? 0 : 100;
        $total = $subtotal + $shippingCost;
        
        // Model สำหรับ form
        $model = new Order();
        $customerModel = new Customer();
        
        // ถ้าล็อกอินอยู่ ใช้ข้อมูลลูกค้า
        if (!Yii::$app->user->isGuest) {
            $customer = Yii::$app->user->identity;
            $model->customer_id = $customer->id;
            $customerModel = $customer;
        }
        
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            
            // ถ้าเป็น guest ต้องสร้าง customer ก่อน
            if (Yii::$app->user->isGuest) {
                $customerModel->load(Yii::$app->request->post());
                $customerModel->type = 'retail';
                $customerModel->status = Customer::STATUS_ACTIVE;
                
                // ตรวจสอบว่ามี email นี้อยู่แล้วหรือไม่
                $existingCustomer = Customer::findOne(['email' => $customerModel->email]);
                if ($existingCustomer) {
                    $model->customer_id = $existingCustomer->id;
                } else {
                    if ($customerModel->save()) {
                        $model->customer_id = $customerModel->id;
                    } else {
                        Yii::$app->session->setFlash('error', 'ไม่สามารถบันทึกข้อมูลลูกค้าได้');
                        return $this->render('checkout', [
                            'cartItems' => $cartItems,
                            'subtotal' => $subtotal,
                            'shippingCost' => $shippingCost,
                            'total' => $total,
                            'model' => $model,
                            'customerModel' => $customerModel,
                        ]);
                    }
                }
            }
            
            // สร้าง Order
            $model->order_number = Order::generateOrderNumber();
            $model->subtotal = $subtotal;
            $model->shipping_cost = $shippingCost;
            $model->discount = 0;
            $model->total_amount = $total;
            $model->status = Order::STATUS_PENDING;
            $model->payment_status = Order::PAYMENT_PENDING;
            $model->order_date = date('Y-m-d H:i:s');
            
            if ($model->save()) {
                // สร้าง OrderItems
                foreach ($cartItems as $item) {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $model->id;
                    $orderItem->part_id = $item['part']->id;
                    $orderItem->quantity = $item['qty'];
                    $orderItem->unit_price = $item['part']->sell_price;
                    $orderItem->subtotal = $item['subtotal'];
                    $orderItem->save();
                    
                    // ลด stock
                    $item['part']->stock_qty -= $item['qty'];
                    $item['part']->sold_count += $item['qty'];
                    $item['part']->save(false);
                }
                
                // ล้างตะกร้า
                Yii::$app->session->remove('cart');
                
                Yii::$app->session->setFlash('success', 'สั่งซื้อสำเร็จ! หมายเลขคำสั่งซื้อ: ' . $model->order_number);
                return $this->redirect(['/order/success', 'id' => $model->id]);
            }
        }
        
        return $this->render('checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'model' => $model,
            'customerModel' => $customerModel,
        ]);
    }
    
    /**
     * หน้าสั่งซื้อสำเร็จ
     */
    public function actionSuccess($id)
    {
        $order = Order::findOne($id);
        
        if (!$order) {
            throw new NotFoundHttpException('ไม่พบคำสั่งซื้อ');
        }
        
        return $this->render('success', [
            'order' => $order,
        ]);
    }
    
    /**
     * ประวัติการสั่งซื้อ
     */
    public function actionHistory()
    {
        $orders = Order::find()
            ->where(['customer_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        return $this->render('history', [
            'orders' => $orders,
        ]);
    }
    
    /**
     * ดูรายละเอียดคำสั่งซื้อ
     */
    public function actionView($id)
    {
        $order = Order::findOne([
            'id' => $id,
            'customer_id' => Yii::$app->user->id,
        ]);
        
        if (!$order) {
            throw new NotFoundHttpException('ไม่พบคำสั่งซื้อ');
        }
        
        return $this->render('view', [
            'order' => $order,
        ]);
    }
    
    /**
     * ติดตามคำสั่งซื้อ (สำหรับ guest)
     */
    public function actionTrack()
    {
        $orderNumber = Yii::$app->request->get('order_number');
        $email = Yii::$app->request->get('email');
        $order = null;
        
        if ($orderNumber && $email) {
            $order = Order::find()
                ->joinWith('customer')
                ->where(['order.order_number' => $orderNumber])
                ->andWhere(['customer.email' => $email])
                ->one();
        }
        
        return $this->render('track', [
            'order' => $order,
            'orderNumber' => $orderNumber,
            'email' => $email,
        ]);
    }
}
