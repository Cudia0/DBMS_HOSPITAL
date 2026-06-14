<?php

namespace frontend\controllers;

use common\repositories\MedicalRecordRepository;
use common\repositories\PatientRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * MedicalRecordController - Patient VIEW ONLY (Frontend)
 */
class MedicalRecordController extends Controller
{
    private MedicalRecordRepository $recordRepo;
    private PatientRepository $patientRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->recordRepo = new MedicalRecordRepository();
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
        
        $records = $this->recordRepo->findByPatient($patientId);
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $records,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($record_id)
    {
        $model = $this->recordRepo->findById($record_id);
        if (!$model) throw new NotFoundHttpException('Medical record not found.');
        
        return $this->render('view', ['model' => (object) $model]);
    }
}