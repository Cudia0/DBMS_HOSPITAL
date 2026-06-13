<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TblLabTest;

/**
 * LabTestSearch represents the model behind the search form of `app\models\TblLabTest`.
 */
class LabTestSearch extends TblLabTest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'appt_id', 'is_abnormal'], 'integer'],
            [['test_name', 'test_category', 'status', 'results', 'ordered_date', 'results_date', 'notes', 'created_at', 'updated_at'], 'safe'],
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
        $query = TblLabTest::find();

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
            'test_id' => $this->test_id,
            'appt_id' => $this->appt_id,
            
            'is_abnormal' => $this->is_abnormal,
            'ordered_date' => $this->ordered_date,
            'results_date' => $this->results_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'test_name', $this->test_name])
            ->andFilterWhere(['like', 'test_category', $this->test_category])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'results', $this->results])
            ->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
