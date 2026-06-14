<?php

namespace backend\controllers;

use common\repositories\ReceptionistRepository;
use common\repositories\DirectorRepository;
use common\repositories\UserRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * ReceptionistController - Only Director can manage receptionists
 * Auto-creates user account when receptionist is created
 * Uses raw SQL via repositories
 */
class ReceptionistController extends Controller
{
    private ReceptionistRepository $receptionistRepo;
    private DirectorRepository $directorRepo;
    private UserRepository $userRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->receptionistRepo = new ReceptionistRepository();
        $this->directorRepo = new DirectorRepository();
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
     * Lists all receptionists
     * SQL: SELECT r.*, d.first_name AS director_fname, d.last_name AS director_lname FROM tbl_receptionist r LEFT JOIN tbl_director d ON r.director_id = d.director_id ORDER BY r.last_name
     */
    public function actionIndex()
    {
        // SQL: SELECT r.*, d.first_name, d.last_name FROM tbl_receptionist r LEFT JOIN tbl_director d ON r.director_id = d.director_id
        $receptionists = $this->receptionistRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $receptionists,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single receptionist
     * SQL: SELECT r.*, d.first_name, d.last_name FROM tbl_receptionist r LEFT JOIN tbl_director d ON r.director_id = d.director_id WHERE r.recep_id = :id
     */
    public function actionView($recep_id)
    {
        // SQL: SELECT ... WHERE recep_id = :id
        $model = $this->receptionistRepo->findById($recep_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Receptionist not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new receptionist + Auto-creates user account
     * SQL: INSERT INTO tbl_receptionist (...) VALUES (...)
     * SQL: INSERT INTO user (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblReceptionist();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblReceptionist', []);
            
            // SQL: INSERT INTO tbl_receptionist (...) VALUES (...)
            $recepId = $this->receptionistRepo->create($post);
            
            if ($recepId) {
                // Auto-create user account if email provided
                if (!empty($post['email'])) {
                    // SQL: SELECT * FROM user WHERE email = :email
                    $existingUser = $this->userRepo->findByEmail($post['email']);
                    
                    if ($existingUser) {
                        Yii::$app->session->setFlash('warning', 'Receptionist saved, but a user account with this email already exists.');
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
                            '✅ Receptionist created!<br>Username: <strong>' . $username . '</strong><br>Password: <strong>' . $password . '</strong>'
                        );
                        return $this->redirect(['view', 'recep_id' => $recepId]);
                    }
                }
                
                Yii::$app->session->setFlash('success', '✅ Receptionist created successfully.');
                return $this->redirect(['view', 'recep_id' => $recepId]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a receptionist
     * SQL: UPDATE tbl_receptionist SET ... WHERE recep_id = :id
     */
    public function actionUpdate($recep_id)
    {
        $receptionist = $this->receptionistRepo->findById($recep_id);
        if (!$receptionist) throw new NotFoundHttpException('Receptionist not found.');

        $model = new \common\models\TblReceptionist();
        $model->attributes = $receptionist;
        $oldEmail = $receptionist['email'];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblReceptionist', []);
            
            // SQL: UPDATE tbl_receptionist SET ... WHERE recep_id = :id
            $this->receptionistRepo->update($recep_id, $post);
            
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
            
            Yii::$app->session->setFlash('success', '✅ Receptionist updated successfully.');
            return $this->redirect(['view', 'recep_id' => $recep_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a receptionist + associated user account
     * SQL: DELETE FROM tbl_receptionist WHERE recep_id = :id
     * SQL: DELETE FROM user WHERE email = :email
     */
    public function actionDelete($recep_id)
    {
        $receptionist = $this->receptionistRepo->findById($recep_id);
        if (!$receptionist) throw new NotFoundHttpException('Receptionist not found.');
        
        $name = $receptionist['first_name'] . ' ' . $receptionist['last_name'];
        $email = $receptionist['email'];
        
        // SQL: DELETE FROM user WHERE email = :email
        if ($email) {
            $user = $this->userRepo->findByEmail($email);
            if ($user) {
                $this->userRepo->delete($user['id']);
            }
        }
        
        // SQL: DELETE FROM tbl_receptionist WHERE recep_id = :id
        $this->receptionistRepo->delete($recep_id);
        
        Yii::$app->session->setFlash('success', '✅ ' . $name . ' and associated account deleted.');
        return $this->redirect(['index']);
    }

    /**
     * Generate username: rec.firstname.lastname
     */
    private function generateUsername(array $data): string
    {
        $base = 'rec.' . strtolower(preg_replace('/[^a-z0-9]/', '', $data['first_name'] . '.' . $data['last_name']));
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