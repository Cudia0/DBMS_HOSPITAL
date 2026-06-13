<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[TblBillItem]].
 *
 * @see TblBillItem
 */
class TblBillItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblBillItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblBillItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
