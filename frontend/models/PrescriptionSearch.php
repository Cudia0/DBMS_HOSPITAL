<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblPrescription;

/**
 * PrescriptionSearch represents the model behind the search form of `app\models\TblPrescription`.
 */
class PrescriptionSearch extends TblPrescription
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prescription_id', 'appt_id', 'med_id', 'dr_id', 'qty'], 'integer'],
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
        $query = TblPrescription::find();

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
            'prescription_id' => $this->prescription_id,
            'appt_id' => $this->appt_id,
            'med_id' => $this->med_id,
            'dr_id' => $this->dr_id,
            'qty' => $this->qty,
        ]);

        return $dataProvider;
    }
}
