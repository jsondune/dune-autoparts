<?php
/**
 * InquirySearch - ค้นหาสอบถาม/แชท
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Inquiry;

class InquirySearch extends Model
{
    public $search;
    public $channel;
    public $status;
    public $assigned_to;
    public $date_from;
    public $date_to;

    public function rules()
    {
        return [
            [['search', 'channel', 'status'], 'string'],
            [['assigned_to'], 'integer'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function search($params)
    {
        $query = Inquiry::find()->with(['customer', 'assignedUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
                'attributes' => [
                    'inquiry_number',
                    'channel',
                    'status',
                    'created_at',
                    'updated_at',
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
                ['like', 'inquiry.inquiry_number', $this->search],
                ['like', 'inquiry.subject', $this->search],
                ['like', 'inquiry.customer_name', $this->search],
                ['like', 'inquiry.customer_phone', $this->search],
            ]);
        }

        // Channel filter
        $query->andFilterWhere(['channel' => $this->channel]);

        // Status filter
        $query->andFilterWhere(['status' => $this->status]);

        // Assigned user filter
        $query->andFilterWhere(['assigned_to' => $this->assigned_to]);

        // Date range filter
        if ($this->date_from) {
            $query->andWhere(['>=', 'DATE(inquiry.created_at)', $this->date_from]);
        }
        if ($this->date_to) {
            $query->andWhere(['<=', 'DATE(inquiry.created_at)', $this->date_to]);
        }

        return $dataProvider;
    }
}
