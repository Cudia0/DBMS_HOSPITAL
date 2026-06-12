<?php

namespace backend\controllers;

use app\models\TblDepartment;
use app\models\DepartmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * DepartmentController implements the CRUD actions for TblDepartment model.
 */
class DepartmentController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all TblDepartment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblDepartment model.
     * @param int $dept_id Dept ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($dept_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($dept_id),
        ]);
    }

    /**
     * Creates a new TblDepartment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblDepartment();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'dept_id' => $model->dept_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblDepartment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $dept_id Dept ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($dept_id)
    {
        $model = $this->findModel($dept_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dept_id' => $model->dept_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblDepartment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $dept_id Dept ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($dept_id)
    {
        $this->findModel($dept_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblDepartment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $dept_id Dept ID
     * @return TblDepartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($dept_id)
    {
        if (($model = TblDepartment::findOne(['dept_id' => $dept_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
