<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Part;

/**
 * PartSearch - Model สำหรับค้นหาและกรองข้อมูลสินค้า
 */
class PartSearch extends Part
{
    public $search;
    public $brand_id;
    public $stock_status; // 'all', 'in_stock', 'low_stock', 'out_of_stock'
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'supplier_id', 'brand_id'], 'integer'],
            [['sku', 'oem_number', 'name_en', 'name_th', 'part_type', 'condition_grade', 'search', 'stock_status'], 'safe'],
            [['is_active', 'is_featured'], 'boolean'],
            [['selling_price', 'cost_price'], 'number'],
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
        $query = Part::find()
            ->with(['category', 'supplier']);

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
                    'sku',
                    'name_th',
                    'name_en',
                    'part_type',
                    'selling_price',
                    'stock_quantity',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Default to active only
        if ($this->is_active === null || $this->is_active === '') {
            $query->andWhere(['is_active' => 1]);
        } else {
            $query->andFilterWhere(['is_active' => $this->is_active]);
        }

        // Exact filters
        $query->andFilterWhere([
            'part.id' => $this->id,
            'part.category_id' => $this->category_id,
            'part.supplier_id' => $this->supplier_id,
            'part.part_type' => $this->part_type,
            'part.condition_grade' => $this->condition_grade,
            'part.is_featured' => $this->is_featured,
        ]);

        // Text search
        if (!empty($this->search)) {
            $query->andWhere([
                'or',
                ['like', 'part.sku', $this->search],
                ['like', 'part.oem_number', $this->search],
                ['like', 'part.name_en', $this->search],
                ['like', 'part.name_th', $this->search],
                ['like', 'part.description', $this->search],
            ]);
        }

        // SKU search
        $query->andFilterWhere(['like', 'part.sku', $this->sku]);
        $query->andFilterWhere(['like', 'part.oem_number', $this->oem_number]);
        $query->andFilterWhere(['like', 'part.name_th', $this->name_th]);
        $query->andFilterWhere(['like', 'part.name_en', $this->name_en]);

        // Stock status filter
        if (!empty($this->stock_status)) {
            switch ($this->stock_status) {
                case 'in_stock':
                    $query->andWhere('stock_quantity > min_stock_level');
                    break;
                case 'low_stock':
                    $query->andWhere('stock_quantity <= min_stock_level AND stock_quantity > 0');
                    break;
                case 'out_of_stock':
                    $query->andWhere(['stock_quantity' => 0]);
                    break;
            }
        }

        // Brand filter (via compatible vehicles)
        if (!empty($this->brand_id)) {
            $query->innerJoin('part_vehicle pv', 'pv.part_id = part.id')
                  ->innerJoin('vehicle_model vm', 'vm.id = pv.vehicle_model_id')
                  ->andWhere(['vm.brand_id' => $this->brand_id])
                  ->distinct();
        }

        return $dataProvider;
    }
}
