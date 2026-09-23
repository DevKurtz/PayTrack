<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/TuitionFee.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../config/mailer.php';

class StudentController
{
    public static function payTuition(): void
    {
        Auth::requireRole('student');
        verify_csrf();

        $userId = Auth::userId();
        $student = Student::findByUserId($userId);
        if (!$student) redirect(APP_URL . '/public/student/');

        $feeId     = (int) ($_POST['fee_id'] ?? 0);
        $rawAmount = trim((string) ($_POST['amount'] ?? ''));
        $rawMethod = trim($_POST['payment_method'] ?? 'online');
        $allowedMethods = ['online', 'gcash', 'maya', 'bank_transfer', 'card'];
        $method = in_array($rawMethod, $allowedMethods) ? $rawMethod : 'online';

        if (!is_numeric($rawAmount) || (float)$rawAmount <= 0) {
            Auth::setFlash('error', 'Please enter a valid positive payment amount.');
            redirect(APP_URL . '/public/student/');
        }

        $amount = round((float)$rawAmount, 2);

        if ($feeId <= 0) {
            // Auto fallback to student's latest active tuition fee if fee_id missing
            $latestFee = TuitionFee::findLatestByStudentId($student['id']);
            $feeId = $latestFee ? (int)$latestFee['id'] : 0;
        }

        $fee = $feeId > 0 ? TuitionFee::findById($feeId) : null;
        if (!$fee || (int)$fee['student_id'] !== (int)$student['id']) {
            Auth::setFlash('error', 'Invalid or unassigned tuition fee record.');
            redirect(APP_URL . '/public/student/');
        }

        $remaining = round(max(0, (float)$fee['total_amount'] - (float)$fee['amount_paid']), 2);
        if ($remaining <= 0) {
            Auth::setFlash('error', 'Your tuition assessment is already fully settled. No payment required.');
            redirect(APP_URL . '/public/student/');
        }

        if ($amount > $remaining) {
            Auth::setFlash('error', 'Payment amount (' . peso($amount) . ') cannot exceed your remaining balance of ' . peso($remaining));
            redirect(APP_URL . '/public/student/');
        }

        $orNumber = generateORNumber();

        // 1. Record payment transaction
        Payment::create($feeId, $student['id'], $orNumber, $amount, $method, 'Payment via Student Portal');

        // 2. Deduct from total tuition balance
        TuitionFee::recordPayment($feeId, $amount);

        // 3. Re-fetch updated fee balance
        $updatedFee = TuitionFee::findById($feeId);
        $newRemaining = round(max(0, (float)$updatedFee['total_amount'] - (float)$updatedFee['amount_paid']), 2);

        // 4. Send official receipt email
        $receiptHtml = Mailer::paymentReceiptHtml($student, $fee, $orNumber, $amount, $method, $newRemaining);

        Mailer::send($student['email'], "{$student['first_name']} {$student['last_name']}", "Payment Receipt: {$orNumber}", $receiptHtml, 'payment_confirmation', $feeId);

        if (!empty($student['parent_email'])) {
            Mailer::send($student['parent_email'], $student['parent_name'] ?? 'Parent', "Payment Confirmation: {$orNumber}", $receiptHtml, 'payment_confirmation', $feeId);
        }

        Auth::setFlash('payment_success', [
            'or_number' => $orNumber,
            'amount' => $amount,
            'method' => strtoupper($method),
            'fee_desc' => $fee['description'],
            'student_name' => "{$student['first_name']} {$student['last_name']}",
            'remaining' => $newRemaining,
            'date' => date('M d, Y h:i A')
        ]);

        redirect(APP_URL . '/public/student/');
    }

    public static function updatePassword(): void
    {
        Auth::requireRole('student');
        Auth::setFlash('error', 'Password changes are disabled for students. Your login password is your Last Name, shared with your parents for tuition monitoring.');
        redirect(APP_URL . '/public/student/');
    }
}
