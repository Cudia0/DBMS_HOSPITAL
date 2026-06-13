<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[TblDepartment]].
 *
 * @see TblDepartment
 */
class TblDepartmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblDepartment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblDepartment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
