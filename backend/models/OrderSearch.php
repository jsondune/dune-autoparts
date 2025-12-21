<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch - Model สำหรับค้นหาและกรองข้อมูลคำสั่งซื้อ
 */
class OrderSearch extends Order
{
    public $search;
    public $date_from;
    public $date_to;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id'], 'integer'],
            [['order_number', 'order_status', 'payment_status', 'shipping_method', 'search', 'date_from', 'date_to'], 'safe'],
            [['grand_total'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()
            ->with(['customer', 'items', 'items.part']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
                'attributes' => [
                    'order_number',
                    'created_at',
                    'grand_total',
                    'order_status',
                    'payment_status',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Exact filters
        $query->andFilterWhere([
            'order.id' => $this->id,
            'order.customer_id' => $this->customer_id,
            'order.order_status' => $this->order_status,
            'order.payment_status' => $this->payment_status,
            'order.shipping_method' => $this->shipping_method,
        ]);

        // Text search
        if (!empty($this->search)) {
            $query->andWhere([
                'or',
                ['like', 'order.order_number', $this->search],
                ['like', 'order.tracking_number', $this->search],
                ['like', 'order.shipping_name', $this->search],
                ['like', 'order.shipping_phone', $this->search],
            ]);
        }

        // Order number search
        $query->andFilterWhere(['like', 'order.order_number', $this->order_number]);

        // Date range
        if (!empty($this->date_from)) {
            $query->andWhere(['>=', 'order.created_at', strtotime($this->date_from)]);
        }
        if (!empty($this->date_to)) {
            $query->andWhere(['<=', 'order.created_at', strtotime($this->date_to . ' 23:59:59')]);
        }

        return $dataProvider;
    }
}
