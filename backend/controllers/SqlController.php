<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * SqlController - Shows SQL queries used by the system
 * Only Director can access
 */
class SqlController extends Controller
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
            ]
        );
    }

    /**
     * Display SQL queries from the log
     */
    public function actionIndex()
    {
        $logFile = Yii::getAlias('@runtime/logs/sql-queries.log');
        $queries = [];
        
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Parse Yii log format
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} \[info\]\[yii\\\\db\\\\Command::(\w+)\] (.*)$/', $line, $matches)) {
                    $queries[] = [
                        'type' => $matches[1],
                        'sql' => $matches[2],
                    ];
                }
            }
            
            // Show most recent first
            $queries = array_reverse($queries);
            // Limit to last 100 queries
            $queries = array_slice($queries, 0, 100);
        }
        
        return $this->render('index', [
            'queries' => $queries,
            'logFile' => $logFile,
        ]);
    }

    /**
     * Clear the SQL log
     */
    public function actionClear()
    {
        $logFile = Yii::getAlias('@runtime/logs/sql-queries.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            Yii::$app->session->setFlash('success', 'SQL log cleared successfully.');
        }
        return $this->redirect(['index']);
    }

    /**
     * Show predefined SQL examples (documentation)
     */
    public function actionExamples()
    {
        return $this->render('examples');
    }
}
