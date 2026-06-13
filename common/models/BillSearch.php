<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TblBill;

/**
 * BillSearch represents the model behind the search form of `app\models\TblBill`.
 */
class BillSearch extends TblBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'appt_id'], 'integer'],
            [['payment_status', 'payment_method', 'bill_date', 'created_at', 'updated_at'], 'safe'],
            [['dr_fee', 'totalm_price', 'total_amount'], 'number'],
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
        $query = TblBill::find();

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
            'bill_id' => $this->bill_id,
            'appt_id' => $this->appt_id,
            'dr_fee' => $this->dr_fee,
            'totalm_price' => $this->totalm_price,
            'total_amount' => $this->total_amount,
            'bill_date' => $this->bill_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'payment_status', $this->payment_status])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method]);

        return $dataProvider;
    }
}
