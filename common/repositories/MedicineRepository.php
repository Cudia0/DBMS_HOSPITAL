<?php

namespace common\repositories;

/**
 * Medicine Repository - All SQL queries for tbl_medicine table
 * Columns: med_id, med_name, dosage_form, strength, med_price, created_at, updated_at
 */
class MedicineRepository extends BaseRepository
{
    /**
     * SQL: SELECT * FROM tbl_medicine ORDER BY med_name
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM tbl_medicine ORDER BY med_name";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT * FROM tbl_medicine WHERE med_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM tbl_medicine WHERE med_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: INSERT INTO tbl_medicine (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_medicine (med_name, dosage_form, strength, med_price) VALUES (:med_name, :dosage_form, :strength, :med_price)";
        
        return $this->insert($sql, [
            ':med_name' => $data['med_name'],
            ':dosage_form' => $data['dosage_form'] ?? null,
            ':strength' => $data['strength'] ?? null,
            ':med_price' => $data['med_price'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_medicine SET ... WHERE med_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_medicine SET med_name = :med_name, dosage_form = :dosage_form, strength = :strength, med_price = :med_price WHERE med_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':med_name' => $data['med_name'],
            ':dosage_form' => $data['dosage_form'] ?? null,
            ':strength' => $data['strength'] ?? null,
            ':med_price' => $data['med_price'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_medicine WHERE med_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_medicine WHERE med_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_medicine
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_medicine";
        return (int) $this->queryScalar($sql);
    }

    /**
     * SQL: SELECT * FROM tbl_medicine WHERE med_name LIKE :name ORDER BY med_name
     */
    public function searchByName(string $name): array
    {
        $sql = "SELECT * FROM tbl_medicine WHERE med_name LIKE :name ORDER BY med_name";
        return $this->queryAll($sql, [':name' => "%$name%"]);
    }

    /**
     * SQL: SELECT * FROM tbl_medicine WHERE med_name = :name AND strength = :strength
     */
    public function findDuplicate(string $name, string $strength, ?int $excludeId = null): array|false
    {
        $sql = "SELECT * FROM tbl_medicine WHERE med_name = :name AND strength = :strength";
        $params = [':name' => $name, ':strength' => $strength];
        
        if ($excludeId) {
            $sql .= " AND med_id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        return $this->queryOne($sql, $params);
    }
}