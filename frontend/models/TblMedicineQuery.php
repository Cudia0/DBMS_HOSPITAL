<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TblMedicine]].
 *
 * @see TblMedicine
 */
class TblMedicineQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblMedicine[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblMedicine|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
