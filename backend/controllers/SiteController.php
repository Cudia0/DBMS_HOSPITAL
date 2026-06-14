<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\repositories\AppointmentRepository;
use common\repositories\PatientRepository;
use common\repositories\DoctorRepository;
use common\repositories\ReceptionistRepository;
use common\repositories\DirectorRepository;
use common\repositories\BillRepository;
use common\repositories\MedicineRepository;
use common\repositories\DepartmentRepository;
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
    private AppointmentRepository $appointmentRepo;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private ReceptionistRepository $receptionistRepo;
    private DirectorRepository $directorRepo;
    private BillRepository $billRepo;
    private MedicineRepository $medicineRepo;
    private DepartmentRepository $deptRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->appointmentRepo = new AppointmentRepository();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->receptionistRepo = new ReceptionistRepository();
        $this->directorRepo = new DirectorRepository();
        $this->billRepo = new BillRepository();
        $this->medicineRepo = new MedicineRepository();
        $this->deptRepo = new DepartmentRepository();
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'about', 'contact'],
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
            'error' => ['class' => \yii\web\ErrorAction::class],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $stats = [];

        if ($user->isDirector()) {
            $stats['totalPatients'] = $this->patientRepo->count();
            $stats['totalDoctors'] = $this->doctorRepo->count();
            $stats['totalReceptionists'] = $this->receptionistRepo->count();
            $stats['totalDirectors'] = $this->directorRepo->count();
            $stats['totalDepartments'] = $this->deptRepo->count();
            $stats['totalMedicines'] = $this->medicineRepo->count();
            $stats['totalAppointments'] = $this->appointmentRepo->count();
            $stats['scheduledAppointments'] = $this->appointmentRepo->countByStatus('scheduled');
            $stats['completedAppointments'] = $this->appointmentRepo->countByStatus('completed');
            $stats['todayAppointments'] = $this->appointmentRepo->countToday(date('Y-m-d'));
            $stats['pendingAppointments'] = count($this->appointmentRepo->findPending());
            $stats['totalBills'] = $this->billRepo->count();
            $stats['totalRevenue'] = $this->billRepo->getTotalRevenue();
            $stats['monthlyRevenue'] = $this->billRepo->getMonthlyRevenue(date('Y-m-01'));
            $stats['todayAppointmentList'] = $this->appointmentRepo->findToday(date('Y-m-d'));
            $stats['recentPatients'] = $this->patientRepo->findRecent(5);
        } elseif ($user->isReceptionist()) {
            $stats['todayAppointments'] = $this->appointmentRepo->countToday(date('Y-m-d'));
            $stats['pendingAppointments'] = count($this->appointmentRepo->findPending());
            $stats['scheduledAppointments'] = $this->appointmentRepo->countByStatus('scheduled');
            $stats['checkedInAppointments'] = $this->appointmentRepo->countByStatus('checked_in');
            $stats['todayAppointmentList'] = $this->appointmentRepo->findToday(date('Y-m-d'));
        } elseif ($user->isDoctor()) {
            $doctorAppointments = $this->appointmentRepo->findByDoctor($user->doctor_id);
            $todayApps = array_filter($doctorAppointments, function($a) { return ($a['appointment_date'] ?? '') === date('Y-m-d'); });
            $stats['todayAppointments'] = count($todayApps);
            $stats['pendingConsultations'] = count(array_filter($doctorAppointments, function($a) { return ($a['status'] ?? '') === 'checked_in'; }));
            $stats['completedToday'] = count(array_filter($todayApps, function($a) { return ($a['status'] ?? '') === 'completed'; }));
            $stats['todayAppointmentList'] = $doctorAppointments;
        }

        return $this->render('index', ['user' => $user, 'stats' => $stats]);
    }

    public function actionLogin()
    {
        $this->layout = 'blank';

        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            if (!$user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'Patients must use the frontend portal.');
                return $this->redirect(['login']);
            }
            return $this->goHome();
        }

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post())) {
            $user = \common\models\User::findByUsername($model->username);
            
            if ($user && !$user->canAccessBackend()) {
                Yii::$app->session->setFlash('error', '⚠️ Patient accounts cannot access the admin portal.');
                $model->password = '';
                return $this->render('login', ['model' => $model]);
            }
            
            if ($model->login()) {
                $loggedInUser = Yii::$app->user->identity;
                if (!$loggedInUser->canAccessBackend()) {
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('error', '⚠️ Access Denied.');
                    return $this->redirect(['login']);
                }
                Yii::$app->session->setFlash('success', 'Welcome, ' . $loggedInUser->getFullName() . '!');
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

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionContact()
    {
        return $this->render('contact');
    }
}