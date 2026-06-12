<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TblReceptionist]].
 *
 * @see TblReceptionist
 */
class TblReceptionistQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblReceptionist[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblReceptionist|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
