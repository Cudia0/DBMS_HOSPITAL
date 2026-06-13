<?php

namespace backend\controllers;

use common\models\TblDepartment;
use common\models\DepartmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * DepartmentController - Only Director can access
 */
class DepartmentController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isDirector();
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new DepartmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($dept_id)
    {
        return $this->render('view', ['model' => $this->findModel($dept_id)]);
    }

    public function actionCreate()
    {
        $model = new TblDepartment();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dept_id' => $model->dept_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($dept_id)
    {
        $model = $this->findModel($dept_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dept_id' => $model->dept_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($dept_id)
    {
        $this->findModel($dept_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($dept_id)
    {
        if (($model = TblDepartment::findOne(['dept_id' => $dept_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}