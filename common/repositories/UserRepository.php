<?php

namespace common\repositories;

/**
 * User Repository - All SQL queries for user table
 */
class UserRepository extends BaseRepository
{
    /**
     * Find user by username (active only)
     * SQL: SELECT * FROM user WHERE username = :username AND status = 10
     */
    public function findByUsername(string $username): array|false
    {
        $sql = "SELECT * FROM user WHERE username = :username AND status = 10";
        return $this->queryOne($sql, [':username' => $username]);
    }

    /**
     * Find user by ID (active only)
     * SQL: SELECT * FROM user WHERE id = :id AND status = 10
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM user WHERE id = :id AND status = 10";
        return $this->queryOne($sql, [':id' => $id]);
    }

    /**
     * Find user by email
     * SQL: SELECT * FROM user WHERE email = :email
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM user WHERE email = :email";
        return $this->queryOne($sql, [':email' => $email]);
    }

    /**
     * Create new user
     * SQL: INSERT INTO user (...) VALUES (...)
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO user (username, auth_key, password_hash, email, status, verification_token, created_at, updated_at) 
                VALUES (:username, :auth_key, :password_hash, :email, :status, :verification_token, :created_at, :updated_at)";
        
        return $this->insert($sql, [
            ':username' => $data['username'],
            ':auth_key' => $data['auth_key'],
            ':password_hash' => $data['password_hash'],
            ':email' => $data['email'],
            ':status' => $data['status'],
            ':verification_token' => $data['verification_token'] ?? null,
            ':created_at' => $data['created_at'],
            ':updated_at' => $data['updated_at'],
        ]);
    }

    /**
     * Update user
     * SQL: UPDATE user SET ... WHERE id = :id
     */
    public function update(int $id, array $data): int
    {
        $sql = "UPDATE user SET username = :username, email = :email, updated_at = :updated_at WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':updated_at' => $data['updated_at'],
        ]);
    }

    /**
     * Update user password
     * SQL: UPDATE user SET password_hash = :password_hash, updated_at = :updated_at WHERE id = :id
     */
    public function updatePassword(int $id, string $passwordHash, int $updatedAt): int
    {
        $sql = "UPDATE user SET password_hash = :password_hash, updated_at = :updated_at WHERE id = :id";
        return $this->execute($sql, [':id' => $id, ':password_hash' => $passwordHash, ':updated_at' => $updatedAt]);
    }

    /**
     * Activate user (set status = 10)
     * SQL: UPDATE user SET status = 10, updated_at = :updated_at WHERE id = :id
     */
    public function activate(int $id, int $updatedAt): int
    {
        $sql = "UPDATE user SET status = 10, updated_at = :updated_at WHERE id = :id";
        return $this->execute($sql, [':id' => $id, ':updated_at' => $updatedAt]);
    }

    /**
     * Deactivate user (set status = 9)
     * SQL: UPDATE user SET status = 9, updated_at = :updated_at WHERE id = :id
     */
    public function deactivate(int $id, int $updatedAt): int
    {
        $sql = "UPDATE user SET status = 9, updated_at = :updated_at WHERE id = :id";
        return $this->execute($sql, [':id' => $id, ':updated_at' => $updatedAt]);
    }

    /**
     * Delete user
     * SQL: DELETE FROM user WHERE id = :id
     */
    public function delete(int $id): int
    {
        $sql = "DELETE FROM user WHERE id = :id";
        return $this->execute($sql, [':id' => $id]);
    }

    /**
     * Get all users
     * SQL: SELECT * FROM user ORDER BY created_at DESC
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM user ORDER BY created_at DESC";
        return $this->queryAll($sql);
    }

    /**
     * Check if username exists (excluding current user)
     * SQL: SELECT COUNT(*) FROM user WHERE username = :username AND id != :exclude_id
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM user WHERE username = :username";
        $params = [':username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        return (int) $this->queryScalar($sql, $params) > 0;
    }

    /**
     * Find user by password reset token
     * SQL: SELECT * FROM user WHERE password_reset_token = :token AND status = 10
     */
    public function findByPasswordResetToken(string $token): array|false
    {
        $sql = "SELECT * FROM user WHERE password_reset_token = :token AND status = 10";
        return $this->queryOne($sql, [':token' => $token]);
    }

    /**
     * Find user by verification token (inactive)
     * SQL: SELECT * FROM user WHERE verification_token = :token AND status = 9
     */
    public function findByVerificationToken(string $token): array|false
    {
        $sql = "SELECT * FROM user WHERE verification_token = :token AND status = 9";
        return $this->queryOne($sql, [':token' => $token]);
    }
}