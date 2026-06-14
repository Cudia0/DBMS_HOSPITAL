<?php

namespace common\repositories;

use Yii;

/**
 * Base Repository - Provides SQL helper methods
 * All repositories extend this class for raw SQL execution
 */
class BaseRepository
{
    /**
     * Execute SELECT query and return all rows
     */
    protected function queryAll(string $sql, array $params = []): array
    {
        return Yii::$app->db->createCommand($sql, $params)->queryAll();
    }

    /**
     * Execute SELECT query and return one row
     */
    protected function queryOne(string $sql, array $params = []): array|false
    {
        return Yii::$app->db->createCommand($sql, $params)->queryOne();
    }

    /**
     * Execute SELECT query and return single scalar value
     */
    protected function queryScalar(string $sql, array $params = []): mixed
    {
        return Yii::$app->db->createCommand($sql, $params)->queryScalar();
    }

    /**
     * Execute INSERT/UPDATE/DELETE query
     */
    protected function execute(string $sql, array $params = []): int
    {
        return Yii::$app->db->createCommand($sql, $params)->execute();
    }

    /**
     * Execute INSERT and return last insert ID
     */
    protected function insert(string $sql, array $params = []): string
    {
        Yii::$app->db->createCommand($sql, $params)->execute();
        return Yii::$app->db->getLastInsertID();
    }
}