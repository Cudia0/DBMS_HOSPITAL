<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use common\models\TblPatient;
use common\models\TblDoctor;
use common\models\TblReceptionist;
use common\models\TblDirector;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * ProfileController - Manages user profiles for all roles
 */
class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update-password' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $roleModel = $this->getRoleModel($user);
        
        return $this->render('index', [
            'user' => $user,
            'roleModel' => $roleModel,
        ]);
    }

    public function actionUpdateUsername()
    {
        $user = Yii::$app->user->identity;
        
        if (Yii::$app->request->isPost) {
            $newUsername = Yii::$app->request->post('username');
            
            if (empty($newUsername)) {
                Yii::$app->session->setFlash('error', 'Username cannot be empty.');
                return $this->redirect(['index']);
            }
            
            $existingUser = User::find()->where(['username' => $newUsername])->andWhere(['!=', 'id', $user->id])->one();
            
            if ($existingUser) {
                Yii::$app->session->setFlash('error', 'Username already taken.');
                return $this->redirect(['index']);
            }
            
            $user->username = $newUsername;
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Username updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update username.');
            }
        }
        
        return $this->redirect(['index']);
    }

    public function actionUpdatePassword()
    {
        $user = Yii::$app->user->identity;
        
        if (Yii::$app->request->isPost) {
            $currentPassword = Yii::$app->request->post('current_password');
            $newPassword = Yii::$app->request->post('new_password');
            $confirmPassword = Yii::$app->request->post('confirm_password');
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                Yii::$app->session->setFlash('error', 'All password fields are required.');
                return $this->redirect(['index']);
            }
            
            if (!$user->validatePassword($currentPassword)) {
                Yii::$app->session->setFlash('error', 'Current password is incorrect.');
                return $this->redirect(['index']);
            }
            
            if ($newPassword !== $confirmPassword) {
                Yii::$app->session->setFlash('error', 'New passwords do not match.');
                return $this->redirect(['index']);
            }
            
            if (strlen($newPassword) < 6) {
                Yii::$app->session->setFlash('error', 'Password must be at least 6 characters.');
                return $this->redirect(['index']);
            }
            
            $user->setPassword($newPassword);
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Password updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update password.');
            }
        }
        
        return $this->redirect(['index']);
    }

    public function actionUpdatePatientInfo()
    {
        $user = Yii::$app->user->identity;
        
        if (!$user->isPatient()) {
            Yii::$app->session->setFlash('error', 'Access denied.');
            return $this->redirect(['index']);
        }
        
        $patient = TblPatient::findOne($user->patient_id);
        if (!$patient) {
            Yii::$app->session->setFlash('error', 'Patient record not found.');
            return $this->redirect(['index']);
        }
        
        if (Yii::$app->request->isPost) {
            $patient->load(Yii::$app->request->post());
            if ($patient->save()) {
                Yii::$app->session->setFlash('success', 'Personal information updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update: ' . implode(', ', $patient->getFirstErrors()));
            }
        }
        
        return $this->redirect(['index']);
    }

    private function getRoleModel($user)
    {
        switch ($user->role) {
            case 'patient': return TblPatient::findOne($user->patient_id);
            case 'doctor': return TblDoctor::findOne($user->doctor_id);
            case 'receptionist': return TblReceptionist::findOne($user->receptionist_id);
            case 'director': return TblDirector::findOne($user->director_id);
        }
        return null;
    }
}