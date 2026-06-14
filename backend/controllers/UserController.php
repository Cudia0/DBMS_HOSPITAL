<?php

namespace backend\controllers;

use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * UserController - Director manages user accounts
 */
class UserController extends Controller
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
                        'activate' => ['POST'],
                        'deactivate' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all user accounts
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single user
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->detectRole();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new user (Director can create staff accounts)
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'create';

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->status = User::STATUS_ACTIVE;
                $model->setPassword($model->password_hash);
                $model->generateAuthKey();
                $model->generateEmailVerificationToken();
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User account created successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a user account
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            // If password changed
            if (!empty($model->password_hash)) {
                $model->setPassword($model->password_hash);
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Activate user account
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->status === User::STATUS_INACTIVE) {
            $model->status = User::STATUS_ACTIVE;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User account activated successfully.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'User account is already active.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Deactivate user account
     */
    public function actionDeactivate($id)
    {
        $model = $this->findModel($id);
        
        // Prevent deactivating own account
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot deactivate your own account.');
            return $this->redirect(['index']);
        }
        
        if ($model->status === User::STATUS_ACTIVE) {
            $model->status = User::STATUS_INACTIVE;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User account deactivated successfully.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'User account is already inactive.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Deletes a user account
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Prevent deleting own account
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['index']);
        }
        
        $username = $model->username;
        $model->delete();
        
        Yii::$app->session->setFlash('success', 'User "' . $username . '" deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
