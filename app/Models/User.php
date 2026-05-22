<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User
{
    public function __construct(private readonly Database $db) {}

    public function getById(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT id, name, email, role, is_active, last_login_at, created_at, updated_at FROM users WHERE id = ?',
            [$id]
        );
    }

    public function getByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM users WHERE email = ?',
            [$email]
        );
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            'SELECT id, name, email, role, is_active, last_login_at, created_at FROM users ORDER BY name ASC'
        );
    }

    public function create(array $data): int
    {
        return $this->db->insert('users', [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'     => $data['role'] ?? 'editor',
            'is_active'=> $data['is_active'] ?? 1,
        ]);
    }

    public function update(int $id, array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('users', 'id = ?', [$id]);
    }

    public function updateLastLogin(int $id): void
    {
        $this->db->query(
            'UPDATE users SET last_login_at = NOW() WHERE id = ?',
            [$id]
        );
    }

    public function verifyPassword(string $email, string $password): ?array
    {
        $user = $this->getByEmail($email);
        if (!$user || !$user['is_active']) {
            return null;
        }
        if (!password_verify($password, $user['password'])) {
            return null;
        }
        return $user;
    }
}
