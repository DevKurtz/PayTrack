<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Student.php';
require_once __DIR__ . '/../../models/FeeCategory.php';
require_once __DIR__ . '/../../models/TuitionFee.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

Auth::start();
Auth::requireRole('admin');

// Handle POST actions with CSRF check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'create_student':
            AdminController::createStudent();
            break;
        case 'create_accounting':
            AdminController::createAccounting();
            break;
        case 'delete_user':
            AdminController::deleteUser();
            break;
        case 'update_status':
            AdminController::updateUserStatus();
            break;
    }
}

// Routing view: 'home' (default), 'users', 'logs'
$currentView = $_GET['view'] ?? 'home';
$allowedViews = ['home', 'users', 'logs'];
if (!in_array($currentView, $allowedViews, true)) {
    $currentView = 'home';
}

// Fetch users with live activity and days online tracking
$allUsers = User::getAllWithActivity();

// Summary Metrics
$totalUsers = count($allUsers);
$studentCount = 0;
$accountingCount = 0;
$adminCount = 0;
$onlineCount = 0;
$activeWeekCount = 0;
$now = time();

foreach ($allUsers as $u) {
    if ($u['role'] === 'student') $studentCount++;
    if ($u['role'] === 'accounting') $accountingCount++;
    if ($u['role'] === 'admin') $adminCount++;
    if (!empty($u['is_online'])) $onlineCount++;

    if (!empty($u['last_active_at']) && ($now - strtotime($u['last_active_at']) <= 7 * 86400)) {
        $activeWeekCount++;
    }
}

// Query system-wide email logs
$db = Database::getInstance();
$emailLogs = $db->query("SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 100")->fetchAll();
$totalLogsCount = count($emailLogs);

$successMsg = Auth::getFlash('success');
$errorMsg = Auth::getFlash('error');
$createdCreds = Auth::getFlash('created_student_credentials');

require_once __DIR__ . '/../../views/admin/dashboard.php';

