<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblPatient;

/**
 * PatientSearch represents the model behind the search form of `app\models\TblPatient`.
 */
class PatientSearch extends TblPatient
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_id', 'age', 'recep_id'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'date_of_birth', 'phone_num', 'country_code', 'email'], 'safe'],
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
        $query = TblPatient::find();

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
            'patient_id' => $this->patient_id,
            'age' => $this->age,
            'date_of_birth' => $this->date_of_birth,
            'recep_id' => $this->recep_id,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'phone_num', $this->phone_num])
            ->andFilterWhere(['like', 'country_code', $this->country_code])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
