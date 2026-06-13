<?php

namespace backend\controllers;

use common\models\TblPatient;
use common\models\PatientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * PatientController - Director & Receptionist can manage, Doctor can view
 */
class PatientController extends Controller
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
                                $user = Yii::$app->user->identity;
                                // All staff can view patients
                                if (in_array($action->id, ['index', 'view'])) {
                                    return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                                }
                                // Only Director & Receptionist can create/update/delete
                                return $user->isDirector() || $user->isReceptionist();
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
        $searchModel = new PatientSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($patient_id)
    {
        return $this->render('view', ['model' => $this->findModel($patient_id)]);
    }

    public function actionCreate()
    {
        $model = new TblPatient();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'patient_id' => $model->patient_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($patient_id)
    {
        $model = $this->findModel($patient_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'patient_id' => $model->patient_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($patient_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete patients.');
            return $this->redirect(['index']);
        }
        $this->findModel($patient_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($patient_id)
    {
        if (($model = TblPatient::findOne(['patient_id' => $patient_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}