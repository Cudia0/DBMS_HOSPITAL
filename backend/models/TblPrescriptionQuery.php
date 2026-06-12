<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TblPrescription]].
 *
 * @see TblPrescription
 */
class TblPrescriptionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblPrescription[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblPrescription|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
