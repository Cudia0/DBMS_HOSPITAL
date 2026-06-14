<?php

namespace backend\controllers;

use common\models\TblReceptionist;
use common\models\ReceptionistSearch;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * ReceptionistController - Only Director can manage receptionists
 * When Director creates a receptionist, a user account is auto-created
 */
class ReceptionistController extends Controller
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
     * Lists all TblReceptionist models.
     */
    public function actionIndex()
    {
        $searchModel = new ReceptionistSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblReceptionist model.
     */
    public function actionView($recep_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($recep_id),
        ]);
    }

    /**
     * Creates a new TblReceptionist model.
     * Also auto-creates a User account for login.
     */
    public function actionCreate()
    {
        $model = new TblReceptionist();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        // Auto-create user account for receptionist
                        if ($model->email) {
                            $existingUser = User::find()->where(['email' => $model->email])->one();
                            
                            if ($existingUser) {
                                Yii::$app->session->setFlash('warning', 
                                    'Receptionist saved, but a user account with this email already exists.'
                                );
                            } else {
                                // Generate username: rec.firstname.lastname
                                $username = $this->generateUsername($model);
                                
                                // Generate password: lastname@emailusername
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
                                        '✅ Receptionist created successfully!<br><br>' .
                                        '<div class="alert alert-info">' .
                                        '<strong>📋 Login Credentials:</strong><br>' .
                                        'Username: <strong>' . $user->username . '</strong><br>' .
                                        'Password: <strong>' . $password . '</strong><br>' .
                                        'Role: <strong>Receptionist</strong><br>' .
                                        '<small class="text-danger">⚠️ Please save these credentials. Share them securely with the receptionist.</small>' .
                                        '</div>'
                                    );
                                } else {
                                    $errors = implode(', ', $user->getFirstErrors());
                                    Yii::$app->session->setFlash('error', 
                                        'Receptionist saved, but failed to create user account: ' . $errors
                                    );
                                }
                            }
                        } else {
                            Yii::$app->session->setFlash('warning', 
                                '⚠️ Receptionist created, but no email was provided. User account NOT created.'
                            );
                        }
                        
                        $transaction->commit();
                        return $this->redirect(['view', 'recep_id' => $model->recep_id]);
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
     * Updates an existing TblReceptionist model.
     */
    public function actionUpdate($recep_id)
    {
        $model = $this->findModel($recep_id);
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
                    Yii::$app->session->setFlash('success', '✅ Receptionist updated successfully.');
                    return $this->redirect(['view', 'recep_id' => $model->recep_id]);
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
     * Deletes an existing TblReceptionist model.
     * Also removes the associated user account.
     */
    public function actionDelete($recep_id)
    {
        $model = $this->findModel($recep_id);
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
            
            Yii::$app->session->setFlash('success', '✅ ' . $name . ' and associated account deleted.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', '❌ Failed to delete: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Generate a unique username for the receptionist
     * Format: rec.firstname.lastname
     */
    protected function generateUsername($model)
    {
        $base = 'rec.' . strtolower($model->first_name . '.' . $model->last_name);
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
     * Generate password for receptionist
     * Format: lastname@emailusername
     * Example: email = johndoe@gmail.com, lastname = Santos → Santos@johndoe
     */
    protected function generatePassword($model)
    {
        // Get lastname with first letter capitalized
        $lastname = ucfirst(strtolower($model->last_name));
        $lastname = preg_replace('/[^a-zA-Z]/', '', $lastname);
        
        // Get email username (part before @)
        $emailParts = explode('@', $model->email);
        $emailUsername = isset($emailParts[0]) ? $emailParts[0] : 'user';
        $emailUsername = strtolower($emailUsername);
        $emailUsername = preg_replace('/[^a-z0-9]/', '', $emailUsername);
        
        $password = $lastname . '@' . $emailUsername;
        
        return $password;
    }

    /**
     * Finds the TblReceptionist model based on its primary key value.
     */
    protected function findModel($recep_id)
    {
        if (($model = TblReceptionist::findOne(['recep_id' => $recep_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}