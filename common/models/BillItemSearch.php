<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TblBillItem;

/**
 * BillItemSearch represents the model behind the search form of `app\models\TblBillItem`.
 */
class BillItemSearch extends TblBillItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_item_id', 'bill_id', 'reference_id', 'quantity'], 'integer'],
            [['item_type', 'description', 'created_at'], 'safe'],
            [['unit_price', 'total_price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = TblBillItem::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bill_item_id' => $this->bill_item_id,
            'bill_id' => $this->bill_id,
            'reference_id' => $this->reference_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'item_type', $this->item_type])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
