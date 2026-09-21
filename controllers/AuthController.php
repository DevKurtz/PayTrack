<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';

class AuthController
{
    public static function login(): void
    {
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Basic validation
        if ($username === '' || $password === '') {
            Auth::setFlash('error', 'Please enter your username and password.');
            Auth::setFlash('last_username', $username);
            redirect(APP_URL . '/public/');
        }

        $user = User::findByUsername($username);

        $isPasswordValid = false;
        if ($user) {
            // 1. Direct standard hash verification
            if (password_verify($password, $user['password_hash'])) {
                $isPasswordValid = true;
            } elseif ($user['role'] === 'student') {
                // 2. Case-insensitive & whitespace-stripped check for default student password (e.g. "Dela Cruz" / "delacruz" -> "DELACRUZ")
                $cleanUpper = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($password));
                if (password_verify($cleanUpper, $user['password_hash'])) {
                    $isPasswordValid = true;
                }

                // 3. Verify against student profile record (literal last name)
                if (!$isPasswordValid) {
                    $student = Student::findByUserId($user['id']);
                    if ($student) {
                        $expectedLast = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($student['last_name']));
                        if ($cleanUpper === $expectedLast) {
                            $isPasswordValid = true;
                        }

                        // Backward-compatible check for studentId + lastName
                        if (!$isPasswordValid) {
                            $combined = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($student['student_id'] . $student['last_name']));
                            if ($cleanUpper === $combined) {
                                $isPasswordValid = true;
                            }
                        }
                    }
                }
            }
        }

        if (!$user || !$isPasswordValid) {
            Auth::setFlash('error', 'Invalid username or password.');
            Auth::setFlash('last_username', $username);
            redirect(APP_URL . '/public/');
        }

        // Check account status
        if (!empty($user['status']) && $user['status'] === 'suspended') {
            Auth::setFlash('error', 'Your account has been suspended. Please contact the administrator.');
            Auth::setFlash('last_username', $username);
            redirect(APP_URL . '/public/');
        }

        if (!empty($user['status']) && $user['status'] === 'inactive') {
            Auth::setFlash('error', 'Your account is currently inactive.');
            Auth::setFlash('last_username', $username);
            redirect(APP_URL . '/public/');
        }

        // Record last login & activity
        User::updateLastLogin($user['id']);

        // Security: Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Set session
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['username']      = $user['username'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['last_activity'] = time();
        $_SESSION['user_agent']    = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Dynamic Role-Based Redirection
        if ($user['role'] === 'admin') {
            redirect(APP_URL . '/public/admin/');
        } elseif ($user['role'] === 'accounting') {
            redirect(APP_URL . '/public/accounting/');
        } else {
            redirect(APP_URL . '/public/student/');
        }
    }

    public static function logout(): void
    {
        Auth::logout();
    }
}
