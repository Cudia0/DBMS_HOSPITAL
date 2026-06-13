<?php

namespace backend\controllers;

use common\models\TblAppointment;
use common\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * AppointmentController - All staff can view, Director & Receptionist can manage
 */
class AppointmentController extends Controller
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
                                // All staff can view
                                if (in_array($action->id, ['index', 'view', 'update'])) {
                                    return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                                }
                                // Director & Receptionist can manage
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

    public function actionGetDetails($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $appointment = TblAppointment::findOne($id);
        if ($appointment) {
            return [
                'success' => true,
                'patient_id' => $appointment->patient_id,
                'dr_id' => $appointment->dr_id,
                'status' => $appointment->status,
            ];
        }
        return ['success' => false];
    }

    public function actionIndex()
    {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($appt_id)
    {
        return $this->render('view', ['model' => $this->findModel($appt_id)]);
    }

    public function actionCreate()
    {
        $model = new TblAppointment();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($appt_id)
    {
        $model = $this->findModel($appt_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($appt_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete appointments.');
            return $this->redirect(['index']);
        }
        $this->findModel($appt_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($appt_id)
    {
        if (($model = TblAppointment::findOne(['appt_id' => $appt_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}