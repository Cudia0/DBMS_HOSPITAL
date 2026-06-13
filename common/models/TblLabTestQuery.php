<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[TblLabTest]].
 *
 * @see TblLabTest
 */
class TblLabTestQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblLabTest[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblLabTest|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
