<?php

namespace common\repositories;

/**
 * Doctor Repository - All SQL queries for tbl_doctor table
 * Columns: dr_id, first_name, middle_name, last_name, license_number, dr_fee, dept_id, specialization, certification, email, created_at, updated_at
 */
class DoctorRepository extends BaseRepository
{
    /**
     * SQL: SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id ORDER BY d.last_name
     */
    public function findAll(): array
    {
        $sql = "SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id ORDER BY d.last_name";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id WHERE d.dr_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id WHERE d.dr_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_doctor WHERE email = :email
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM tbl_doctor WHERE email = :email";
        return $this->queryOne($sql, [':email' => $email]);
    }

    /**
     * SQL: INSERT INTO tbl_doctor (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_doctor (first_name, middle_name, last_name, license_number, dr_fee, dept_id, specialization, certification, email) 
                VALUES (:first_name, :middle_name, :last_name, :license_number, :dr_fee, :dept_id, :specialization, :certification, :email)";
        
        return $this->insert($sql, [
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':license_number' => $data['license_number'] ?? null,
            ':dr_fee' => $data['dr_fee'] ?? null,
            ':dept_id' => $data['dept_id'] ?? null,
            ':specialization' => $data['specialization'] ?? null,
            ':certification' => $data['certification'] ?? null,
            ':email' => $data['email'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_doctor SET ... WHERE dr_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_doctor 
                SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name,
                    license_number = :license_number, dr_fee = :dr_fee, dept_id = :dept_id,
                    specialization = :specialization, certification = :certification, email = :email
                WHERE dr_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':license_number' => $data['license_number'] ?? null,
            ':dr_fee' => $data['dr_fee'] ?? null,
            ':dept_id' => $data['dept_id'] ?? null,
            ':specialization' => $data['specialization'] ?? null,
            ':certification' => $data['certification'] ?? null,
            ':email' => $data['email'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_doctor WHERE dr_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_doctor WHERE dr_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_doctor
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_doctor";
        return (int) $this->queryScalar($sql);
    }

    /**
     * SQL: SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id WHERE d.dept_id = :dept_id
     */
    public function findByDepartment(int $deptId): array
    {
        $sql = "SELECT d.*, dept.dept_name FROM tbl_doctor d LEFT JOIN tbl_department dept ON d.dept_id = dept.dept_id WHERE d.dept_id = :dept_id ORDER BY d.last_name";
        return $this->queryAll($sql, [':dept_id' => $deptId]);
    }
}