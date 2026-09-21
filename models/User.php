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

    public static function create(string $username, string $password, string $role = 'student', string $name = '', string $email = ''): int
    {
        $db   = Database::getInstance();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            'INSERT INTO users (username, password_hash, role, name, email, is_first_login) VALUES (?, ?, ?, ?, ?, 1)'
        );
        $stmt->execute([$username, $hash, $role, $name, $email]);
        return (int) $db->lastInsertId();
    }

    public static function findFirstByRole(string $role): ?array
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM users WHERE role = ? ORDER BY id ASC LIMIT 1');
        $stmt->execute([$role]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function createAccounting(string $username, string $name, string $email, string $password): int
    {
        $db   = Database::getInstance();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            'INSERT INTO users (username, name, email, password_hash, role, is_first_login, status, created_at)
             VALUES (?, ?, ?, ?, "accounting", 0, "active", NOW())'
        );
        $stmt->execute([$username, $name, $email, $hash]);
        return (int) $db->lastInsertId();
    }

    public static function getAllWithActivity(): array
    {
        $db = Database::getInstance();
        $sql = "
            SELECT 
                u.id,
                u.username,
                u.name,
                u.email,
                u.role,
                u.status,
                u.last_login_at,
                u.last_active_at,
                u.created_at,
                s.student_id,
                s.first_name,
                s.last_name,
                s.grade_level,
                s.school_year,
                s.email as student_email
            FROM users u
            LEFT JOIN students s ON u.id = s.user_id
            ORDER BY u.id DESC
        ";
        $users = $db->query($sql)->fetchAll();
        $now = time();

        foreach ($users as &$u) {
            // Display Name
            if ($u['role'] === 'student' && !empty($u['first_name'])) {
                $u['display_name'] = trim("{$u['first_name']} {$u['last_name']}");
                $u['contact_email'] = $u['student_email'] ?? $u['email'];
            } else {
                $u['display_name'] = !empty($u['name']) ? $u['name'] : ucfirst($u['username']);
                $u['contact_email'] = $u['email'] ?? '—';
            }

            // Online status & Days online computation
            $lastActiveTs = !empty($u['last_active_at']) ? strtotime($u['last_active_at']) : null;
            if ($lastActiveTs) {
                $diffSec = $now - $lastActiveTs;
                $u['is_online'] = ($diffSec <= 300); // within 5 minutes
                $diffDays = (int) floor($diffSec / 86400);

                if ($u['is_online']) {
                    $u['online_badge'] = 'Online Now';
                    $u['days_online_text'] = 'Active now';
                } elseif ($diffDays === 0) {
                    $diffHours = max(1, (int) floor($diffSec / 3600));
                    $u['online_badge'] = 'Offline';
                    $u['days_online_text'] = "Active {$diffHours}h ago";
                } elseif ($diffDays === 1) {
                    $u['online_badge'] = 'Offline';
                    $u['days_online_text'] = 'Active 1 day ago';
                } else {
                    $u['online_badge'] = 'Offline';
                    $u['days_online_text'] = "Active {$diffDays} days ago";
                }
            } else {
                $u['is_online'] = false;
                $u['online_badge'] = 'Offline';
                $u['days_online_text'] = 'Never logged in';
            }
        }
        unset($u);

        return $users;
    }

    public static function updateLastLogin(int $userId): void
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare('UPDATE users SET last_login_at = NOW(), last_active_at = NOW() WHERE id = ?');
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            // Fail silently
        }
    }

    public static function updateStatus(int $userId, string $status): void
    {
        $allowed = ['active', 'inactive', 'suspended'];
        if (!in_array($status, $allowed)) return;

        $db = Database::getInstance();
        $stmt = $db->prepare('UPDATE users SET status = ? WHERE id = ?');
        $stmt->execute([$status, $userId]);
    }

    public static function deleteUser(int $userId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$userId]);
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
