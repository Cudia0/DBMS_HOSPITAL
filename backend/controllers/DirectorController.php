<?php

namespace backend\controllers;

use common\repositories\DirectorRepository;
use common\repositories\UserRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * DirectorController - Only Director can manage directors
 * Auto-creates user account when director is created
 * Uses raw SQL via repositories
 */
class DirectorController extends Controller
{
    private DirectorRepository $directorRepo;
    private UserRepository $userRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
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
     * Lists all directors
     * SQL: SELECT * FROM tbl_director ORDER BY last_name
     */
    public function actionIndex()
    {
        // SQL: SELECT * FROM tbl_director
        $directors = $this->directorRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $directors,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single director
     * SQL: SELECT * FROM tbl_director WHERE director_id = :id
     */
    public function actionView($director_id)
    {
        // SQL: SELECT * FROM tbl_director WHERE director_id = :id
        $model = $this->directorRepo->findById($director_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Director not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new director + Auto-creates user account
     * SQL: INSERT INTO tbl_director (...) VALUES (...)
     * SQL: INSERT INTO user (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblDirector();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblDirector', []);
            
            // SQL: INSERT INTO tbl_director (...) VALUES (...)
            $directorId = $this->directorRepo->create($post);
            
            if ($directorId) {
                // Auto-create user account
                if (!empty($post['email'])) {
                    // SQL: SELECT * FROM user WHERE email = :email
                    $existingUser = $this->userRepo->findByEmail($post['email']);
                    
                    if ($existingUser) {
                        Yii::$app->session->setFlash('warning', 'Director saved, but a user account with this email already exists.');
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
                            '✅ Director created!<br>Username: <strong>' . $username . '</strong><br>Password: <strong>' . $password . '</strong>'
                        );
                        return $this->redirect(['view', 'director_id' => $directorId]);
                    }
                }
                
                Yii::$app->session->setFlash('success', '✅ Director created successfully.');
                return $this->redirect(['view', 'director_id' => $directorId]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a director
     * SQL: UPDATE tbl_director SET ... WHERE director_id = :id
     */
    public function actionUpdate($director_id)
    {
        $director = $this->directorRepo->findById($director_id);
        if (!$director) throw new NotFoundHttpException('Director not found.');

        $model = new \common\models\TblDirector();
        $model->attributes = $director;
        $oldEmail = $director['email'];

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblDirector', []);
            
            // SQL: UPDATE tbl_director SET ... WHERE director_id = :id
            $this->directorRepo->update($director_id, $post);
            
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
            
            Yii::$app->session->setFlash('success', '✅ Director updated successfully.');
            return $this->redirect(['view', 'director_id' => $director_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a director + associated user account
     * SQL: DELETE FROM tbl_director WHERE director_id = :id
     * SQL: DELETE FROM user WHERE email = :email
     */
    public function actionDelete($director_id)
    {
        $director = $this->directorRepo->findById($director_id);
        if (!$director) throw new NotFoundHttpException('Director not found.');
        
        $name = $director['first_name'] . ' ' . $director['last_name'];
        $email = $director['email'];
        
        // SQL: DELETE FROM user WHERE email = :email
        if ($email) {
            $user = $this->userRepo->findByEmail($email);
            if ($user) {
                $this->userRepo->delete($user['id']);
            }
        }
        
        // SQL: DELETE FROM tbl_director WHERE director_id = :id
        $this->directorRepo->delete($director_id);
        
        Yii::$app->session->setFlash('success', '✅ ' . $name . ' and associated account deleted.');
        return $this->redirect(['index']);
    }

    /**
     * Generate username: dir.firstname.lastname
     */
    private function generateUsername(array $data): string
    {
        $base = 'dir.' . strtolower(preg_replace('/[^a-z0-9]/', '', $data['first_name'] . '.' . $data['last_name']));
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