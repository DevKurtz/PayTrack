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

        $userId = Auth::userId();
        $student = Student::findByUserId($userId);
        if (!$student) redirect(APP_URL . '/public/student/');

        $feeId = (int) ($_POST['fee_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $method = trim($_POST['payment_method'] ?? 'online');

        if ($feeId <= 0 || $amount <= 0) {
            Auth::setFlash('error', 'Please enter a valid payment amount.');
            redirect(APP_URL . '/public/student/');
        }

        $fee = TuitionFee::findById($feeId);
        if (!$fee || (int)$fee['student_id'] !== (int)$student['id']) {
            Auth::setFlash('error', 'Invalid tuition fee record.');
            redirect(APP_URL . '/public/student/');
        }

        $remaining = max(0, (float)$fee['total_amount'] - (float)$fee['amount_paid']);
        if ($amount > $remaining) {
            Auth::setFlash('error', 'Payment amount cannot exceed the remaining balance of ' . peso($remaining));
            redirect(APP_URL . '/public/student/');
        }

        $orNumber = generateORNumber();

        // 1. Record payment transaction
        Payment::create($feeId, $student['id'], $orNumber, $amount, $method, 'Payment via Student Portal');

        // 2. Deduct from total tuition balance
        TuitionFee::recordPayment($feeId, $amount);

        // 3. Re-fetch updated fee balance
        $updatedFee = TuitionFee::findById($feeId);
        $newRemaining = max(0, (float)$updatedFee['total_amount'] - (float)$updatedFee['amount_paid']);

        // 4. Send official receipt email
        $receiptHtml = "
            <div style='font-family: sans-serif; padding: 20px; line-height: 1.6; color: #111827;'>
                <h2 style='color: #0b3d2e;'>PayTrack — Official Payment Receipt</h2>
                <div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; margin: 16px 0;'>
                    <p style='margin: 4px 0;'><strong>Receipt No (OR#):</strong> {$orNumber}</p>
                    <p style='margin: 4px 0;'><strong>Student:</strong> {$student['first_name']} {$student['last_name']} ({$student['student_id']})</p>
                    <p style='margin: 4px 0;'><strong>Tuition Assessment:</strong> {$fee['description']}</p>
                    <p style='margin: 4px 0;'><strong>Payment Method:</strong> " . strtoupper($method) . "</p>
                    <p style='margin: 4px 0; font-size: 16px; color: #047857;'><strong>Amount Paid:</strong> " . peso($amount) . "</p>
                    <p style='margin: 4px 0;'><strong>Remaining Tuition Balance:</strong> " . peso($newRemaining) . "</p>
                    <p style='margin: 4px 0;'><strong>Date & Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                </div>
                <p style='color: #6b7280; font-size: 13px;'>Keep this receipt for your records. Verified & Recorded.</p>
            </div>
        ";

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
        $newPassword = $_POST['new_password'] ?? '';
        if (strlen($newPassword) < 6) {
            Auth::setFlash('error', 'Password must be at least 6 characters.');
            redirect(APP_URL . '/public/student/');
        }

        User::updatePassword(Auth::userId(), $newPassword);
        Auth::setFlash('success', 'Password successfully updated.');
        redirect(APP_URL . '/public/student/');
    }
}
