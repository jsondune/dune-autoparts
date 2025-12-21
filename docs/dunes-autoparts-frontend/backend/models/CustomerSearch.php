<?php
/**
 * CustomerSearch - ค้นหาลูกค้า
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

class CustomerSearch extends Model
{
    public $search;
    public $customer_type;
    public $status;
    public $date_from;
    public $date_to;

    public function rules()
    {
        return [
            [['search', 'customer_type', 'status'], 'string'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params)
    {
        $query = Customer::find();

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
                    'customer_code',
                    'first_name',
                    'last_name',
                    'company_name',
                    'phone',
                    'total_orders',
                    'total_spent',
                    'created_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Text search
        if ($this->search) {
            $query->andFilterWhere([
                'or',
                ['like', 'customer_code', $this->search],
                ['like', 'first_name', $this->search],
                ['like', 'last_name', $this->search],
                ['like', 'company_name', $this->search],
                ['like', 'phone', $this->search],
                ['like', 'email', $this->search],
                ['like', 'line_id', $this->search],
            ]);
        }

        // Customer type filter
        $query->andFilterWhere(['customer_type' => $this->customer_type]);

        // Status filter
        if ($this->status !== null && $this->status !== '') {
            $query->andFilterWhere(['is_active' => $this->status]);
        }

        // Date range filter
        if ($this->date_from) {
            $query->andWhere(['>=', 'DATE(created_at)', $this->date_from]);
        }
        if ($this->date_to) {
            $query->andWhere(['<=', 'DATE(created_at)', $this->date_to]);
        }

        return $dataProvider;
    }
}
