<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\TblDepartment;
use common\models\TblDoctor;
use common\models\TblPatient;
use common\models\TblAppointment;
use common\models\TblBill;
use common\models\TblMedicine;

/**
 * SettingsController - Director manages system configuration
 */
class SettingsController extends Controller
{
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
                    'actions' => [
                        'clear-cache' => ['POST'],
                        'reset-demo' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * System settings page
     */
    public function actionIndex()
    {
        // Get system statistics
        $stats = $this->getSystemStats();
        
        // Get current settings from params
        $settings = [
            'appName' => Yii::$app->name,
            'adminEmail' => Yii::$app->params['adminEmail'] ?? 'admin@hospital.com',
            'supportEmail' => Yii::$app->params['supportEmail'] ?? 'support@hospital.com',
            'senderEmail' => Yii::$app->params['senderEmail'] ?? 'noreply@hospital.com',
            'senderName' => Yii::$app->params['senderName'] ?? 'MediSync Hospital',
            'passwordMinLength' => Yii::$app->params['user.passwordMinLength'] ?? 8,
            'passwordResetExpire' => Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600,
        ];

        if (Yii::$app->request->isPost) {
            // Since params are in files, show success message
            // In production, you'd update a settings table or .env file
            Yii::$app->session->setFlash('info', 
                'Settings are configured in the params files. ' .
                'To change these values, edit:<br>' .
                '<code>common/config/params.php</code><br>' .
                '<code>frontend/config/params.php</code><br>' .
                '<code>backend/config/params.php</code>'
            );
        }

        return $this->render('index', [
            'settings' => $settings,
            'stats' => $stats,
        ]);
    }

    /**
     * Clear application cache
     */
    public function actionClearCache()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('success', '✅ Application cache cleared successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Get system statistics
     */
    private function getSystemStats()
    {
        return [
            'totalPatients' => TblPatient::find()->count(),
            'totalDoctors' => TblDoctor::find()->count(),
            'totalDepartments' => TblDepartment::find()->count(),
            'totalMedicines' => TblMedicine::find()->count(),
            'totalAppointments' => TblAppointment::find()->count(),
            'totalBills' => TblBill::find()->count(),
            'todayAppointments' => TblAppointment::find()
                ->where(['appointment_date' => date('Y-m-d')])
                ->count(),
            'pendingAppointments' => TblAppointment::find()
                ->where(['status' => null])
                ->orWhere(['status' => ''])
                ->count(),
            'completedAppointments' => TblAppointment::find()
                ->where(['status' => 'completed'])
                ->count(),
            'totalRevenue' => TblBill::find()
                ->where(['payment_status' => 'paid'])
                ->sum('total_amount') ?? 0,
            'pendingPayments' => TblBill::find()
                ->where(['payment_status' => 'pending'])
                ->count(),
            'databaseSize' => $this->getDatabaseSize(),
            'phpVersion' => PHP_VERSION,
            'yiiVersion' => Yii::getVersion(),
        ];
    }

    /**
     * Get approximate database size
     */
    private function getDatabaseSize()
    {
        try {
            $db = Yii::$app->db;
            $dbName = $db->createCommand("SELECT DATABASE()")->queryScalar();
            $result = $db->createCommand("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables
                WHERE table_schema = :db
            ", [':db' => $dbName])->queryOne();
            
            return ($result['size_mb'] ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}