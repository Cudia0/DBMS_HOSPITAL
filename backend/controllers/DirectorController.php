<?php

namespace backend\controllers;

use common\models\TblDirector;
use common\models\DirectorSearch;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * DirectorController - Only Director can access
 */
class DirectorController extends Controller
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
        $searchModel = new DirectorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($director_id)
    {
        return $this->render('view', ['model' => $this->findModel($director_id)]);
    }

    public function actionCreate()
    {
        $model = new TblDirector();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            // Auto-create user account
            if ($model->email) {
                $user = new User();
                $user->username = 'dir.' . strtolower($model->first_name . '.' . $model->last_name);
                $user->email = $model->email;
                $user->status = User::STATUS_ACTIVE;
                $user->setPassword('Director@' . $model->director_id);
                $user->generateAuthKey();
                $user->save();
            }
            return $this->redirect(['view', 'director_id' => $model->director_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($director_id)
    {
        $model = $this->findModel($director_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'director_id' => $model->director_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($director_id)
    {
        $this->findModel($director_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($director_id)
    {
        if (($model = TblDirector::findOne(['director_id' => $director_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}