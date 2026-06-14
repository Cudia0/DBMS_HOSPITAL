<?php

namespace common\repositories;

/**
 * Department Repository - All SQL queries for tbl_department table
 * Columns: dept_id, dept_name, operating_days, office_hours, created_at, updated_at
 */
class DepartmentRepository extends BaseRepository
{
    /**
     * SQL: SELECT * FROM tbl_department ORDER BY dept_name
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM tbl_department ORDER BY dept_name";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT * FROM tbl_department WHERE dept_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM tbl_department WHERE dept_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_department (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_department (dept_name, operating_days, office_hours) VALUES (:dept_name, :operating_days, :office_hours)";
        
        return $this->insert($sql, [
            ':dept_name' => $data['dept_name'],
            ':operating_days' => $data['operating_days'] ?? null,
            ':office_hours' => $data['office_hours'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_department SET ... WHERE dept_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_department SET dept_name = :dept_name, operating_days = :operating_days, office_hours = :office_hours WHERE dept_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':dept_name' => $data['dept_name'],
            ':operating_days' => $data['operating_days'] ?? null,
            ':office_hours' => $data['office_hours'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_department WHERE dept_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_department WHERE dept_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_department
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_department";
        return (int) $this->queryScalar($sql);
    }
}