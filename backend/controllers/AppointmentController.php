<?php

namespace backend\controllers;

use common\repositories\AppointmentRepository;
use common\repositories\PatientRepository;
use common\repositories\DoctorRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * AppointmentController - Receptionist & Director manage, Doctor views own
 * Uses raw SQL via AppointmentRepository
 */
class AppointmentController extends Controller
{
    private AppointmentRepository $appointmentRepo;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->appointmentRepo = new AppointmentRepository();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
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
                            'actions' => ['create', 'update', 'accept', 'reject', 'check-in', 'delete'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                if ($user->isDirector()) return true;
                                if ($user->isReceptionist() && in_array($action->id, ['create', 'update', 'accept', 'reject', 'check-in'])) return true;
                                return false;
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => ['delete' => ['POST'], 'accept' => ['POST'], 'reject' => ['POST'], 'check-in' => ['POST']],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $appointments = $this->appointmentRepo->findByDoctor($user->doctor_id);
        } else {
            $appointments = $this->appointmentRepo->findAll();
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $appointments,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($appt_id)
    {
        $model = $this->appointmentRepo->findById($appt_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Appointment not found.');
        }
        
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && $model['dr_id'] != $user->doctor_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own appointments.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    public function actionCreate()
    {
        $model = new \common\models\TblAppointment();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblAppointment', []);
            $user = Yii::$app->user->identity;
            
            if ($user->isReceptionist() && empty($post['recep_id'])) {
                $post['recep_id'] = $user->receptionist_id;
            }
            
            $id = $this->appointmentRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', 'Appointment created successfully.');
                return $this->redirect(['view', 'appt_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionAccept($appt_id)
    {
        $appointment = $this->appointmentRepo->findById($appt_id);
        if (!$appointment) throw new NotFoundHttpException('Appointment not found.');
        
        $user = Yii::$app->user->identity;
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission.');
            return $this->redirect(['view', 'appt_id' => $appt_id]);
        }
        
        if (empty($appointment['status'])) {
            if (Yii::$app->request->isPost) {
                $date = Yii::$app->request->post('TblAppointment')['appointment_date'] ?? null;
                $time = Yii::$app->request->post('TblAppointment')['appointment_time'] ?? null;
                
                if (empty($date) || empty($time)) {
                    Yii::$app->session->setFlash('error', 'Please set date and time.');
                    return $this->redirect(['view', 'appt_id' => $appt_id]);
                }
                
                $this->appointmentRepo->accept($appt_id, 'scheduled', $date, $time, $user->isReceptionist() ? $user->receptionist_id : null);
                Yii::$app->session->setFlash('success', '✅ Appointment accepted and scheduled.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'This appointment is already processed.');
        }
        
        return $this->redirect(['view', 'appt_id' => $appt_id]);
    }

    public function actionReject($appt_id)
    {
        $appointment = $this->appointmentRepo->findById($appt_id);
        if (!$appointment) throw new NotFoundHttpException('Appointment not found.');
        
        $user = Yii::$app->user->identity;
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission.');
            return $this->redirect(['view', 'appt_id' => $appt_id]);
        }
        
        if (empty($appointment['status']) || $appointment['status'] === 'scheduled') {
            if (Yii::$app->request->isPost) {
                $reason = Yii::$app->request->post('reject_reason', '');
                $this->appointmentRepo->updateStatus($appt_id, 'cancelled');
                
                if ($reason) {
                    $currentSymptoms = $appointment['symptoms_list'] ?? '';
                    $this->appointmentRepo->update($appt_id, [
                        'symptoms_list' => $currentSymptoms . "\n\n[CANCELLED]\nReason: " . $reason,
                    ]);
                }
                
                Yii::$app->session->setFlash('success', '❌ Appointment cancelled.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'Cannot cancel. Current status: ' . $appointment['status']);
        }
        
        return $this->redirect(['view', 'appt_id' => $appt_id]);
    }

    public function actionCheckIn($appt_id)
    {
        $appointment = $this->appointmentRepo->findById($appt_id);
        if (!$appointment) throw new NotFoundHttpException('Appointment not found.');
        
        $user = Yii::$app->user->identity;
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission.');
            return $this->redirect(['view', 'appt_id' => $appt_id]);
        }
        
        if ($appointment['status'] === 'scheduled') {
            $this->appointmentRepo->updateStatus($appt_id, 'checked_in');
            Yii::$app->session->setFlash('success', '✅ Patient checked in successfully.');
        } else {
            Yii::$app->session->setFlash('warning', 'Appointment must be in scheduled status.');
        }
        
        return $this->redirect(['view', 'appt_id' => $appt_id]);
    }

    public function actionUpdate($appt_id)
    {
        $appointment = $this->appointmentRepo->findById($appt_id);
        if (!$appointment) throw new NotFoundHttpException('Appointment not found.');

        $model = new \common\models\TblAppointment();
        $model->attributes = $appointment;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblAppointment', []);
            $this->appointmentRepo->update($appt_id, $post);
            Yii::$app->session->setFlash('success', 'Appointment updated.');
            return $this->redirect(['view', 'appt_id' => $appt_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($appt_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete appointments.');
            return $this->redirect(['index']);
        }
        
        $this->appointmentRepo->delete($appt_id);
        return $this->redirect(['index']);
    }
}