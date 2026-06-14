<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $queries */
/** @var string $logFile */

$this->title = 'SQL Queries Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sql-index">

    <h1><i class="fas fa-database"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-sync"></i> Refresh', ['index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Clear Log', ['clear'], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Clear all SQL logs?', 'method' => 'post'],
        ]) ?>
        <?= Html::a('<i class="fas fa-book"></i> SQL Examples', ['examples'], ['class' => 'btn btn-info']) ?>
    </p>

    <div class="alert alert-info">
        <strong><i class="fas fa-info-circle"></i> How it works:</strong><br>
        This page shows all SQL queries executed by the system through Yii2 ActiveRecord and raw SQL repositories.<br>
        Log file: <code><?= Html::encode($logFile) ?></code><br>
        <strong>Use the application normally, then come back here to see the SQL commands used.</strong>
    </div>

    <?php if (!empty($queries)): ?>
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">SQL Queries (<?= count($queries) ?> total)</h5>
            <span class="badge bg-info">Last 100 queries shown</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th width="8%">Type</th>
                            <th>SQL Query</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queries as $i => $query): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td>
                                <?php if ($query['type'] === 'query'): ?>
                                    <span class="badge bg-info">SELECT</span>
                                <?php elseif ($query['type'] === 'execute'): ?>
                                    <span class="badge bg-warning text-dark">EXECUTE</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= strtoupper($query['type']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="font-size: 11px; word-break: break-all;"><?= Html::encode($query['sql']) ?></code>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        <strong><i class="fas fa-exclamation-triangle"></i> No SQL queries logged yet!</strong><br>
        Navigate through the application (create patients, book appointments, etc.) to generate SQL queries, then come back here.<br>
        <br>
        <strong>Troubleshooting:</strong>
        <ul class="mb-0">
            <li>Make sure SQL logging is enabled in <code>common/config/main.php</code></li>
            <li>Check that <code>enableLogging</code> and <code>enableProfiling</code> are set to <code>true</code></li>
            <li>Verify the log file path: <code><?= Html::encode($logFile) ?></code></li>
            <li>Ensure the runtime directory is writable</li>
        </ul>
    </div>
    <?php endif; ?>

</div>