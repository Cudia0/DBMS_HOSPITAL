<?php

namespace frontend\controllers;

use common\repositories\AppointmentRepository;
use common\repositories\DoctorRepository;
use common\repositories\PatientRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * AppointmentController - Patient's appointment management (Frontend)
 */
class AppointmentController extends Controller
{
    private AppointmentRepository $appointmentRepo;
    private DoctorRepository $doctorRepo;
    private PatientRepository $patientRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->appointmentRepo = new AppointmentRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->patientRepo = new PatientRepository();
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
                            'actions' => ['index', 'view', 'create'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isPatient();
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => ['delete' => ['POST']],
                ],
            ]
        );
    }

    /**
     * Get patient ID from logged-in user
     * Tries multiple methods to find the patient
     */
    private function getPatientId(): ?int
    {
        $user = Yii::$app->user->identity;
        
        // Method 1: Use patient_id from user object (set by detectRole)
        if (!empty($user->patient_id)) {
            return $user->patient_id;
        }
        
        // Method 2: Find patient by email
        if (!empty($user->email)) {
            // SQL: SELECT * FROM tbl_patient WHERE email = :email
            $patient = $this->patientRepo->findByEmail($user->email);
            if ($patient) {
                // Update user's patient_id for future use
                $user->patient_id = $patient['patient_id'];
                return $patient['patient_id'];
            }
        }
        
        // Method 3: Find patient by username (firstname.lastname format)
        if (!empty($user->username)) {
            $parts = explode('.', $user->username);
            if (count($parts) >= 2) {
                $firstName = $parts[0];
                $lastName = $parts[1];
                // SQL: SELECT * FROM tbl_patient WHERE first_name LIKE :fn AND last_name LIKE :ln
                $patients = $this->patientRepo->search($firstName . ' ' . $lastName);
                if (!empty($patients)) {
                    $user->patient_id = $patients[0]['patient_id'];
                    return $patients[0]['patient_id'];
                }
            }
        }
        
        return null;
    }

    public function actionIndex()
    {
        $patientId = $this->getPatientId();
        
        if (!$patientId) {
            Yii::$app->session->setFlash('warning', 'Please complete your patient profile first.');
            return $this->redirect(['profile/index']);
        }
        
        // SQL: SELECT a.*, d.first_name, d.last_name FROM tbl_appointment a LEFT JOIN tbl_doctor d ON a.dr_id = d.dr_id WHERE a.patient_id = :patient_id
        $appointments = $this->appointmentRepo->findByPatient($patientId);
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $appointments,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($appt_id)
    {
        $model = $this->appointmentRepo->findById($appt_id);
        
        if (!$model) throw new NotFoundHttpException('Appointment not found.');
        
        $patientId = $this->getPatientId();
        
        if (!$patientId || $model['patient_id'] != $patientId) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own appointments.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    public function actionCreate()
{
    $model = new \common\models\TblAppointment();
    $patientId = $this->getPatientId();
    
    if (!$patientId) {
        Yii::$app->session->setFlash('warning', 'Please complete your patient profile before booking an appointment.');
        return $this->redirect(['profile/index']);
    }
    
    // SQL: SELECT * FROM tbl_patient WHERE patient_id = :id
    $patient = $this->patientRepo->findById($patientId);
    
    if (!$patient) {
        Yii::$app->session->setFlash('error', 'Patient profile not found.');
        return $this->redirect(['site/index']);
    }

    if (Yii::$app->request->isPost) {
        $post = Yii::$app->request->post('TblAppointment', []);
        $post['patient_id'] = $patientId;
        $post['status'] = null;
        $post['recep_id'] = null;
        $post['appointment_date'] = null;
        $post['appointment_time'] = null;
        
        // SQL: INSERT INTO tbl_appointment (...) VALUES (...)
        $id = $this->appointmentRepo->create($post);
        
        if ($id) {
            Yii::$app->session->setFlash('success', '✅ Appointment request submitted! The receptionist will schedule your appointment.');
            return $this->redirect(['view', 'appt_id' => $id]);
        }
    }

    // Pass patient as array, not object
    return $this->render('create', ['model' => $model, 'patient' => $patient]);
}
}