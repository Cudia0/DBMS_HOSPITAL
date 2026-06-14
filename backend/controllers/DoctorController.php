<?php

namespace backend\controllers;

use common\repositories\DoctorRepository;
use common\repositories\DepartmentRepository;
use common\repositories\UserRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * DoctorController - Only Director can manage doctors
 * Auto-creates user account when doctor is created
 * Uses raw SQL via repositories
 */
class DoctorController extends Controller
{
    private DoctorRepository $doctorRepo;
    private DepartmentRepository $deptRepo;
    private UserRepository $userRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->doctorRepo = new DoctorRepository();
        $this->deptRepo = new DepartmentRepository();
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
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isDirector();
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
     * Lists all doctors
     * SQL: SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id ORDER BY d.last_name
     */
    public function actionIndex()
    {
        // SQL: SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id
        $doctors = $this->doctorRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $doctors,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single doctor
     * SQL: SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id WHERE d.dr_id = :id
     */
    public function actionView($dr_id)
    {
        // SQL: SELECT ... WHERE dr_id = :id
        $model = $this->doctorRepo->findById($dr_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Doctor not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new doctor + Auto-creates user account
     * SQL: INSERT INTO tbl_doctor (...) VALUES (...)
     * SQL: INSERT INTO user (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblDoctor();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblDoctor', []);
            
            // SQL: INSERT INTO tbl_doctor (...) VALUES (...)
            $doctorId = $this->doctorRepo->create($post);
            
            if ($doctorId) {
                // Auto-create user account if email provided
                if (!empty($post['email'])) {
                    // SQL: SELECT * FROM user WHERE email = :email
                    $existingUser = $this->userRepo->findByEmail($post['email']);
                    
                    if ($existingUser) {
                        Yii::$app->session->setFlash('warning', 'Doctor saved, but a user account with this email already exists.');
                    } else {
                        $username = $this->generateUsername($post);
                        $password = $this->generatePassword($post);
                        
                        // SQL: INSERT INTO user (...) VALUES (...)
                        $this->userRepo->create([
                            'username' => $username,
                            'auth_key' => Yii::$app->security->generateRandomString(),
                            'password_hash' => Yii::$app->security->generatePasswordHash($password),
                            'email' => $post['email'],
                            'status' => 10,
                            'verification_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                            'created_at' => time(),
                            'updated_at' => time(),
                        ]);
                        
                        Yii::$app->session->setFlash('success', 
                            '✅ Doctor created!<br>Username: <strong>' . $username . '</strong><br>Password: <strong>' . $password . '</strong>'
                        );
                        return $this->redirect(['view', 'dr_id' => $doctorId]);
                    }
                }
                
                Yii::$app->session->setFlash('success', '✅ Doctor created successfully.');
                return $this->redirect(['view', 'dr_id' => $doctorId]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a doctor
     * SQL: UPDATE tbl_doctor SET ... WHERE dr_id = :id
     */
    public function actionUpdate($dr_id)
    {
        $doctor = $this->doctorRepo->findById($dr_id);
        if (!$doctor) throw new NotFoundHttpException('Doctor not found.');

        $model = new \common\models\TblDoctor();
        $model->attributes = $doctor;
        $oldEmail = $doctor['email'];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblDoctor', []);
            
            // SQL: UPDATE tbl_doctor SET ... WHERE dr_id = :id
            $this->doctorRepo->update($dr_id, $post);
            
            // If email changed, update user account
            if (!empty($post['email']) && $post['email'] !== $oldEmail) {
                // SQL: SELECT * FROM user WHERE email = :email
                $user = $this->userRepo->findByEmail($oldEmail);
                if ($user) {
                    $password = $this->generatePassword($post);
                    $this->userRepo->update($user['id'], [
                        'username' => $this->generateUsername($post),
                        'email' => $post['email'],
                        'updated_at' => time(),
                    ]);
                    $this->userRepo->updatePassword($user['id'], Yii::$app->security->generatePasswordHash($password), time());
                    Yii::$app->session->setFlash('info', 'User account updated with new email and password.');
                }
            }
            
            Yii::$app->session->setFlash('success', '✅ Doctor updated successfully.');
            return $this->redirect(['view', 'dr_id' => $dr_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a doctor + associated user account
     * SQL: DELETE FROM tbl_doctor WHERE dr_id = :id
     * SQL: DELETE FROM user WHERE email = :email
     */
    public function actionDelete($dr_id)
    {
        $doctor = $this->doctorRepo->findById($dr_id);
        if (!$doctor) throw new NotFoundHttpException('Doctor not found.');
        
        $name = 'Dr. ' . $doctor['first_name'] . ' ' . $doctor['last_name'];
        $email = $doctor['email'];
        
        // SQL: DELETE FROM user WHERE email = :email
        if ($email) {
            $user = $this->userRepo->findByEmail($email);
            if ($user) {
                $this->userRepo->delete($user['id']);
            }
        }
        
        // SQL: DELETE FROM tbl_doctor WHERE dr_id = :id
        $this->doctorRepo->delete($dr_id);
        
        Yii::$app->session->setFlash('success', '✅ ' . $name . ' and associated account deleted.');
        return $this->redirect(['index']);
    }

    /**
     * Generate username: dr.firstname.lastname
     */
    private function generateUsername(array $data): string
    {
        $base = 'dr.' . strtolower(preg_replace('/[^a-z0-9]/', '', $data['first_name'] . '.' . $data['last_name']));
        $username = $base;
        $count = 1;
        
        while ($this->userRepo->usernameExists($username)) {
            $username = $base . $count;
            $count++;
        }
        
        return $username;
    }

    /**
     * Generate password: Lastname@emailusername
     */
    private function generatePassword(array $data): string
    {
        $lastname = ucfirst(strtolower(preg_replace('/[^a-zA-Z]/', '', $data['last_name'])));
        $emailParts = explode('@', $data['email']);
        $emailUsername = strtolower(preg_replace('/[^a-z0-9]/', '', $emailParts[0] ?? 'user'));
        
        return $lastname . '@' . $emailUsername;
    }
}