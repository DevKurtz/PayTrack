<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Student.php';
require_once __DIR__ . '/../../models/TuitionFee.php';
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../controllers/StudentController.php';

Auth::start();
Auth::requireRole('student');

$userId = Auth::userId();
$student = Student::findByUserId($userId);

// Routing view: 'home' (default), 'fees', 'history', 'receipts', 'password'
$currentView = $_GET['view'] ?? 'home';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'pay_tuition') {
        StudentController::payTuition();
    } elseif ($action === 'change_password') {
        StudentController::updatePassword();
    }
}

// Fetch Student Fee Records (with breakdown items)
$fees = $student ? TuitionFee::getByStudentId($student['id']) : [];

$totalFees = 0;
$totalPaid = 0;
foreach ($fees as $f) {
    $totalFees += (float) $f['total_amount'];
    $totalPaid += (float) $f['amount_paid'];
}
$totalRemaining = max(0, $totalFees - $totalPaid);

// Fetch Student Payments History
$payments = $student ? Payment::getByStudentId($student['id']) : [];

// Flatten fee items across all tuition records for search breakdown
$breakdownItems = [];
foreach ($fees as $f) {
    foreach ($f['items'] ?? [] as $item) {
        $breakdownItems[] = $item;
    }
}

$paymentSuccess = Auth::getFlash('payment_success');
$successMsg = Auth::getFlash('success');
$errorMsg = Auth::getFlash('error');

// Direct Standalone Printable Official Receipt View
if ($currentView === 'receipt') {
    $orNumber = trim($_GET['or'] ?? '');
    $selectedPayment = null;
    foreach ($payments as $p) {
        if ($p['or_number'] === $orNumber) {
            $selectedPayment = $p;
            break;
        }
    }
    if (!$selectedPayment && !empty($payments)) {
        $selectedPayment = $payments[0];
    }
    require_once __DIR__ . '/../../views/student/receipt.php';
    exit;
}

require_once __DIR__ . '/../../views/student/dashboard.php';
