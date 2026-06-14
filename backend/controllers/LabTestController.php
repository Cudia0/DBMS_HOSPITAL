<?php

namespace backend\controllers;

use common\repositories\LabTestRepository;
use common\repositories\AppointmentRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * LabTestController - Director & Doctor manage, Receptionist can view
 * Lab tests are OPTIONAL diagnostic tests ordered by doctors
 * Uses raw SQL via LabTestRepository
 */
class LabTestController extends Controller
{
    private LabTestRepository $labTestRepo;
    private AppointmentRepository $appointmentRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->labTestRepo = new LabTestRepository();
        $this->appointmentRepo = new AppointmentRepository();
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
                                return $user->isDirector() || $user->isDoctor();
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
     * Lists lab tests - Filtered for doctor
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $labTests = $this->labTestRepo->findByPatient($user->doctor_id);
        } else {
            $labTests = $this->labTestRepo->findAll();
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $labTests,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single lab test
     */
    public function actionView($test_id)
    {
        $model = $this->labTestRepo->findById($test_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Lab test not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Orders a new lab test
     * SQL: INSERT INTO tbl_lab_test (...) VALUES (...)
     */
    public function actionCreate($appt_id = null)
    {
        $model = new \common\models\TblLabTest();
        $model->status = 'ordered';
        $model->ordered_date = date('Y-m-d H:i:s');
        
        if ($appt_id) {
            $model->appt_id = $appt_id;
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblLabTest', []);
            $post['ordered_date'] = date('Y-m-d H:i:s');
            $post['status'] = $post['status'] ?? 'ordered';
            
            // SQL: INSERT INTO tbl_lab_test (...) VALUES (...)
            $id = $this->labTestRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Lab test ordered successfully.');
                return $this->redirect(['view', 'test_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a lab test (add results, change status)
     * SQL: UPDATE tbl_lab_test SET ... WHERE test_id = :id
     */
    public function actionUpdate($test_id)
    {
        $labTest = $this->labTestRepo->findById($test_id);
        if (!$labTest) throw new NotFoundHttpException('Lab test not found.');

        $model = new \common\models\TblLabTest();
        $model->attributes = $labTest;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblLabTest', []);
            
            if (!empty($post['results']) && empty($post['results_date'])) {
                $post['results_date'] = date('Y-m-d H:i:s');
            }
            
            // SQL: UPDATE tbl_lab_test SET ... WHERE test_id = :id
            $this->labTestRepo->update($test_id, $post);
            
            Yii::$app->session->setFlash('success', '✅ Lab test updated.');
            return $this->redirect(['view', 'test_id' => $test_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a lab test (Director only)
     * SQL: DELETE FROM tbl_lab_test WHERE test_id = :id
     */
    public function actionDelete($test_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete lab tests.');
            return $this->redirect(['index']);
        }
        
        // SQL: DELETE FROM tbl_lab_test WHERE test_id = :id
        $this->labTestRepo->delete($test_id);
        
        return $this->redirect(['index']);
    }
}