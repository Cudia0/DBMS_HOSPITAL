<?php

namespace common\repositories;

/**
 * Patient Repository - All SQL queries for tbl_patient table
 * Columns: patient_id, first_name, middle_name, last_name, sex, date_of_birth, phone_num, country_code, email, address, created_at, updated_at
 */
class PatientRepository extends BaseRepository
{
    /**
     * SQL: SELECT * FROM tbl_patient ORDER BY last_name, first_name
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM tbl_patient ORDER BY last_name, first_name";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT * FROM tbl_patient WHERE patient_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM tbl_patient WHERE patient_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_patient WHERE email = :email
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM tbl_patient WHERE email = :email";
        return $this->queryOne($sql, [':email' => $email]);
    }

    /**
     * SQL: INSERT INTO tbl_patient (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_patient (first_name, middle_name, last_name, sex, date_of_birth, phone_num, country_code, email, address) 
                VALUES (:first_name, :middle_name, :last_name, :sex, :date_of_birth, :phone_num, :country_code, :email, :address)";
        
        return $this->insert($sql, [
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':sex' => $data['sex'] ?? null,
            ':date_of_birth' => $data['date_of_birth'] ?? null,
            ':phone_num' => $data['phone_num'] ?? null,
            ':country_code' => $data['country_code'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_patient SET ... WHERE patient_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_patient 
                SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name,
                    sex = :sex, date_of_birth = :date_of_birth, phone_num = :phone_num,
                    country_code = :country_code, email = :email, address = :address
                WHERE patient_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':sex' => $data['sex'] ?? null,
            ':date_of_birth' => $data['date_of_birth'] ?? null,
            ':phone_num' => $data['phone_num'] ?? null,
            ':country_code' => $data['country_code'] ?? null,
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_patient WHERE patient_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_patient WHERE patient_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_patient
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_patient";
        return (int) $this->queryScalar($sql);
    }

    /**
     * SQL: SELECT * FROM tbl_patient WHERE first_name LIKE :search OR last_name LIKE :search2 ORDER BY last_name
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM tbl_patient WHERE first_name LIKE :search OR last_name LIKE :search2 ORDER BY last_name, first_name";
        return $this->queryAll($sql, [':search' => "%$query%", ':search2' => "%$query%"]);
    }

    /**
     * SQL: SELECT * FROM tbl_patient WHERE first_name = :fn AND last_name = :ln AND date_of_birth = :dob
     */
    public function findDuplicate(string $firstName, string $lastName, string $dob, ?int $excludeId = null): array|false
    {
        $sql = "SELECT * FROM tbl_patient WHERE first_name = :fn AND last_name = :ln AND date_of_birth = :dob";
        $params = [':fn' => $firstName, ':ln' => $lastName, ':dob' => $dob];
        
        if ($excludeId) {
            $sql .= " AND patient_id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        return $this->queryOne($sql, $params);
    }

    /**
     * SQL: SELECT * FROM tbl_patient WHERE phone_num = :phone AND country_code = :code
     */
    public function findByPhone(string $phone, string $countryCode, ?int $excludeId = null): array|false
    {
        $sql = "SELECT * FROM tbl_patient WHERE phone_num = :phone AND country_code = :code";
        $params = [':phone' => $phone, ':code' => $countryCode];
        
        if ($excludeId) {
            $sql .= " AND patient_id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        return $this->queryOne($sql, $params);
    }

    /**
     * SQL: SELECT * FROM tbl_patient ORDER BY created_at DESC LIMIT :limit
     */
    public function findRecent(int $limit = 5): array
    {
        $sql = "SELECT * FROM tbl_patient ORDER BY created_at DESC LIMIT :limit";
        return $this->queryAll($sql, [':limit' => $limit]);
    }
}