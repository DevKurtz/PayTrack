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

        // Security: Inactivity session timeout (1 hour = 3600 seconds)
        if (self::isLoggedIn()) {
            $now = time();
            $maxIdle = 3600; // 60 minutes
            if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > $maxIdle)) {
                self::logout();
            }
            $_SESSION['last_activity'] = $now;

            // Record user agent (do not invalidate on mobile devtools toggle to allow seamless testing)
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if (!isset($_SESSION['user_agent'])) {
                $_SESSION['user_agent'] = $userAgent;
            }

            // Real-time online activity tracking in DB (throttled to every 30s)
            if (!isset($_SESSION['last_db_active']) || ($now - $_SESSION['last_db_active'] > 30)) {
                $_SESSION['last_db_active'] = $now;
                try {
                    require_once __DIR__ . '/../config/database.php';
                    $db = Database::getInstance();
                    $stmt = $db->prepare('UPDATE users SET last_active_at = NOW() WHERE id = ?');
                    $stmt->execute([self::userId()]);
                } catch (Exception $e) {
                    // Fail silently
                }
            }
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
        } elseif (self::role() === 'accounting') {
            header('Location: ' . APP_URL . '/public/accounting/');
        } else {
            header('Location: ' . APP_URL . '/public/student/');
        }
        exit;
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
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
