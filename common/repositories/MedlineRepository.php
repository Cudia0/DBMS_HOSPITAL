<?php

namespace common\repositories;

/**
 * Medline Repository - All SQL queries for tbl_medline table
 * Columns: medline_id, prescription_id, med_id, qty, dosage_per_intake, frequency, created_at
 */
class MedlineRepository extends BaseRepository
{
    /**
     * SQL: SELECT ml.*, m.med_name, m.strength, m.med_price FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.prescription_id = :prescription_id
     */
    public function findByPrescription(int $prescriptionId): array
    {
        $sql = "SELECT ml.*, m.med_name, m.strength, m.med_price 
                FROM tbl_medline ml 
                JOIN tbl_medicine m ON ml.med_id = m.med_id 
                WHERE ml.prescription_id = :prescription_id";
        return $this->queryAll($sql, [':prescription_id' => $prescriptionId]);
    }

    /**
     * SQL: INSERT INTO tbl_medline (prescription_id, med_id, qty, dosage_per_intake, frequency) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_medline (prescription_id, med_id, qty, dosage_per_intake, frequency) 
                VALUES (:prescription_id, :med_id, :qty, :dosage_per_intake, :frequency)";
        
        return $this->insert($sql, [
            ':prescription_id' => $data['prescription_id'],
            ':med_id' => $data['med_id'],
            ':qty' => $data['qty'],
            ':dosage_per_intake' => $data['dosage_per_intake'] ?? null,
            ':frequency' => $data['frequency'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_medline WHERE prescription_id = :prescription_id
     */
    public function deleteByPrescription(int $prescriptionId): int
    {
        $sql = "DELETE FROM tbl_medline WHERE prescription_id = :prescription_id";
        return $this->execute($sql, [':prescription_id' => $prescriptionId]);
    }

    /**
     * SQL: DELETE FROM tbl_medline WHERE medline_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_medline WHERE medline_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT ml.*, m.med_name, m.strength FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.medline_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT ml.*, m.med_name, m.strength FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.medline_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT ml.*, m.med_name FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id ORDER BY ml.created_at DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT ml.*, m.med_name, m.strength FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id ORDER BY ml.created_at DESC";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT SUM(m.med_price * ml.qty) FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.prescription_id = :prescription_id
     */
    public function getTotalPrice(int $prescriptionId): float
    {
        $sql = "SELECT COALESCE(SUM(m.med_price * ml.qty), 0) FROM tbl_medline ml JOIN tbl_medicine m ON ml.med_id = m.med_id WHERE ml.prescription_id = :prescription_id";
        return (float) $this->queryScalar($sql, [':prescription_id' => $prescriptionId]);
    }
}