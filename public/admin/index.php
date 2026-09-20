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
        case 'assign_fee':
            AdminController::assignFee();
            break;
        case 'save_fee_category':
            AdminController::saveFeeCategory();
            break;
        case 'delete_fee_category':
            AdminController::deleteFeeCategory();
            break;
        case 'delete_student':
            AdminController::deleteStudent();
            break;
        case 'delete_fee':
            AdminController::deleteFee();
            break;
    }
}

// Routing view: 'home' (students), 'transactions', 'fees', 'categories', 'logs'
$currentView = $_GET['view'] ?? 'home';

$students = Student::all();
$fees = TuitionFee::all();
$payments = Payment::all();
$feeCategories = FeeCategory::all();
$fixedFeeTotal = FeeCategory::getFixedTotal();
$categoryCount = count($feeCategories);

// Compute Summary Metrics for Home Dashboard
$totalRevenue = 0;
foreach ($payments as $p) {
    $totalRevenue += (float) ($p['amount'] ?? 0);
}
$totalAssessed = 0;
foreach ($fees as $f) {
    $totalAssessed += (float) ($f['total_amount'] ?? 0);
}
$totalReceivables = max(0, $totalAssessed - $totalRevenue);

// Query email logs
$db = Database::getInstance();
$emailLogs = $db->query("SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 50")->fetchAll();

$successMsg = Auth::getFlash('success');
$errorMsg = Auth::getFlash('error');
$createdCreds = Auth::getFlash('created_student_credentials');

require_once __DIR__ . '/../../views/admin/dashboard.php';
