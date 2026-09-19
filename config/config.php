<?php
// ============================================================
//  PAYTRACK — Application Configuration
//  Edit these values to match your environment.
// ============================================================

// ------------------------------------------------------------
//  Application
// ------------------------------------------------------------
define('APP_NAME',    'Paytrack');

// Auto-detect APP_URL dynamically so assets and links work regardless of folder name
if (!defined('APP_URL')) {
    if (!empty($_SERVER['HTTP_HOST'])) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host     = $_SERVER['HTTP_HOST'];
        $basePath = '';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
            if (preg_match('#^(.*?)/public(?:/|$)#', $script, $m)) {
                $basePath = $m[1];
            } else {
                $docRoot  = !empty($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
                $projRoot = str_replace('\\', '/', realpath(dirname(__DIR__)));
                if ($docRoot && strpos($projRoot, $docRoot) === 0) {
                    $basePath = substr($projRoot, strlen($docRoot));
                }
            }
        }
        define('APP_URL', rtrim($protocol . $host . $basePath, '/'));
    } else {
        define('APP_URL', 'http://localhost/' . basename(dirname(__DIR__)));
    }
}

define('APP_VERSION', '1.0.0');

// ------------------------------------------------------------
//  Database  (MariaDB via XAMPP)
// ------------------------------------------------------------
define('DB_HOST',     'localhost');
define('DB_NAME',     'paytrack');
define('DB_USER',     'root');
define('DB_PASS',     '');          // Default XAMPP MariaDB has no password
define('DB_CHARSET',  'utf8mb4');

// ------------------------------------------------------------
//  PHPMailer / SMTP Configuration
//  To send real emails via Gmail:
//    1. Turn ON 2-Step Verification in your Google Account.
//    2. Generate an "App Password" (16 characters, e.g. "abcd efgh ijkl mnop").
//    3. Set MAIL_USER to your Gmail and MAIL_PASS to your 16-char App Password.
//  Or use Mailtrap / Mailpit for local testing.
// ------------------------------------------------------------
define('MAIL_HOST',   'smtp.gmail.com');
define('MAIL_PORT',   587);
define('MAIL_USER',   'ncstenrollment@gmail.com');
define('MAIL_PASS',   'vuil adnd hkjp cxxf');        // 16-character Google App Password
define('MAIL_FROM',   'ncstenrollment@gmail.com');
define('MAIL_NAME',   'PayTrack System');

// ------------------------------------------------------------
//  Session
// ------------------------------------------------------------
define('SESSION_NAME', 'paytrack_session');
