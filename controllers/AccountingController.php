<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/FeeCategory.php';
require_once __DIR__ . '/../models/TuitionFee.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../config/mailer.php';

class AccountingController
{
    /** Allow the signed-in accounting user to securely change their password. */
    public static function changePassword(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $user = User::findById((int) Auth::userId());
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if (!$user || ($user['role'] ?? '') !== 'accounting' || !password_verify($currentPassword, $user['password_hash'] ?? '')) {
            Auth::setFlash('error', 'Your current password is incorrect.');
            redirect(APP_URL . '/public/accounting/?view=home');
        }
        if (strlen($newPassword) < 8) {
            Auth::setFlash('error', 'Your new password must be at least 8 characters long.');
            redirect(APP_URL . '/public/accounting/?view=home');
        }
        if ($newPassword !== $confirmPassword) {
            Auth::setFlash('error', 'The new password and confirmation do not match.');
            redirect(APP_URL . '/public/accounting/?view=home');
        }
        if (password_verify($newPassword, $user['password_hash'])) {
            Auth::setFlash('error', 'Choose a password different from your current password.');
            redirect(APP_URL . '/public/accounting/?view=home');
        }

        User::updatePassword((int) $user['id'], $newPassword);
        Auth::setFlash('success', 'Your password has been changed. Use the new password the next time you sign in.');
        redirect(APP_URL . '/public/accounting/?view=home');
    }

