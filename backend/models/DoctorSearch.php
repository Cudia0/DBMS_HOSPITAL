<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblDoctor;

/**
 * DoctorSearch represents the model behind the search form of `app\models\TblDoctor`.
 */
class DoctorSearch extends TblDoctor
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dr_id', 'dept_id'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'specialization', 'certification'], 'safe'],
            [['dr_fee'], 'number'],
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
        $query = TblDoctor::find();

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
            'dr_id' => $this->dr_id,
            'dr_fee' => $this->dr_fee,
            'dept_id' => $this->dept_id,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'specialization', $this->specialization])
            ->andFilterWhere(['like', 'certification', $this->certification]);

        return $dataProvider;
    }
}
