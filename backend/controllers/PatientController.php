<?php

namespace backend\controllers;

use common\repositories\PatientRepository;
use common\repositories\UserRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * PatientController - Director & Receptionist manage, Doctor can view
 * Uses raw SQL via PatientRepository
 */
class PatientController extends Controller
{
    private PatientRepository $patientRepo;
    private UserRepository $userRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->patientRepo = new PatientRepository();
        $this->userRepo = new UserRepository();
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
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist();
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
     * Lists all patients
     * SQL: SELECT * FROM tbl_patient ORDER BY last_name, first_name
     */
    public function actionIndex()
    {
        $patients = $this->patientRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $patients,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'attributes' => ['patient_id', 'first_name', 'last_name', 'sex', 'date_of_birth', 'created_at'],
            ],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single patient
     * SQL: SELECT * FROM tbl_patient WHERE patient_id = :id
     */
    public function actionView($patient_id)
    {
        $model = $this->patientRepo->findById($patient_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Patient not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new patient
     * SQL: INSERT INTO tbl_patient (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblPatient();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblPatient', []);
            
            if (!empty($post['first_name']) && !empty($post['last_name']) && !empty($post['date_of_birth'])) {
                $duplicate = $this->patientRepo->findDuplicate($post['first_name'], $post['last_name'], $post['date_of_birth']);
                if ($duplicate) {
                    Yii::$app->session->setFlash('warning', '⚠️ A patient with this name and date of birth already exists (ID: ' . $duplicate['patient_id'] . ').');
                }
            }
            
            $id = $this->patientRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Patient registered successfully. Patient ID: ' . $id);
                return $this->redirect(['view', 'patient_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing patient
     * SQL: UPDATE tbl_patient SET ... WHERE patient_id = :id
     */
    public function actionUpdate($patient_id)
    {
        $patient = $this->patientRepo->findById($patient_id);
        if (!$patient) {
            throw new NotFoundHttpException('Patient not found.');
        }

        $model = new \common\models\TblPatient();
        $model->attributes = $patient;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblPatient', []);
            
            if (!empty($post['first_name']) && !empty($post['last_name']) && !empty($post['date_of_birth'])) {
                $duplicate = $this->patientRepo->findDuplicate($post['first_name'], $post['last_name'], $post['date_of_birth'], $patient_id);
                if ($duplicate) {
                    Yii::$app->session->setFlash('warning', '⚠️ Another patient with this name and DOB exists.');
                }
            }
            
            $result = $this->patientRepo->update($patient_id, $post);
            
            if ($result) {
                Yii::$app->session->setFlash('success', '✅ Patient updated successfully.');
                return $this->redirect(['view', 'patient_id' => $patient_id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a patient (Director only)
     * SQL: DELETE FROM tbl_patient WHERE patient_id = :id
     */
    public function actionDelete($patient_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete patients.');
            return $this->redirect(['index']);
        }
        
        $this->patientRepo->delete($patient_id);
        
        Yii::$app->session->setFlash('success', 'Patient deleted.');
        return $this->redirect(['index']);
    }
}