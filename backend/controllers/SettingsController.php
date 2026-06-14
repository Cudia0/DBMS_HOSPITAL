<?php

namespace backend\controllers;

use common\repositories\PatientRepository;
use common\repositories\DoctorRepository;
use common\repositories\AppointmentRepository;
use common\repositories\BillRepository;
use common\repositories\MedicineRepository;
use common\repositories\DepartmentRepository;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SettingsController - Director manages system configuration
 * Uses raw SQL via repositories
 */
class SettingsController extends Controller
{
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private AppointmentRepository $appointmentRepo;
    private BillRepository $billRepo;
    private MedicineRepository $medicineRepo;
    private DepartmentRepository $deptRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->appointmentRepo = new AppointmentRepository();
        $this->billRepo = new BillRepository();
        $this->medicineRepo = new MedicineRepository();
        $this->deptRepo = new DepartmentRepository();
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
                        'clear-cache' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * System settings page with statistics
     * SQL: SELECT COUNT(*) FROM various tables
     */
    public function actionIndex()
    {
        // SQL: SELECT COUNT(*) FROM tbl_patient
        $stats['totalPatients'] = $this->patientRepo->count();
        // SQL: SELECT COUNT(*) FROM tbl_doctor
        $stats['totalDoctors'] = $this->doctorRepo->count();
        // SQL: SELECT COUNT(*) FROM tbl_department
        $stats['totalDepartments'] = $this->deptRepo->count();
        // SQL: SELECT COUNT(*) FROM tbl_medicine
        $stats['totalMedicines'] = $this->medicineRepo->count();
        // SQL: SELECT COUNT(*) FROM tbl_appointment
        $stats['totalAppointments'] = $this->appointmentRepo->count();
        // SQL: SELECT COUNT(*) FROM tbl_bill
        $stats['totalBills'] = $this->billRepo->count();
        // SQL: SELECT COUNT(*) FROM tbl_appointment WHERE appointment_date = CURDATE()
        $stats['todayAppointments'] = $this->appointmentRepo->countToday(date('Y-m-d'));
        // SQL: SELECT * FROM tbl_appointment WHERE (status IS NULL OR status = '')
        $stats['pendingAppointments'] = count($this->appointmentRepo->findPending());
        // SQL: SELECT COUNT(*) FROM tbl_appointment WHERE status = 'completed'
        $stats['completedAppointments'] = $this->appointmentRepo->countByStatus('completed');
        // SQL: SELECT COALESCE(SUM(total_amount), 0) FROM tbl_bill WHERE payment_status = 'paid'
        $stats['totalRevenue'] = $this->billRepo->getTotalRevenue();
        // SQL: SELECT COUNT(*) FROM tbl_bill WHERE payment_status = 'pending'
        $stats['pendingPayments'] = count($this->billRepo->findAll());
        $stats['phpVersion'] = PHP_VERSION;
        $stats['yiiVersion'] = Yii::getVersion();
        $stats['databaseSize'] = $this->getDatabaseSize();

        $settings = [
            'appName' => Yii::$app->name,
            'adminEmail' => Yii::$app->params['adminEmail'] ?? 'admin@hospital.com',
            'supportEmail' => Yii::$app->params['supportEmail'] ?? 'support@hospital.com',
            'senderEmail' => Yii::$app->params['senderEmail'] ?? 'noreply@hospital.com',
            'senderName' => Yii::$app->params['senderName'] ?? 'MediSync Hospital',
            'passwordMinLength' => Yii::$app->params['user.passwordMinLength'] ?? 8,
            'passwordResetExpire' => Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600,
        ];

        if (Yii::$app->request->isPost) {
            Yii::$app->session->setFlash('info', 'Settings are configured in parameter files. Edit common/config/params.php to change.');
        }

        return $this->render('index', [
            'settings' => $settings,
            'stats' => $stats,
        ]);
    }

    /**
     * Clear application cache
     */
    public function actionClearCache()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('success', '✅ Application cache cleared.');
        return $this->redirect(['index']);
    }

    /**
     * Get approximate database size
     * SQL: SELECT SUM(data_length + index_length) FROM information_schema.tables WHERE table_schema = :db
     */
    private function getDatabaseSize(): string
    {
        try {
            $db = Yii::$app->db;
            $dbName = $db->createCommand("SELECT DATABASE()")->queryScalar();
            $result = $db->createCommand(
                "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = :db",
                [':db' => $dbName]
            )->queryOne();
            
            return ($result['size_mb'] ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}