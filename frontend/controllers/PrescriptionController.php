<?php

namespace frontend\controllers;

use common\repositories\PrescriptionRepository;
use common\repositories\PatientRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * PrescriptionController - Patient VIEW ONLY (Frontend)
 */
class PrescriptionController extends Controller
{
    private PrescriptionRepository $prescriptionRepo;
    private PatientRepository $patientRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->prescriptionRepo = new PrescriptionRepository();
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
                            'actions' => ['index', 'view'],
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
     */
    private function getPatientId(): ?int
    {
        $user = Yii::$app->user->identity;
        if (!empty($user->patient_id)) return $user->patient_id;
        if (!empty($user->email)) {
            $patient = $this->patientRepo->findByEmail($user->email);
            if ($patient) { $user->patient_id = $patient['patient_id']; return $patient['patient_id']; }
        }
        return null;
    }

    public function actionIndex()
    {
        $patientId = $this->getPatientId();
        if (!$patientId) { Yii::$app->session->setFlash('warning', 'Please complete your profile first.'); return $this->redirect(['profile/index']); }
        
        // SQL: SELECT pr.*, a.appointment_date, d.last_name FROM tbl_prescription pr JOIN tbl_appointment a ON pr.appt_id = a.appt_id JOIN tbl_doctor d ON a.dr_id = d.dr_id WHERE a.patient_id = :patient_id
        $prescriptions = $this->prescriptionRepo->findByPatient($patientId);
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $prescriptions,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($prescription_id)
    {
        $model = $this->prescriptionRepo->findById($prescription_id);
        if (!$model) throw new NotFoundHttpException('Prescription not found.');
        return $this->render('view', ['model' => (object) $model]);
    }
}