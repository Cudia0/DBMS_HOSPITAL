<?php

namespace backend\controllers;

use common\models\TblDoctor;
use common\models\DoctorSearch;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * DoctorController - Only Director can manage doctors
 */
class DoctorController extends Controller
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
        $searchModel = new DoctorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($dr_id)
    {
        return $this->render('view', ['model' => $this->findModel($dr_id)]);
    }

    public function actionCreate()
    {
        $model = new TblDoctor();
        if ($this->request->isPost && $model->load($this->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Auto-create user account
                    if ($model->email) {
                        $existingUser = User::find()->where(['email' => $model->email])->one();
                        if (!$existingUser) {
                            $user = new User();
                            $user->username = 'dr.' . strtolower($model->first_name . '.' . $model->last_name);
                            $user->email = $model->email;
                            $user->status = User::STATUS_ACTIVE;
                            $user->setPassword('Doctor@' . $model->dr_id);
                            $user->generateAuthKey();
                            $user->save();
                            
                            Yii::$app->session->setFlash('info', 
                                'Doctor account created!<br>Username: <strong>' . $user->username . '</strong><br>Password: <strong>Doctor@' . $model->dr_id . '</strong>'
                            );
                        }
                    }
                    $transaction->commit();
                    return $this->redirect(['view', 'dr_id' => $model->dr_id]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($dr_id)
    {
        $model = $this->findModel($dr_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dr_id' => $model->dr_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($dr_id)
    {
        $this->findModel($dr_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($dr_id)
    {
        if (($model = TblDoctor::findOne(['dr_id' => $dr_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}