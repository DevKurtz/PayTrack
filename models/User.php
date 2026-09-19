<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    public static function findByUsername(string $username): ?array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $username, string $password, string $role = 'student'): int
    {
        $db   = Database::getInstance();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            'INSERT INTO users (username, password_hash, role, is_first_login) VALUES (?, ?, ?, 1)'
        );
        $stmt->execute([$username, $hash, $role]);
        return (int) $db->lastInsertId();
    }

    public static function updatePassword(int $userId, string $newPassword): void
    {
        $db   = Database::getInstance();
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            'UPDATE users SET password_hash = ?, is_first_login = 0 WHERE id = ?'
        );
        $stmt->execute([$hash, $userId]);
    }
}
