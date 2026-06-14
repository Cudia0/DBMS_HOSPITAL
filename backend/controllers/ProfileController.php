<?php

namespace backend\controllers;

use common\repositories\UserRepository;
use common\repositories\PatientRepository;
use common\repositories\DoctorRepository;
use common\repositories\ReceptionistRepository;
use common\repositories\DirectorRepository;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * ProfileController - Manages user profiles for all roles (Backend)
 * Uses raw SQL via repositories
 */
class ProfileController extends Controller
{
    private UserRepository $userRepo;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private ReceptionistRepository $receptionistRepo;
    private DirectorRepository $directorRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userRepo = new UserRepository();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->receptionistRepo = new ReceptionistRepository();
        $this->directorRepo = new DirectorRepository();
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['update-password' => ['post']],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $roleModel = $this->getRoleModel($user);
        return $this->render('index', ['user' => $user, 'roleModel' => $roleModel ? (object) $roleModel : null]);
    }

    public function actionUpdateUsername()
    {
        $user = Yii::$app->user->identity;
        if (Yii::$app->request->isPost) {
            $newUsername = Yii::$app->request->post('username');
            if (empty($newUsername)) { Yii::$app->session->setFlash('error', 'Username cannot be empty.'); return $this->redirect(['index']); }
            if ($this->userRepo->usernameExists($newUsername, $user->id)) { Yii::$app->session->setFlash('error', 'Username already taken.'); return $this->redirect(['index']); }
            $this->userRepo->update($user->id, ['username' => $newUsername, 'email' => $user->email, 'updated_at' => time()]);
            Yii::$app->session->setFlash('success', 'Username updated.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdatePassword()
    {
        $user = Yii::$app->user->identity;
        if (Yii::$app->request->isPost) {
            $cp = Yii::$app->request->post('current_password');
            $np = Yii::$app->request->post('new_password');
            $cf = Yii::$app->request->post('confirm_password');
            if (empty($cp) || empty($np) || empty($cf)) { Yii::$app->session->setFlash('error', 'All fields required.'); return $this->redirect(['index']); }
            if (!$user->validatePassword($cp)) { Yii::$app->session->setFlash('error', 'Current password incorrect.'); return $this->redirect(['index']); }
            if ($np !== $cf) { Yii::$app->session->setFlash('error', 'Passwords do not match.'); return $this->redirect(['index']); }
            if (strlen($np) < 6) { Yii::$app->session->setFlash('error', 'Min 6 characters.'); return $this->redirect(['index']); }
            $this->userRepo->updatePassword($user->id, Yii::$app->security->generatePasswordHash($np), time());
            Yii::$app->session->setFlash('success', 'Password updated.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdatePatientInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isPatient()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        $patient = $this->patientRepo->findById($user->patient_id);
        if (!$patient) { Yii::$app->session->setFlash('error', 'Patient not found.'); return $this->redirect(['index']); }
        if (Yii::$app->request->isPost) {
            $this->patientRepo->update($user->patient_id, Yii::$app->request->post('TblPatient', []));
            Yii::$app->session->setFlash('success', 'Information updated.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdateDoctorInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isDoctor()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        $doctor = $this->doctorRepo->findById($user->doctor_id);
        if (!$doctor) { Yii::$app->session->setFlash('error', 'Doctor not found.'); return $this->redirect(['index']); }
        if (Yii::$app->request->isPost) {
            $doctor['first_name'] = Yii::$app->request->post('first_name');
            $doctor['middle_name'] = Yii::$app->request->post('middle_name');
            $doctor['last_name'] = Yii::$app->request->post('last_name');
            $this->doctorRepo->update($user->doctor_id, $doctor);
            Yii::$app->session->setFlash('success', 'Name updated.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdateReceptionistInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isReceptionist()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        $receptionist = $this->receptionistRepo->findById($user->receptionist_id);
        if (!$receptionist) { Yii::$app->session->setFlash('error', 'Receptionist not found.'); return $this->redirect(['index']); }
        if (Yii::$app->request->isPost) {
            $receptionist['first_name'] = Yii::$app->request->post('first_name');
            $receptionist['middle_name'] = Yii::$app->request->post('middle_name');
            $receptionist['last_name'] = Yii::$app->request->post('last_name');
            $this->receptionistRepo->update($user->receptionist_id, $receptionist);
            Yii::$app->session->setFlash('success', 'Name updated.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdateDirectorInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isDirector()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        $director = $this->directorRepo->findById($user->director_id);
        if (!$director) { Yii::$app->session->setFlash('error', 'Director not found.'); return $this->redirect(['index']); }
        if (Yii::$app->request->isPost) {
            $director['first_name'] = Yii::$app->request->post('first_name');
            $director['middle_name'] = Yii::$app->request->post('middle_name');
            $director['last_name'] = Yii::$app->request->post('last_name');
            $this->directorRepo->update($user->director_id, $director);
            Yii::$app->session->setFlash('success', 'Name updated.');
        }
        return $this->redirect(['index']);
    }

    private function getRoleModel($user)
    {
        switch ($user->role) {
            case 'patient': return $this->patientRepo->findById($user->patient_id);
            case 'doctor': return $this->doctorRepo->findById($user->doctor_id);
            case 'receptionist': return $this->receptionistRepo->findById($user->receptionist_id);
            case 'director': return $this->directorRepo->findById($user->director_id);
        }
        return null;
    }
}