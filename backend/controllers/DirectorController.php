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
 * DirectorController - Only Director can manage directors
 * When Director creates a director, a user account is auto-created
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

    /**
     * Lists all TblDirector models.
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
     */
    public function actionView($director_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($director_id),
        ]);
    }

    /**
     * Creates a new TblDirector model.
     * Also auto-creates a User account for login.
     */
    public function actionCreate()
    {
        $model = new TblDirector();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        // Auto-create user account for director
                        $existingUser = User::find()->where(['email' => $model->email])->one();
                        
                        if ($existingUser) {
                            Yii::$app->session->setFlash('warning', 
                                'Director saved, but a user account with this email already exists.'
                            );
                        } else {
                            // Generate username: dir.firstname.lastname
                            $username = $this->generateUsername($model);
                            
                            // Generate password: Lastname@emailusername
                            $password = $this->generatePassword($model);
                            
                            $user = new User();
                            $user->username = $username;
                            $user->email = $model->email;
                            $user->status = User::STATUS_ACTIVE;
                            $user->setPassword($password);
                            $user->generateAuthKey();
                            $user->generateEmailVerificationToken();
                            
                            if ($user->save()) {
                                Yii::$app->session->setFlash('success', 
                                    '✅ Director created successfully!<br><br>' .
                                    '<div class="alert alert-info">' .
                                    '<strong>📋 Login Credentials:</strong><br>' .
                                    'Username: <strong>' . $user->username . '</strong><br>' .
                                    'Password: <strong>' . $password . '</strong><br>' .
                                    'Role: <strong>Director</strong><br>' .
                                    '<small class="text-danger">⚠️ Please save these credentials. Share them securely with the director.</small>' .
                                    '</div>'
                                );
                            } else {
                                $errors = implode(', ', $user->getFirstErrors());
                                Yii::$app->session->setFlash('error', 
                                    'Director saved, but failed to create user account: ' . $errors
                                );
                            }
                        }
                        
                        $transaction->commit();
                        return $this->redirect(['view', 'director_id' => $model->director_id]);
                    }
                    $transaction->rollBack();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', '❌ Error: ' . $e->getMessage());
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing TblDirector model.
     */
    public function actionUpdate($director_id)
    {
        $model = $this->findModel($director_id);
        $oldEmail = $model->email;

        if ($this->request->isPost && $model->load($this->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // If email changed, update the user account
                    if ($model->email && $model->email !== $oldEmail) {
                        $user = User::find()->where(['email' => $oldEmail])->one();
                        if ($user) {
                            $user->email = $model->email;
                            $user->setPassword($this->generatePassword($model));
                            $user->save();
                            Yii::$app->session->setFlash('info', 'User account email and password updated to match.');
                        }
                    }
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '✅ Director updated successfully.');
                    return $this->redirect(['view', 'director_id' => $model->director_id]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', '❌ Error: ' . $e->getMessage());
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing TblDirector model.
     * Also removes the associated user account.
     */
    public function actionDelete($director_id)
    {
        $model = $this->findModel($director_id);
        $email = $model->email;
        $name = $model->first_name . ' ' . $model->last_name;
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($email) {
                $user = User::find()->where(['email' => $email])->one();
                if ($user) {
                    $user->delete();
                }
            }
            
            $model->delete();
            $transaction->commit();
            
            Yii::$app->session->setFlash('success', '✅ Director "' . $name . '" and associated account deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', '❌ Failed to delete: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Generate a unique username for the director
     * Format: dir.firstname.lastname
     */
    protected function generateUsername($model)
    {
        $base = 'dir.' . strtolower($model->first_name . '.' . $model->last_name);
        $base = preg_replace('/[^a-z0-9.]/', '', $base);
        $username = $base;
        $count = 1;
        
        while (User::findByUsername($username)) {
            $username = $base . $count;
            $count++;
        }
        
        return $username;
    }

    /**
     * Generate password for director
     * Format: Lastname@emailusername
     */
    protected function generatePassword($model)
    {
        $lastname = ucfirst(strtolower($model->last_name));
        $lastname = preg_replace('/[^a-zA-Z]/', '', $lastname);
        
        $emailParts = explode('@', $model->email);
        $emailUsername = isset($emailParts[0]) ? $emailParts[0] : 'user';
        $emailUsername = strtolower($emailUsername);
        $emailUsername = preg_replace('/[^a-z0-9]/', '', $emailUsername);
        
        return $lastname . '@' . $emailUsername;
    }

    /**
     * Finds the TblDirector model
     */
    protected function findModel($director_id)
    {
        if (($model = TblDirector::findOne(['director_id' => $director_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}