    /**
     * Assign / Update Student Tuition Fee Assessment & Class Details
     * Allows customizing individual fee prices and excluding specific fee categories
     */
    public static function assignTuitionFee(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $student = Student::findById($studentId);
        if (!$student) {
            Auth::setFlash('error', 'Student record not found.');
            redirect(APP_URL . '/public/accounting/?view=students');
        }

        // Class & Academic Details
        $gradeLevel  = trim($_POST['grade_level'] ?? ($student['grade_level'] ?? 'BSCS 11A1'));
        $schoolYear  = trim($_POST['school_year'] ?? (date('Y') . '-' . (date('Y') + 1)));
        $semester    = trim($_POST['semester'] ?? '1st Semester');
        $dueDate     = trim($_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days')));
        $description = trim($_POST['description'] ?? "S.Y. {$schoolYear} - {$semester} Tuition");

        // Process customized fee category items
        // Form sends: fee_included[cat_id]=1 and fee_amount[cat_id]=123.45 and fee_name[cat_id]=...
        $includedCats = $_POST['fee_included'] ?? [];
        $amounts      = $_POST['fee_amount'] ?? [];
        $catNames     = $_POST['fee_name'] ?? [];

        $selectedItems = [];
        foreach ($includedCats as $catId => $val) {
            $catId = (int) $catId;
            $category = FeeCategory::findById($catId);
            if (!$category || empty($category['is_active'])) {
                continue;
            }
            // Institutional charges are controlled by Accounting fee settings;
            // ignore any client-submitted edits to their rates.
            $rawAmt = empty($category['is_variable'])
                ? number_format((float) $category['default_amount'], 2, '.', '')
                : trim((string) ($amounts[$catId] ?? ''));
            if (!preg_match('/^\d+(?:\.\d{1,2})?$/D', $rawAmt)) {
                Auth::setFlash('error', 'Fee amounts must be non-negative numbers with no more than 2 decimal places.');
                redirect(APP_URL . '/public/accounting/?view=students');
            }
            $amt = (float) $rawAmt;
            $name = trim($catNames[$catId] ?? "Fee Aspect #{$catId}");

            $selectedItems[] = [
                'fee_category_id' => $catId,
                'category_name'   => $name,
                'amount'          => $amt,
            ];
        }

        if (empty($selectedItems)) {
            Auth::setFlash('error', 'Please include at least one fee category in the assessment.');
            redirect(APP_URL . '/public/accounting/?view=students');
        }

        $feeId = (int) ($_POST['fee_id'] ?? 0);
        if ($feeId <= 0) {
            $existingFee = TuitionFee::findLatestByStudentId($studentId);
            if ($existingFee) {
                $feeId = (int)$existingFee['id'];
            }
        }

        if ($feeId > 0) {
            TuitionFee::updateCustomAssessment(
                $feeId,
                $studentId,
                $schoolYear,
                $semester,
                $description,
                !empty($dueDate) ? $dueDate : null,
                $selectedItems
            );
            $createdFee = TuitionFee::findById($feeId);
            $actionWord = 'updated';
        } else {
            $feeId = TuitionFee::createCustomAssessment(
                $studentId,
                $schoolYear,
                $semester,
                $description,
                !empty($dueDate) ? $dueDate : null,
                $selectedItems
            );
            $createdFee = TuitionFee::findById($feeId);
            $actionWord = 'posted';
        }

        // Notify student & parents via email
        $notifyEmail = !empty($_POST['notify_email']);
        if ($notifyEmail && $createdFee) {
            Mailer::sendTuitionAssessmentNotice($student, $createdFee, $selectedItems);
        }

        Auth::setFlash('success', "Tuition assessment of " . peso($createdFee['total_amount'] ?? 0) . " {$actionWord} for {$student['first_name']} {$student['last_name']}." . ($notifyEmail ? " Notification emails sent to student & parents." : ""));
        redirect(APP_URL . '/public/accounting/?view=students');
    }

    /**
     * Record Over-the-Counter / Manual Cash Payment by Accounting Staff
     */
    public static function recordManualPayment(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $feeId     = (int) ($_POST['fee_id'] ?? 0);
        $rawAmount = trim((string) ($_POST['amount'] ?? ''));
        $method    = trim($_POST['payment_method'] ?? 'cash');
        $notes     = trim($_POST['notes'] ?? 'Over-the-counter payment at Accounting Office');

        $allowed = ['cash', 'gcash', 'maya', 'bank_transfer', 'card', 'online', 'other'];
        if (!in_array($method, $allowed)) $method = 'cash';

        if (!is_numeric($rawAmount) || (float)$rawAmount <= 0) {
            Auth::setFlash('error', 'Please enter a valid positive payment amount.');
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        $amount = round((float)$rawAmount, 2);

        $fee = TuitionFee::findById($feeId);
        if (!$fee) {
            Auth::setFlash('error', 'Tuition assessment record not found.');
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        $remaining = round(max(0, (float)$fee['total_amount'] - (float)$fee['amount_paid']), 2);
        if ($remaining <= 0) {
            Auth::setFlash('error', 'This tuition assessment is already fully settled and has no remaining balance.');
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        if ($amount > $remaining) {
            Auth::setFlash('error', 'Payment amount (' . peso($amount) . ') cannot exceed remaining balance of ' . peso($remaining));
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        $orNumber = generateORNumber();

        Payment::create($feeId, $fee['student_id'], $orNumber, $amount, $method, $notes);
        TuitionFee::recordPayment($feeId, $amount);

        // Send payment confirmation email
        $student = Student::findById($fee['student_id']);
        if ($student) {
            $updatedFee = TuitionFee::findById($feeId);
            $newRem = round(max(0, (float)$updatedFee['total_amount'] - (float)$updatedFee['amount_paid']), 2);

            $receiptHtml = Mailer::paymentReceiptHtml($student, $fee, $orNumber, $amount, $method, $newRem, true);
            Mailer::send($student['email'], "{$student['first_name']} {$student['last_name']}", "Payment Receipt: {$orNumber}", $receiptHtml, 'payment_confirmation', $feeId);
            if (!empty($student['parent_email'])) {
                Mailer::send($student['parent_email'], $student['parent_name'] ?? 'Parent', "Payment Confirmation: {$orNumber}", $receiptHtml, 'payment_confirmation', $feeId);
            }
        }

        Auth::setFlash('success', "Payment of " . peso($amount) . " recorded. Official Receipt {$orNumber} generated.");
        redirect(APP_URL . '/public/accounting/?view=transactions');
    }

    public static function saveFeeCategory(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $id = (int) ($_POST['category_id'] ?? 0);
        $name = trim(strip_tags($_POST['name'] ?? ''));
        $defaultAmount = (float) ($_POST['default_amount'] ?? 0);
        $isVariable = !empty($_POST['is_variable']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 99);

        if (empty($name)) {
            Auth::setFlash('error', 'Category name is required.');
            redirect(APP_URL . '/public/accounting/?view=categories');
        }

        if ($id > 0) {
            FeeCategory::update($id, [
                'name' => $name,
                'default_amount' => $defaultAmount,
                'is_variable' => $isVariable,
                'sort_order' => $sortOrder,
                'is_active' => 1
            ]);
            Auth::setFlash('success', 'Fee category updated.');
        } else {
            FeeCategory::create([
                'name' => $name,
                'default_amount' => $defaultAmount,
                'is_variable' => $isVariable,
                'sort_order' => $sortOrder
            ]);
            Auth::setFlash('success', 'Fee category added.');
        }

        redirect(APP_URL . '/public/accounting/?view=categories');
    }

    public static function deleteFeeCategory(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $id = (int) ($_POST['category_id'] ?? 0);
        if ($id > 0) {
            FeeCategory::delete($id);
            Auth::setFlash('success', 'Fee category deleted.');
        }
        redirect(APP_URL . '/public/accounting/?view=categories');
    }

    public static function deleteFee(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $feeId = (int) ($_POST['fee_id'] ?? 0);
        if ($feeId > 0) {
            TuitionFee::delete($feeId);
            Auth::setFlash('success', 'Tuition assessment record deleted.');
        }
        redirect(APP_URL . '/public/accounting/?view=fees');
    }

    /**
     * Real-time polling API endpoint for Accounting Portal
     * Returns new payments, updated balances, and metrics in JSON format
     */
    public static function realtimeFeed(): void
    {
        Auth::requireRole('accounting');
        header('Content-Type: application/json; charset=utf-8');

        $db = Database::getInstance();
        $lastPaymentId = (int) ($_GET['last_payment_id'] ?? 0);

        // Fetch any new payments created after $lastPaymentId
        $newPaymentsStmt = $db->prepare("
            SELECT p.*, s.first_name, s.last_name, s.student_id as student_num, tf.description as fee_desc
            FROM payments p
            JOIN students s ON p.student_id = s.id
            JOIN tuition_fees tf ON p.tuition_fee_id = tf.id
            WHERE p.id > ?
            ORDER BY p.id ASC
        ");
        $newPaymentsStmt->execute([$lastPaymentId]);
        $newPayments = $newPaymentsStmt->fetchAll();

        // Calculate latest system-wide financial metrics
        $payments = Payment::all();
        $fees = TuitionFee::all();

        $totalRevenue = 0;
        foreach ($payments as $p) {
            $totalRevenue += (float) ($p['amount'] ?? 0);
        }
        $totalAssessed = 0;
        foreach ($fees as $f) {
            $totalAssessed += (float) ($f['total_amount'] ?? 0);
        }

        // Save class changes only after every submitted fee amount is valid.
        Student::updateClassDetails($studentId, $gradeLevel, $schoolYear);
        $totalReceivables = max(0, $totalAssessed - $totalRevenue);
        $collectionRate = ($totalAssessed > 0) ? min(100, round(($totalRevenue / $totalAssessed) * 100, 1)) : 0;

        // Fetch students with current balance
        $students = Student::all();
        $studentBalances = [];
        foreach ($students as $s) {
            $sFees = TuitionFee::getByStudentId($s['id']);
            $primaryFee = !empty($sFees) ? $sFees[0] : null;
            $tAmt = $primaryFee ? (float)$primaryFee['total_amount'] : 0.0;
            $pAmt = $primaryFee ? (float)$primaryFee['amount_paid'] : 0.0;
            $rAmt = max(0.0, $tAmt - $pAmt);

            $studentBalances[] = [
                'id' => (int)$s['id'],
                'student_id' => $s['student_id'],
                'name' => $s['first_name'] . ' ' . $s['last_name'],
                'has_assessment' => !empty($sFees),
                'total_amount' => $tAmt,
                'amount_paid' => $pAmt,
                'remaining_balance' => $rAmt,
                'formatted_balance' => peso($rAmt),
                'formatted_total' => peso($tAmt)
            ];
        }

        // Return each assessment separately so the live feed can update the
        // Accounting > Tuition Assessments table (students may have many fees).
        $assessmentBalances = [];
        foreach ($fees as $fee) {
            $feeTotal = (float) ($fee['total_amount'] ?? 0);
            $feePaid = (float) ($fee['amount_paid'] ?? 0);
            $feeRemaining = max(0, $feeTotal - $feePaid);
            $assessmentBalances[(int) $fee['id']] = [
                'total_amount' => $feeTotal,
                'amount_paid' => $feePaid,
                'remaining_balance' => $feeRemaining,
                'formatted_total' => peso($feeTotal),
                'formatted_paid' => peso($feePaid),
                'formatted_remaining' => peso($feeRemaining),
                'status' => $feeRemaining <= 0 ? 'paid' : ($feePaid > 0 ? 'partial' : 'unpaid')
            ];
        }

        // Check latest unread email logs / notifications
        $emailLogs = $db->query("SELECT id, recipient_email, subject, type, status, sent_at FROM email_logs ORDER BY sent_at DESC LIMIT 10")->fetchAll();

        echo json_encode([
            'success' => true,
            'timestamp' => time(),
            'new_payments_count' => count($newPayments),
            'new_payments' => $newPayments,
            'metrics' => [
                'total_revenue' => $totalRevenue,
                'total_receivables' => $totalReceivables,
                'collection_rate' => $collectionRate,
                'formatted_revenue' => peso($totalRevenue),
                'formatted_receivables' => peso($totalReceivables),
                'formatted_collection_rate' => $collectionRate . '%'
            ],
            'students' => $studentBalances,
            'assessments' => $assessmentBalances,
            'recent_notifications' => $emailLogs
        ]);
        exit;
    }
}
