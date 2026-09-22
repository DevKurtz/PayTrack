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

        // Update student class details
        Student::updateClassDetails($studentId, $gradeLevel, $schoolYear);

        // Process customized fee category items
        // Form sends: fee_included[cat_id]=1 and fee_amount[cat_id]=123.45 and fee_name[cat_id]=...
        $includedCats = $_POST['fee_included'] ?? [];
        $amounts      = $_POST['fee_amount'] ?? [];
        $catNames     = $_POST['fee_name'] ?? [];

        $selectedItems = [];
        foreach ($includedCats as $catId => $val) {
            $catId = (int) $catId;
            $amt = isset($amounts[$catId]) ? max(0.0, (float) $amounts[$catId]) : 0.0;
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

        // Create tuition assessment in DB
        $feeId = TuitionFee::createCustomAssessment(
            $studentId,
            $schoolYear,
            $semester,
            $description,
            !empty($dueDate) ? $dueDate : null,
            $selectedItems
        );

        $createdFee = TuitionFee::findById($feeId);

        // Notify student & parents via email
        $notifyEmail = !empty($_POST['notify_email']);
        if ($notifyEmail && $createdFee) {
            Mailer::sendTuitionAssessmentNotice($student, $createdFee, $selectedItems);
        }

        Auth::setFlash('success', "Tuition assessment of " . peso($createdFee['total_amount'] ?? 0) . " posted for {$student['first_name']} {$student['last_name']}." . ($notifyEmail ? " Notification emails sent to student & parents." : ""));
        redirect(APP_URL . '/public/accounting/?view=students');
    }

    /**
     * Record Over-the-Counter / Manual Cash Payment by Accounting Staff
     */
    public static function recordManualPayment(): void
    {
        Auth::requireRole('accounting');
        verify_csrf();

        $feeId  = (int) ($_POST['fee_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $method = trim($_POST['payment_method'] ?? 'cash');
        $notes  = trim($_POST['notes'] ?? 'Over-the-counter payment at Accounting Office');

        $allowed = ['cash', 'gcash', 'maya', 'bank_transfer', 'card', 'online', 'other'];
        if (!in_array($method, $allowed)) $method = 'cash';

        $fee = TuitionFee::findById($feeId);
        if (!$fee) {
            Auth::setFlash('error', 'Tuition assessment record not found.');
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        $remaining = max(0, (float)$fee['total_amount'] - (float)$fee['amount_paid']);
        if ($amount <= 0) {
            Auth::setFlash('error', 'Please enter a valid payment amount.');
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        if ($amount > $remaining) {
            Auth::setFlash('error', 'Payment amount cannot exceed remaining balance of ' . peso($remaining));
            redirect(APP_URL . '/public/accounting/?view=transactions');
        }

        $orNumber = generateORNumber();

        Payment::create($feeId, $fee['student_id'], $orNumber, $amount, $method, $notes);
        TuitionFee::recordPayment($feeId, $amount);

        // Send payment confirmation email
        $student = Student::findById($fee['student_id']);
        if ($student) {
            $updatedFee = TuitionFee::findById($feeId);
            $newRem = max(0, (float)$updatedFee['total_amount'] - (float)$updatedFee['amount_paid']);

            $receiptHtml = "
                <div style='font-family: sans-serif; padding: 20px; line-height: 1.6; color: #111827;'>
                    <h2 style='color: #0b3d2e;'>PayTrack — Official Payment Receipt</h2>
                    <div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; margin: 16px 0;'>
                        <p style='margin: 4px 0;'><strong>Receipt No (OR#):</strong> {$orNumber}</p>
                        <p style='margin: 4px 0;'><strong>Student:</strong> {$student['first_name']} {$student['last_name']} ({$student['student_id']})</p>
                        <p style='margin: 4px 0;'><strong>Tuition Assessment:</strong> {$fee['description']}</p>
                        <p style='margin: 4px 0;'><strong>Payment Method:</strong> " . strtoupper($method) . " (Accounting Cashier)</p>
                        <p style='margin: 4px 0; font-size: 16px; color: #047857;'><strong>Amount Paid:</strong> " . peso($amount) . "</p>
                        <p style='margin: 4px 0;'><strong>Remaining Balance:</strong> " . peso($newRem) . "</p>
                        <p style='margin: 4px 0;'><strong>Date & Time:</strong> " . date('Y-m-d H:i:s') . "</p>
                    </div>
                </div>
            ";
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
}
