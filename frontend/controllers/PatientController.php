<?php

namespace frontend\controllers;

use common\models\TblPatient;
use common\models\PatientSearch;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * PatientController implements the CRUD actions for TblPatient model.
 */
class PatientController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                // ADD THIS: Restrict to logged-in patients only
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['create'],
                            'roles' => ['@'], // Allow create for newly registered users
                        ],
                        [
                            'allow' => true,
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isPatient();
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all TblPatient models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PatientSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblPatient model.
     * @param int $patient_id Patient ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($patient_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($patient_id),
        ]);
    }

    /**
     * Creates a new TblPatient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblPatient();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Check for potential duplicate before saving
                $duplicateCheck = $this->checkForDuplicates($model);
                
                if ($duplicateCheck['hasDuplicate']) {
                    Yii::$app->session->setFlash('warning', 
                        '⚠️ Potential duplicate patient detected: ' . $duplicateCheck['message']
                    );
                }
                
                if ($model->save()) {
                    // UPDATE USER'S EMAIL TO MATCH PATIENT'S EMAIL
                    if (!Yii::$app->user->isGuest) {
                        $user = Yii::$app->user->identity;
                        if ($model->email && $model->email !== 'N/A' && $model->email !== 'n/a') {
                            $user->email = $model->email;
                            $user->save(false);
                        }
                    }
                    
                    Yii::$app->session->setFlash('success', 
                        '✅ Patient registered successfully. Patient ID: ' . $model->patient_id . ' - ' . $model->getFullName()
                    );
                    return $this->redirect(['view', 'patient_id' => $model->patient_id]);
                } else {
                    Yii::$app->session->setFlash('error', '❌ Failed to register patient. Please check the errors below.');
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblPatient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $patient_id Patient ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($patient_id)
    {
        $model = $this->findModel($patient_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Check for potential duplicate before saving
            $duplicateCheck = $this->checkForDuplicates($model);
            
            if ($duplicateCheck['hasDuplicate']) {
                Yii::$app->session->setFlash('warning', 
                    '⚠️ Potential duplicate patient detected: ' . $duplicateCheck['message']
                );
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 
                    '✅ Patient updated successfully. Patient ID: ' . $model->patient_id . ' - ' . $model->getFullName()
                );
                return $this->redirect(['view', 'patient_id' => $model->patient_id]);
            } else {
                Yii::$app->session->setFlash('error', '❌ Failed to update patient. Please check the errors below.');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblPatient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $patient_id Patient ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($patient_id)
    {
        $model = $this->findModel($patient_id);
        $patientName = $model->getFullName();
        
        $model->delete();
        
        Yii::$app->session->setFlash('success', '✅ Patient deleted: ' . $patientName);

        return $this->redirect(['index']);
    }

    /**
     * Check for potential duplicate patients
     * @param TblPatient $model
     * @return array
     */
    protected function checkForDuplicates($model)
    {
        $messages = [];
        $hasDuplicate = false;
        
        if ($model->first_name && $model->last_name && $model->date_of_birth) {
            $existingPatient = TblPatient::find()
                ->where([
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'date_of_birth' => $model->date_of_birth,
                ])
                ->andFilterWhere(['!=', 'patient_id', $model->patient_id])
                ->one();
            
            if ($existingPatient) {
                $hasDuplicate = true;
                $messages[] = 'Patient with same name and date of birth exists (ID: ' . $existingPatient->patient_id . ')';
            }
        }
        
        if ($model->phone_num && $model->country_code) {
            $existingPhone = TblPatient::find()
                ->where([
                    'phone_num' => $model->phone_num,
                    'country_code' => $model->country_code,
                ])
                ->andFilterWhere(['!=', 'patient_id', $model->patient_id])
                ->one();
            
            if ($existingPhone) {
                $hasDuplicate = true;
                $messages[] = 'Phone number already registered to Patient ID: ' . $existingPhone->patient_id;
            }
        }
        
        if ($model->email && $model->email !== 'N/A' && $model->email !== 'n/a') {
            $existingEmail = TblPatient::find()
                ->where(['email' => $model->email])
                ->andFilterWhere(['!=', 'patient_id', $model->patient_id])
                ->one();
            
            if ($existingEmail) {
                $hasDuplicate = true;
                $messages[] = 'Email already registered to Patient ID: ' . $existingEmail->patient_id;
            }
        }
        
        return [
            'hasDuplicate' => $hasDuplicate,
            'message' => implode(' | ', $messages),
        ];
    }

    /**
     * Finds the TblPatient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $patient_id Patient ID
     * @return TblPatient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($patient_id)
    {
        if (($model = TblPatient::findOne(['patient_id' => $patient_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}