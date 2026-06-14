<?php

namespace backend\controllers;

use common\repositories\MedicalRecordRepository;
use common\repositories\AppointmentRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * MedicalRecordController - Director & Doctor manage, Receptionist can view
 * Uses raw SQL via MedicalRecordRepository
 */
class MedicalRecordController extends Controller
{
    private MedicalRecordRepository $recordRepo;
    private AppointmentRepository $appointmentRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->recordRepo = new MedicalRecordRepository();
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

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $records = $this->recordRepo->findByDoctor($user->doctor_id);
        } else {
            $records = $this->recordRepo->findAll();
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $records,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($record_id)
    {
        $model = $this->recordRepo->findById($record_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Medical record not found.');
        }
        
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && isset($model['doctor_lname']) === false) {
            throw new \yii\web\ForbiddenHttpException('You can only view records for your own patients.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    public function actionCreate($appt_id = null)
    {
        $model = new \common\models\TblMedicalRecord();
        
        if ($appt_id) {
            $existing = $this->recordRepo->findByAppointment($appt_id);
            
            if ($existing) {
                Yii::$app->session->setFlash('warning', '⚠️ Medical record already exists for this appointment (Record #' . $existing['record_id'] . '). You can update it.');
                return $this->redirect(['update', 'record_id' => $existing['record_id']]);
            }
            
            $model->appt_id = $appt_id;
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblMedicalRecord', []);
            
            if (!empty($post['appt_id'])) {
                $existing = $this->recordRepo->findByAppointment($post['appt_id']);
                if ($existing) {
                    Yii::$app->session->setFlash('error', '❌ Medical record already exists for this appointment.');
                    return $this->redirect(['update', 'record_id' => $existing['record_id']]);
                }
            }
            
            $post['record_date'] = date('Y-m-d H:i:s');
            $id = $this->recordRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Medical record created successfully.');
                return $this->redirect(['view', 'record_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($record_id)
    {
        $record = $this->recordRepo->findById($record_id);
        if (!$record) throw new NotFoundHttpException('Medical record not found.');

        $model = new \common\models\TblMedicalRecord();
        $model->attributes = $record;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblMedicalRecord', []);
            $this->recordRepo->update($record_id, $post);
            Yii::$app->session->setFlash('success', '✅ Medical record updated.');
            return $this->redirect(['view', 'record_id' => $record_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($record_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete medical records.');
            return $this->redirect(['index']);
        }
        
        $this->recordRepo->delete($record_id);
        return $this->redirect(['index']);
    }
}