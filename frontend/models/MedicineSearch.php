<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblMedicine;

/**
 * MedicineSearch represents the model behind the search form of `app\models\TblMedicine`.
 */
class MedicineSearch extends TblMedicine
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['med_id'], 'integer'],
            [['med_name'], 'safe'],
            [['med_price'], 'number'],
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
        $query = TblMedicine::find();

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
            'med_id' => $this->med_id,
            'med_price' => $this->med_price,
        ]);

        $query->andFilterWhere(['like', 'med_name', $this->med_name]);

        return $dataProvider;
    }
}
