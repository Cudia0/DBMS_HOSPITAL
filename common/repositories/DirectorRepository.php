<?php

namespace common\repositories;

/**
 * Director Repository - All SQL queries for tbl_director table
 * Columns: director_id, first_name, middle_name, last_name, phone_num, country_code, email, created_at, updated_at
 */
class DirectorRepository extends BaseRepository
{
    /**
     * SQL: SELECT * FROM tbl_director ORDER BY last_name
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM tbl_director ORDER BY last_name";
        return $this->queryAll($sql);
    }

    /**
     * SQL: SELECT * FROM tbl_director WHERE director_id = :id
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM tbl_director WHERE director_id = :id";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT * FROM tbl_director WHERE email = :email
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM tbl_director WHERE email = :email";
        return $this->queryOne($sql, [':email' => $email]);
    }

    /**
     * SQL: INSERT INTO tbl_director (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO tbl_director (first_name, middle_name, last_name, phone_num, country_code, email) 
                VALUES (:first_name, :middle_name, :last_name, :phone_num, :country_code, :email)";
        
        return $this->insert($sql, [
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':phone_num' => $data['phone_num'] ?? null,
            ':country_code' => $data['country_code'] ?? null,
            ':email' => $data['email'],
        ]);
    }

    /**
     * SQL: UPDATE tbl_director SET ... WHERE director_id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE tbl_director 
                SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name,
                    phone_num = :phone_num, country_code = :country_code, email = :email
                WHERE director_id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':phone_num' => $data['phone_num'] ?? null,
            ':country_code' => $data['country_code'] ?? null,
            ':email' => $data['email'],
        ]);
    }

    /**
     * SQL: DELETE FROM tbl_director WHERE director_id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM tbl_director WHERE director_id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * SQL: SELECT COUNT(*) FROM tbl_director
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM tbl_director";
        return (int) $this->queryScalar($sql);
    }
}