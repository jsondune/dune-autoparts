<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch - โมเดลค้นหาผู้ใช้งาน
 */
class UserSearch extends User
{
    public $search;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['search', 'role', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = User::find()
            ->where(['!=', 'status', User::STATUS_DELETED]);

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
                    'username',
                    'email',
                    'full_name',
                    'role',
                    'status',
                    'created_at',
                    'last_login_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Full-text search
        if ($this->search) {
            $query->andWhere([
                'or',
                ['like', 'username', $this->search],
                ['like', 'email', $this->search],
                ['like', 'full_name', $this->search],
                ['like', 'phone', $this->search],
            ]);
        }

        // Filter by role
        if ($this->role) {
            $query->andWhere(['role' => $this->role]);
        }

        // Filter by status
        if ($this->status !== null && $this->status !== '') {
            $query->andWhere(['status' => $this->status]);
        }

        return $dataProvider;
    }

    /**
     * Get user statistics
     * @return array
     */
    public static function getStats()
    {
        return [
            'total' => User::find()->where(['!=', 'status', User::STATUS_DELETED])->count(),
            'active' => User::find()->where(['status' => User::STATUS_ACTIVE])->count(),
            'inactive' => User::find()->where(['status' => User::STATUS_INACTIVE])->count(),
            'admin' => User::find()->where(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE])->count(),
            'manager' => User::find()->where(['role' => User::ROLE_MANAGER, 'status' => User::STATUS_ACTIVE])->count(),
            'staff' => User::find()->where(['role' => User::ROLE_STAFF, 'status' => User::STATUS_ACTIVE])->count(),
        ];
    }
}
