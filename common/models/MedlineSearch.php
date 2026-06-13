<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TblMedline;

/**
 * MedlineSearch represents the model behind the search form of `app\models\TblMedline`.
 */
class MedlineSearch extends TblMedline
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['medline_id', 'prescription_id', 'med_id', 'qty'], 'integer'],
            [['dosage_per_intake', 'frequency', 'created_at'], 'safe'],
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
        $query = TblMedline::find();

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
            'medline_id' => $this->medline_id,
            'prescription_id' => $this->prescription_id,
            'med_id' => $this->med_id,
            'qty' => $this->qty,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'dosage_per_intake', $this->dosage_per_intake])
            ->andFilterWhere(['like', 'frequency', $this->frequency]);

        return $dataProvider;
    }
}
