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
require_once __DIR__ . '/../../controllers/AccountingController.php';

Auth::start();
Auth::requireRole('accounting');

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'assign_tuition':
            AccountingController::assignTuitionFee();
            break;
        case 'record_payment':
            AccountingController::recordManualPayment();
            break;
        case 'save_fee_category':
            AccountingController::saveFeeCategory();
            break;
        case 'delete_fee_category':
            AccountingController::deleteFeeCategory();
            break;
        case 'delete_fee':
            AccountingController::deleteFee();
            break;
    }
}

// Routing view: 'home' (default), 'students', 'transactions', 'fees', 'categories', 'logs'
$currentView = $_GET['view'] ?? 'home';

$students = Student::all();
$fees = TuitionFee::all();
$payments = Payment::all();
$feeCategories = FeeCategory::allActive();

// Check assessment status for each student
$pendingAssessmentCount = 0;
foreach ($students as &$s) {
    $sFees = TuitionFee::getByStudentId($s['id']);
    $s['fees_count'] = count($sFees);
    $s['has_assessment'] = !empty($sFees);
    $s['primary_fee'] = !empty($sFees) ? $sFees[0] : null;
    if (!$s['has_assessment']) {
        $pendingAssessmentCount++;
    }
}
unset($s);

// Compute Financial Summary Metrics
$totalRevenue = 0;
foreach ($payments as $p) {
    $totalRevenue += (float) ($p['amount'] ?? 0);
}
$totalAssessed = 0;
foreach ($fees as $f) {
    $totalAssessed += (float) ($f['total_amount'] ?? 0);
}
$totalReceivables = max(0, $totalAssessed - $totalRevenue);
$collectionRate = ($totalAssessed > 0) ? min(100, round(($totalRevenue / $totalAssessed) * 100, 1)) : 0;

// Query email logs
$db = Database::getInstance();
$emailLogs = $db->query("SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 60")->fetchAll();

$successMsg = Auth::getFlash('success');
$errorMsg = Auth::getFlash('error');

require_once __DIR__ . '/../../views/accounting/dashboard.php';
