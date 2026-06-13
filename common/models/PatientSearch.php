<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class PatientSearch extends TblPatient
{
    public function rules()
    {
        return [
            [['patient_id'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'sex', 'date_of_birth', 'phone_num', 'country_code', 'email', 'address'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TblPatient::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'patient_id' => $this->patient_id,
            'date_of_birth' => $this->date_of_birth,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'middle_name', $this->middle_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'phone_num', $this->phone_num])
            ->andFilterWhere(['like', 'country_code', $this->country_code])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}