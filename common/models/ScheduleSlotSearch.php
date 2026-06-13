<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TblScheduleSlot;

/**
 * ScheduleSlotSearch represents the model behind the search form of `app\models\TblScheduleSlot`.
 */
class ScheduleSlotSearch extends TblScheduleSlot
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slot_id', 'dr_id', 'is_available'], 'integer'],
            [['slot_date', 'start_time', 'end_time', 'created_at'], 'safe'],
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
        $query = TblScheduleSlot::find();

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
            'slot_id' => $this->slot_id,
            'dr_id' => $this->dr_id,
            'slot_date' => $this->slot_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
