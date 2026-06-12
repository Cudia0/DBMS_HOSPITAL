<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblAppointment;

/**
 * AppointmentSearch represents the model behind the search form of `app\models\TblAppointment`.
 */
class AppointmentSearch extends TblAppointment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appt_id', 'dr_id', 'patient_id', 'recep_id'], 'integer'],
            [['symptoms_list', 'appointment_date'], 'safe'],
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
        $query = TblAppointment::find();

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
            'appt_id' => $this->appt_id,
            'dr_id' => $this->dr_id,
            'patient_id' => $this->patient_id,
            'recep_id' => $this->recep_id,
            'appointment_date' => $this->appointment_date,
        ]);

        $query->andFilterWhere(['like', 'symptoms_list', $this->symptoms_list]);

        return $dataProvider;
    }
}
