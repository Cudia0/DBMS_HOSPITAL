<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\TblAppointment;
use common\models\TblPatient;
use common\models\TblDoctor;
use common\models\TblReceptionist;
use common\models\TblBill;
use common\models\TblMedicine;
use common\models\TblDepartment;
use common\models\TblDirector;
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

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Dashboard with statistics based on role
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        // Statistics for dashboard
        $stats = [];
        
        if ($user->isDirector()) {
            $stats = $this->getDirectorStats();
        } elseif ($user->isReceptionist()) {
            $stats = $this->getReceptionistStats();
        } elseif ($user->isDoctor()) {
            $stats = $this->getDoctorStats($user->doctor_id);
        }
        
        return $this->render('index', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Get statistics for Director dashboard
     */
    private function getDirectorStats()
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m-01');
        
        return [
            // Total counts
            'totalPatients' => TblPatient::find()->count(),
            'totalDoctors' => TblDoctor::find()->count(),
            'totalReceptionists' => TblReceptionist::find()->count(),
            'totalDirectors' => TblDirector::find()->count(),
            'totalDepartments' => TblDepartment::find()->count(),
            'totalMedicines' => TblMedicine::find()->count(),
            
            // Appointment stats
            'todayAppointments' => TblAppointment::find()
                ->where(['appointment_date' => $today])
                ->andWhere(['IS NOT', 'status', null])
                ->count(),
            'pendingAppointments' => TblAppointment::find()
                ->where(['status' => null])
                ->orWhere(['status' => ''])
                ->count(),
            'scheduledAppointments' => TblAppointment::find()
                ->where(['status' => 'scheduled'])
                ->count(),
            'completedAppointments' => TblAppointment::find()
                ->where(['status' => 'completed'])
                ->count(),
            'totalAppointments' => TblAppointment::find()->count(),
            
            // Bill stats
            'totalBills' => TblBill::find()->count(),
            'pendingPayments' => TblBill::find()
                ->where(['payment_status' => 'pending'])
                ->count(),
            'paidBills' => TblBill::find()
                ->where(['payment_status' => 'paid'])
                ->count(),
            'monthlyRevenue' => TblBill::find()
                ->where(['payment_status' => 'paid'])
                ->andWhere(['>=', 'bill_date', $thisMonth])
                ->sum('total_amount') ?? 0,
            'totalRevenue' => TblBill::find()
                ->where(['payment_status' => 'paid'])
                ->sum('total_amount') ?? 0,
                
            // Today's appointments list
            'todayAppointmentList' => TblAppointment::find()
                ->with(['patient', 'doctor'])
                ->where(['appointment_date' => $today])
                ->andWhere(['IS NOT', 'status', null])
                ->orderBy(['appointment_time' => SORT_ASC])
                ->limit(10)
                ->all(),
                
            // Recent patients
            'recentPatients' => TblPatient::find()
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(5)
                ->all(),
        ];
    }

    /**
     * Get statistics for Receptionist dashboard
     */
    private function getReceptionistStats()
    {
        $today = date('Y-m-d');
        
        return [
            'todayAppointments' => TblAppointment::find()
                ->where(['appointment_date' => $today])
                ->count(),
            'pendingAppointments' => TblAppointment::find()
                ->where(['status' => null])
                ->orWhere(['status' => ''])
                ->count(),
            'scheduledAppointments' => TblAppointment::find()
                ->where(['status' => 'scheduled'])
                ->count(),
            'checkedInAppointments' => TblAppointment::find()
                ->where(['status' => 'checked_in'])
                ->count(),
            'todayAppointmentList' => TblAppointment::find()
                ->with(['patient', 'doctor'])
                ->where(['appointment_date' => $today])
                ->orderBy(['appointment_time' => SORT_ASC])
                ->all(),
        ];
    }

    /**
     * Get statistics for Doctor dashboard
     */
    private function getDoctorStats($doctorId)
    {
        $today = date('Y-m-d');
        
        return [
            'todayAppointments' => TblAppointment::find()
                ->where(['dr_id' => $doctorId, 'appointment_date' => $today])
                ->count(),
            'pendingConsultations' => TblAppointment::find()
                ->where(['dr_id' => $doctorId, 'status' => 'checked_in'])
                ->count(),
            'completedToday' => TblAppointment::find()
                ->where(['dr_id' => $doctorId, 'status' => 'completed'])
                ->andWhere(['appointment_date' => $today])
                ->count(),
            'todayAppointmentList' => TblAppointment::find()
                ->with(['patient'])
                ->where(['dr_id' => $doctorId, 'appointment_date' => $today])
                ->orderBy(['appointment_time' => SORT_ASC])
                ->all(),
        ];
    }

    /**
     * Login action - BACKEND ONLY FOR STAFF
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            
            if (!$user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'Patients must use the frontend portal. Please login at the patient website.');
                return $this->redirect(['login']);
            }
            
            return $this->goHome();
        }

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post())) {
            $user = \common\models\User::findByUsername($model->username);
            
            if ($user && !$user->canAccessBackend()) {
                Yii::$app->session->setFlash('error', '⚠️ Access Denied: Patient accounts cannot access the admin portal. Please use the patient website.');
                $model->password = '';
                return $this->render('login', ['model' => $model]);
            }
            
            if ($model->login()) {
                $loggedInUser = Yii::$app->user->identity;
                
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

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}