<?php
require_once __DIR__ . '/../config/config.php';

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Security: HttpOnly, SameSite=Lax, strict mode
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_strict_mode', '1');
            
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public static function username(): ?string
    {
        return $_SESSION['username'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . APP_URL . '/public/');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (self::role() !== $role) {
            header('Location: ' . APP_URL . '/public/');
            exit;
        }
    }

    public static function redirectIfLoggedIn(): void
    {
        if (!self::isLoggedIn()) return;

        if (self::role() === 'admin') {
            header('Location: ' . APP_URL . '/public/admin/');
        } else {
            header('Location: ' . APP_URL . '/public/student/');
        }
        exit;
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
        header('Location: ' . APP_URL . '/public/');
        exit;
    }

    public static function setFlash(string $key, mixed $message): void
    {
        self::start();
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): mixed
    {
        self::start();
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}
