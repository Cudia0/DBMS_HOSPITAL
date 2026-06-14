<?php

namespace backend\controllers;

use common\models\TblMedline;
use common\models\MedlineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * MedlineController - Only Director can manage medline records
 */
class MedlineController extends Controller
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
                            'actions' => ['index', 'view'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isDoctor();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete'],
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
        $searchModel = new MedlineSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($medline_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($medline_id),
        ]);
    }

    public function actionCreate()
    {
        $model = new TblMedline();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'medline_id' => $model->medline_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($medline_id)
    {
        $model = $this->findModel($medline_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'medline_id' => $model->medline_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($medline_id)
    {
        $this->findModel($medline_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($medline_id)
    {
        if (($model = TblMedline::findOne(['medline_id' => $medline_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}