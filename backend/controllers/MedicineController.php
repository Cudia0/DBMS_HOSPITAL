<?php

namespace backend\controllers;

use common\repositories\MedicineRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * MedicineController - Only Director can manage medicines
 * Uses raw SQL via MedicineRepository
 */
class MedicineController extends Controller
{
    private MedicineRepository $medicineRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->medicineRepo = new MedicineRepository();
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
     * Lists all medicines
     * SQL: SELECT * FROM tbl_medicine ORDER BY med_name
     */
    public function actionIndex()
    {
        // SQL: SELECT * FROM tbl_medicine
        $medicines = $this->medicineRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $medicines,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single medicine
     * SQL: SELECT * FROM tbl_medicine WHERE med_id = :id
     */
    public function actionView($med_id)
    {
        // SQL: SELECT * FROM tbl_medicine WHERE med_id = :id
        $model = $this->medicineRepo->findById($med_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Medicine not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new medicine
     * SQL: INSERT INTO tbl_medicine (med_name, dosage_form, strength, med_price) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblMedicine();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblMedicine', []);
            
            // Check duplicate
            if (!empty($post['med_name']) && !empty($post['strength'])) {
                $duplicate = $this->medicineRepo->findDuplicate($post['med_name'], $post['strength']);
                if ($duplicate) {
                    Yii::$app->session->setFlash('warning', '⚠️ Medicine with this name and strength already exists (ID: ' . $duplicate['med_id'] . ').');
                }
            }
            
            // SQL: INSERT INTO tbl_medicine (...) VALUES (...)
            $id = $this->medicineRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Medicine created successfully.');
                return $this->redirect(['view', 'med_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a medicine
     * SQL: UPDATE tbl_medicine SET ... WHERE med_id = :id
     */
    public function actionUpdate($med_id)
    {
        $medicine = $this->medicineRepo->findById($med_id);
        if (!$medicine) throw new NotFoundHttpException('Medicine not found.');

        $model = new \common\models\TblMedicine();
        $model->attributes = $medicine;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblMedicine', []);
            
            // SQL: UPDATE tbl_medicine SET ... WHERE med_id = :id
            $this->medicineRepo->update($med_id, $post);
            
            Yii::$app->session->setFlash('success', '✅ Medicine updated successfully.');
            return $this->redirect(['view', 'med_id' => $med_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a medicine
     * SQL: DELETE FROM tbl_medicine WHERE med_id = :id
     */
    public function actionDelete($med_id)
    {
        // SQL: DELETE FROM tbl_medicine WHERE med_id = :id
        $this->medicineRepo->delete($med_id);
        
        Yii::$app->session->setFlash('success', 'Medicine deleted.');
        return $this->redirect(['index']);
    }
}