<?php

namespace frontend\controllers;

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
 * ProfileController - Manages user profiles for all roles (Frontend)
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
                'rules' => [['allow' => true, 'roles' => ['@']]],
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
        
        return $this->render('index', [
            'user' => $user,
            'roleModel' => $roleModel ? (object) $roleModel : null,
        ]);
    }

    public function actionUpdateUsername()
    {
        $user = Yii::$app->user->identity;
        if (Yii::$app->request->isPost) {
            $newUsername = Yii::$app->request->post('username');
            if (empty($newUsername)) { Yii::$app->session->setFlash('error', 'Username cannot be empty.'); return $this->redirect(['index']); }
            if ($this->userRepo->usernameExists($newUsername, $user->id)) { Yii::$app->session->setFlash('error', 'Username already taken.'); return $this->redirect(['index']); }
            // SQL: UPDATE user SET username = :username, updated_at = :updated_at WHERE id = :id
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
            // SQL: UPDATE user SET password_hash = :password_hash, updated_at = :updated_at WHERE id = :id
            $this->userRepo->updatePassword($user->id, Yii::$app->security->generatePasswordHash($np), time());
            Yii::$app->session->setFlash('success', 'Password updated.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdatePatientInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isPatient()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        
        $patientId = $this->getPatientId($user);
        if (!$patientId) { Yii::$app->session->setFlash('error', 'Patient record not found.'); return $this->redirect(['index']); }
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblPatient', []);
            
            // Get existing patient data first
            $existingPatient = $this->patientRepo->findById($patientId);
            
            // Build update data - keep existing email, don't overwrite with null
            $updateData = [
                'first_name' => $post['first_name'] ?? $existingPatient['first_name'],
                'middle_name' => $post['middle_name'] ?? $existingPatient['middle_name'],
                'last_name' => $post['last_name'] ?? $existingPatient['last_name'],
                'sex' => $post['sex'] ?? $existingPatient['sex'],
                'date_of_birth' => $post['date_of_birth'] ?? $existingPatient['date_of_birth'],
                'phone_num' => $post['phone_num'] ?? $existingPatient['phone_num'],
                'country_code' => $post['country_code'] ?? $existingPatient['country_code'],
                'email' => $existingPatient['email'], // NEVER change email
                'address' => $post['address'] ?? $existingPatient['address'],
            ];
            
            // SQL: UPDATE tbl_patient SET ... WHERE patient_id = :id
            $this->patientRepo->update($patientId, $updateData);
            Yii::$app->session->setFlash('success', 'Personal information updated successfully.');
        }
        return $this->redirect(['index']);
    }

    public function actionUpdateDoctorInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isDoctor()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        
        $doctorId = $this->getDoctorId($user);
        if (!$doctorId) { Yii::$app->session->setFlash('error', 'Doctor record not found.'); return $this->redirect(['index']); }
        
        if (Yii::$app->request->isPost) {
            $existingDoctor = $this->doctorRepo->findById($doctorId);
            if ($existingDoctor) {
                $existingDoctor['first_name'] = Yii::$app->request->post('first_name', $existingDoctor['first_name']);
                $existingDoctor['middle_name'] = Yii::$app->request->post('middle_name', $existingDoctor['middle_name']);
                $existingDoctor['last_name'] = Yii::$app->request->post('last_name', $existingDoctor['last_name']);
                // SQL: UPDATE tbl_doctor SET ... WHERE dr_id = :id
                $this->doctorRepo->update($doctorId, $existingDoctor);
                Yii::$app->session->setFlash('success', 'Name updated.');
            }
        }
        return $this->redirect(['index']);
    }

    public function actionUpdateReceptionistInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isReceptionist()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        
        $recepId = $this->getReceptionistId($user);
        if (!$recepId) { Yii::$app->session->setFlash('error', 'Receptionist record not found.'); return $this->redirect(['index']); }
        
        if (Yii::$app->request->isPost) {
            $existingRecep = $this->receptionistRepo->findById($recepId);
            if ($existingRecep) {
                $existingRecep['first_name'] = Yii::$app->request->post('first_name', $existingRecep['first_name']);
                $existingRecep['middle_name'] = Yii::$app->request->post('middle_name', $existingRecep['middle_name']);
                $existingRecep['last_name'] = Yii::$app->request->post('last_name', $existingRecep['last_name']);
                // SQL: UPDATE tbl_receptionist SET ... WHERE recep_id = :id
                $this->receptionistRepo->update($recepId, $existingRecep);
                Yii::$app->session->setFlash('success', 'Name updated.');
            }
        }
        return $this->redirect(['index']);
    }

    public function actionUpdateDirectorInfo()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isDirector()) { Yii::$app->session->setFlash('error', 'Access denied.'); return $this->redirect(['index']); }
        
        $directorId = $this->getDirectorId($user);
        if (!$directorId) { Yii::$app->session->setFlash('error', 'Director record not found.'); return $this->redirect(['index']); }
        
        if (Yii::$app->request->isPost) {
            $existingDirector = $this->directorRepo->findById($directorId);
            if ($existingDirector) {
                $existingDirector['first_name'] = Yii::$app->request->post('first_name', $existingDirector['first_name']);
                $existingDirector['middle_name'] = Yii::$app->request->post('middle_name', $existingDirector['middle_name']);
                $existingDirector['last_name'] = Yii::$app->request->post('last_name', $existingDirector['last_name']);
                // SQL: UPDATE tbl_director SET ... WHERE director_id = :id
                $this->directorRepo->update($directorId, $existingDirector);
                Yii::$app->session->setFlash('success', 'Name updated.');
            }
        }
        return $this->redirect(['index']);
    }

    private function getPatientId($user): ?int
    {
        if (!empty($user->patient_id)) return $user->patient_id;
        if (!empty($user->email)) {
            $patient = $this->patientRepo->findByEmail($user->email);
            if ($patient) { $user->patient_id = $patient['patient_id']; return $patient['patient_id']; }
        }
        return null;
    }

    private function getDoctorId($user): ?int
    {
        if (!empty($user->doctor_id)) return $user->doctor_id;
        if (!empty($user->email)) {
            $doctor = $this->doctorRepo->findByEmail($user->email);
            if ($doctor) { $user->doctor_id = $doctor['dr_id']; return $doctor['dr_id']; }
        }
        return null;
    }

    private function getReceptionistId($user): ?int
    {
        if (!empty($user->receptionist_id)) return $user->receptionist_id;
        if (!empty($user->email)) {
            $receptionist = $this->receptionistRepo->findByEmail($user->email);
            if ($receptionist) { $user->receptionist_id = $receptionist['recep_id']; return $receptionist['recep_id']; }
        }
        return null;
    }

    private function getDirectorId($user): ?int
    {
        if (!empty($user->director_id)) return $user->director_id;
        if (!empty($user->email)) {
            $director = $this->directorRepo->findByEmail($user->email);
            if ($director) { $user->director_id = $director['director_id']; return $director['director_id']; }
        }
        return null;
    }

    private function getRoleModel($user)
    {
        switch ($user->role) {
            case 'patient':
                $patientId = $this->getPatientId($user);
                return $patientId ? $this->patientRepo->findById($patientId) : null;
            case 'doctor':
                $doctorId = $this->getDoctorId($user);
                return $doctorId ? $this->doctorRepo->findById($doctorId) : null;
            case 'receptionist':
                $recepId = $this->getReceptionistId($user);
                return $recepId ? $this->receptionistRepo->findById($recepId) : null;
            case 'director':
                $directorId = $this->getDirectorId($user);
                return $directorId ? $this->directorRepo->findById($directorId) : null;
        }
        return null;
    }
}