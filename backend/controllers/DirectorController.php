<?php

namespace backend\controllers;

use app\models\TblDirector;
use app\models\DirectorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * DirectorController implements the CRUD actions for TblDirector model.
 */
class DirectorController extends Controller
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
     * Lists all TblDirector models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DirectorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblDirector model.
     * @param int $director_id Director ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($director_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($director_id),
        ]);
    }

    /**
     * Creates a new TblDirector model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblDirector();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'director_id' => $model->director_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblDirector model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $director_id Director ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($director_id)
    {
        $model = $this->findModel($director_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'director_id' => $model->director_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblDirector model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $director_id Director ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($director_id)
    {
        $this->findModel($director_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblDirector model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $director_id Director ID
     * @return TblDirector the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($director_id)
    {
        if (($model = TblDirector::findOne(['director_id' => $director_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
