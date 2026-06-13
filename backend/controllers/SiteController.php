<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller - BACKEND (Staff Only)
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->canAccessBackend();
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        return $this->render('index', [
            'user' => $user,
        ]);
    }

    /**
     * Login action - BACKEND ONLY FOR STAFF
     * Patient accounts will be rejected with error message
     */
    public function actionLogin()
    {
        // If already logged in as staff
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            
            // If patient somehow logged in to backend, log them out
            if (!$user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'Patients must use the frontend portal. Please login at the patient website.');
                return $this->redirect(['login']);
            }
            
            // Staff already logged in
            return $this->goHome();
        }

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post())) {
            // First check if user exists and is staff BEFORE logging in
            $user = \common\models\User::findByUsername($model->username);
            
            if ($user && !$user->canAccessBackend()) {
                // Patient trying to access backend - DON'T LOG THEM IN
                Yii::$app->session->setFlash('error', '⚠️ Access Denied: Patient accounts cannot access the admin portal. Please use the patient website.');
                $model->password = '';
                return $this->render('login', ['model' => $model]);
            }
            
            // Try to login (only staff accounts will succeed)
            if ($model->login()) {
                $loggedInUser = Yii::$app->user->identity;
                
                // Double-check after login
                if (!$loggedInUser->canAccessBackend()) {
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('error', '⚠️ Access Denied: This account does not have backend access.');
                    return $this->redirect(['login']);
                }
                
                Yii::$app->session->setFlash('success', 'Welcome, ' . $loggedInUser->getFullName() . ' (' . $loggedInUser->getRoleLabel() . ')!');
                return $this->goBack();
            }
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}