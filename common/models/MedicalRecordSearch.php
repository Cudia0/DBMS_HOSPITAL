<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TblMedicalRecord;

/**
 * MedicalRecordSearch represents the model behind the search form of `app\models\TblMedicalRecord`.
 */
class MedicalRecordSearch extends TblMedicalRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['record_id', 'appt_id'], 'integer'],
            [['diagnosis', 'treatment_plan', 'vital_signs', 'notes', 'record_date', 'created_at', 'updated_at'], 'safe'],
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
        $query = TblMedicalRecord::find();

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
            'record_id' => $this->record_id,
            'appt_id' => $this->appt_id,

            'record_date' => $this->record_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'diagnosis', $this->diagnosis])
            ->andFilterWhere(['like', 'treatment_plan', $this->treatment_plan])
            ->andFilterWhere(['like', 'vital_signs', $this->vital_signs])
            ->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
}
