<?php

namespace backend\controllers;

use common\repositories\DepartmentRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * DepartmentController - Only Director can manage departments
 * Uses raw SQL via DepartmentRepository
 */
class DepartmentController extends Controller
{
    private DepartmentRepository $deptRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->deptRepo = new DepartmentRepository();
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
     * Lists all departments
     * SQL: SELECT * FROM tbl_department ORDER BY dept_name
     */
    public function actionIndex()
    {
        // SQL: SELECT * FROM tbl_department
        $departments = $this->deptRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $departments,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single department
     * SQL: SELECT * FROM tbl_department WHERE dept_id = :id
     */
    public function actionView($dept_id)
    {
        // SQL: SELECT * FROM tbl_department WHERE dept_id = :id
        $model = $this->deptRepo->findById($dept_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Department not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new department
     * SQL: INSERT INTO tbl_department (dept_name, operating_days, office_hours) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblDepartment();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblDepartment', []);
            
            // SQL: INSERT INTO tbl_department (...) VALUES (...)
            $id = $this->deptRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Department created successfully.');
                return $this->redirect(['view', 'dept_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a department
     * SQL: UPDATE tbl_department SET ... WHERE dept_id = :id
     */
    public function actionUpdate($dept_id)
    {
        $department = $this->deptRepo->findById($dept_id);
        if (!$department) throw new NotFoundHttpException('Department not found.');

        $model = new \common\models\TblDepartment();
        $model->attributes = $department;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblDepartment', []);
            
            // SQL: UPDATE tbl_department SET ... WHERE dept_id = :id
            $this->deptRepo->update($dept_id, $post);
            
            Yii::$app->session->setFlash('success', '✅ Department updated successfully.');
            return $this->redirect(['view', 'dept_id' => $dept_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a department
     * SQL: DELETE FROM tbl_department WHERE dept_id = :id
     */
    public function actionDelete($dept_id)
    {
        // SQL: DELETE FROM tbl_department WHERE dept_id = :id
        $this->deptRepo->delete($dept_id);
        
        Yii::$app->session->setFlash('success', 'Department deleted.');
        return $this->redirect(['index']);
    }
}