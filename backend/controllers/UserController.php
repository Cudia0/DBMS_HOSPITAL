<?php

namespace backend\controllers;

use common\repositories\UserRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * UserController - Director manages user accounts
 * Uses raw SQL via UserRepository
 */
class UserController extends Controller
{
    private UserRepository $userRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userRepo = new UserRepository();
    }

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
     * SQL: SELECT * FROM user ORDER BY created_at DESC
     */
    public function actionIndex()
    {
        // SQL: SELECT * FROM user ORDER BY created_at DESC
        $users = $this->userRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $users,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single user
     * SQL: SELECT * FROM user WHERE id = :id AND status = 10
     */
    public function actionView($id)
    {
        // SQL: SELECT * FROM user WHERE id = :id
        $model = \common\models\User::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('User not found.');
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Creates a new user
     * SQL: INSERT INTO user (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\User();
        $model->scenario = 'create';

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->status = \common\models\User::STATUS_ACTIVE;
            $model->setPassword($model->password_hash);
            $model->generateAuthKey();
            $model->generateEmailVerificationToken();
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User account created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a user
     * SQL: UPDATE user SET ... WHERE id = :id
     */
    public function actionUpdate($id)
    {
        $model = \common\models\User::findOne($id);
        if (!$model) throw new NotFoundHttpException('User not found.');

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
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
     * SQL: UPDATE user SET status = 10, updated_at = :updated_at WHERE id = :id
     */
    public function actionActivate($id)
    {
        $model = \common\models\User::findOne($id);
        if (!$model) throw new NotFoundHttpException('User not found.');
        
        if ($model->status === \common\models\User::STATUS_INACTIVE) {
            // SQL: UPDATE user SET status = 10, updated_at = :updated_at WHERE id = :id
            $this->userRepo->activate($id, time());
            Yii::$app->session->setFlash('success', 'User activated.');
        } else {
            Yii::$app->session->setFlash('warning', 'User is already active.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Deactivate user account
     * SQL: UPDATE user SET status = 9, updated_at = :updated_at WHERE id = :id
     */
    public function actionDeactivate($id)
    {
        $model = \common\models\User::findOne($id);
        if (!$model) throw new NotFoundHttpException('User not found.');
        
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot deactivate your own account.');
            return $this->redirect(['index']);
        }
        
        if ($model->status === \common\models\User::STATUS_ACTIVE) {
            // SQL: UPDATE user SET status = 9, updated_at = :updated_at WHERE id = :id
            $this->userRepo->deactivate($id, time());
            Yii::$app->session->setFlash('success', 'User deactivated.');
        } else {
            Yii::$app->session->setFlash('warning', 'User is already inactive.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Delete user account
     * SQL: DELETE FROM user WHERE id = :id
     */
    public function actionDelete($id)
    {
        $model = \common\models\User::findOne($id);
        if (!$model) throw new NotFoundHttpException('User not found.');
        
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['index']);
        }
        
        // SQL: DELETE FROM user WHERE id = :id
        $this->userRepo->delete($id);
        
        Yii::$app->session->setFlash('success', 'User "' . $model->username . '" deleted.');
        return $this->redirect(['index']);
    }
}