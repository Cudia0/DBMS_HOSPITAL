<?php

namespace common\repositories;

/**
 * Receptionist Repository - All SQL queries for tbl_receptionist table
 * Columns: recep_id, first_name, middle_name, last_name, email, phone_num, country_code, director_id, created_at, updated_at
 */
class ReceptionistRepository extends BaseRepository
{
    /**
     * SQL: SELECT r.*, d.first_name AS director_fname, d.last_name AS director_lname FROM tbl_receptionist r LEFT JOIN tbl_director d ON r.director_id = d.director_id ORDER BY r.last_name
     */
    public function findAll(): array
    {
        $sql = "SELECT r.*, d.first_name AS director_fname, d.last_name AS director_lname 
                FROM tbl_receptionist r 
                LEFT JOIN tbl_director d ON r.director_id = d.director_id 
                ORDER BY r.last_name";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT r.*, d.first_name AS director_fname, d.last_name AS director_lname FROM tbl_receptionist r LEFT JOIN tbl_director d ON r.director_id = d.director_id WHERE r.recep_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT r.*, d.first_name AS director_fname, d.last_name AS director_lname 
                FROM tbl_receptionist r 
                LEFT JOIN tbl_director d ON r.director_id = d.director_id 
                WHERE r.recep_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_receptionist WHERE email = :email
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM tbl_receptionist WHERE email = :email";
        return $this->queryOne($sql, [':email' => $email]);
    }

    /**
     * SQL: INSERT INTO tbl_receptionist (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_receptionist (first_name, middle_name, last_name, email, phone_num, country_code, director_id) 
                VALUES (:first_name, :middle_name, :last_name, :email, :phone_num, :country_code, :director_id)";
        
        return $this->insert($sql, [
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':email' => $data['email'] ?? null,
            ':phone_num' => $data['phone_num'] ?? null,
            ':country_code' => $data['country_code'] ?? null,
            ':director_id' => $data['director_id'] ?? null,
        ]);
    }

    /**
     * SQL: UPDATE tbl_receptionist SET ... WHERE recep_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_receptionist 
                SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name,
                    email = :email, phone_num = :phone_num, country_code = :country_code, director_id = :director_id
                WHERE recep_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':email' => $data['email'] ?? null,
            ':phone_num' => $data['phone_num'] ?? null,
            ':country_code' => $data['country_code'] ?? null,
            ':director_id' => $data['director_id'] ?? null,
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_receptionist WHERE recep_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_receptionist WHERE recep_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_receptionist
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_receptionist";
        return (int) $this->queryScalar($sql);
    }
}