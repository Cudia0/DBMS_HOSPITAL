<?php

namespace backend\controllers;

use common\repositories\MedlineRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * MedlineController - Director & Doctor can view, Director can manage
 * Medline = Junction table linking prescriptions to medicines
 * Uses raw SQL via MedlineRepository
 */
class MedlineController extends Controller
{
    private MedlineRepository $medlineRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->medlineRepo = new MedlineRepository();
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
                                return $user->isDirector() || $user->isDoctor();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete'],
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
     * Lists all medline records
     * SQL: SELECT ml.*, m.med_name FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id ORDER BY ml.created_at DESC
     */
    public function actionIndex()
    {
        // SQL: SELECT ml.*, m.med_name, m.strength FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id
        $medlines = $this->medlineRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $medlines,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single medline record
     * SQL: SELECT ml.*, m.med_name FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.medline_id = :id
     */
    public function actionView($medline_id)
    {
        // SQL: SELECT ... WHERE medline_id = :id
        $model = $this->medlineRepo->findById($medline_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Medline record not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new medline record (Director only)
     * SQL: INSERT INTO tbl_medline (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblMedline();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblMedline', []);
            
            // SQL: INSERT INTO tbl_medline (...) VALUES (...)
            $id = $this->medlineRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Medline record created.');
                return $this->redirect(['view', 'medline_id' => $id]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Deletes a medline record (Director only)
     * SQL: DELETE FROM tbl_medline WHERE medline_id = :id
     */
    public function actionDelete($medline_id)
    {
        // SQL: DELETE FROM tbl_medline WHERE medline_id = :id
        $this->medlineRepo->delete($medline_id);
        
        Yii::$app->session->setFlash('success', 'Medline record deleted.');
        return $this->redirect(['index']);
    }
}