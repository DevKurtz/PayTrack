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

class AdminController
{
    public static function createStudent(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $studentId     = trim($_POST['student_id'] ?? '');
        $firstName     = trim($_POST['first_name'] ?? '');
        $lastName      = trim($_POST['last_name'] ?? '');
        $parentName    = trim($_POST['parent_name'] ?? '');
        $parentEmail   = trim($_POST['parent_email'] ?? '');
        $studentEmail  = trim($_POST['student_email'] ?? '');
        $classGrade    = trim($_POST['class_grade'] ?? 'Class A');
        $parentContact = trim($_POST['parent_contact'] ?? '');

        // Tuition Fee Assessment Fields
        $totalTuition  = (float) ($_POST['total_tuition'] ?? 0);
        $schoolYear    = trim($_POST['school_year'] ?? (date('Y') . '-' . (date('Y') + 1)));
        $semester      = trim($_POST['semester'] ?? '1st Semester');
        $dueDate       = trim($_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days')));

        // Validations
        if (!preg_match('/^\d{4}-\d{5}$/', $studentId)) {
            Auth::setFlash('error', 'Invalid Student ID format. Must be 4 numbers, hyphen, and 5 numbers (e.g. 2024-12345).');
            redirect(APP_URL . '/public/admin/');
        }

        if (!isValidName($firstName) || !isValidName($lastName)) {
            Auth::setFlash('error', 'Please enter a valid student first and last name (letters only).');
            redirect(APP_URL . '/public/admin/');
        }

        if (!isValidEmail($studentEmail)) {
            Auth::setFlash('error', 'Please provide a valid Student Email address.');
            redirect(APP_URL . '/public/admin/');
        }

        if (!empty($parentEmail) && !isValidEmail($parentEmail)) {
            Auth::setFlash('error', 'Please provide a valid Parent Email address.');
            redirect(APP_URL . '/public/admin/');
        }

        $fixedSum = FeeCategory::getFixedTotal();
        if ($totalTuition < $fixedSum) {
            Auth::setFlash('error', 'Total tuition fee must be at least ' . peso($fixedSum) . ' to cover default school fees.');
            redirect(APP_URL . '/public/admin/');
        }

        // Check if student id or username already exists
        if (User::findByUsername($studentId)) {
            Auth::setFlash('error', "Student ID '{$studentId}' is already registered.");
            redirect(APP_URL . '/public/admin/');
        }

        // Default password = studentId + UPPERCASE(lastName)
        $rawPassword = defaultPassword($studentId, $lastName);

        // Create user & student with transaction
        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $userId = User::create($studentId, $rawPassword, 'student');

            $studentDbId = Student::create([
                'user_id'        => $userId,
                'student_id'     => $studentId,
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'email'          => $studentEmail,
                'grade_level'    => $classGrade,
                'school_year'    => $schoolYear,
                'parent_name'    => $parentName,
                'parent_email'   => $parentEmail,
                'contact_number' => $parentContact,
            ]);

            // Create initial tuition fee record with breakdown (LMS, Misc, Subject Fee)
            TuitionFee::createWithBreakdown($studentDbId, $totalTuition, $schoolYear, $semester, $dueDate);

            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            Auth::setFlash('error', 'Failed to create student account: ' . $e->getMessage());
            redirect(APP_URL . '/public/admin/');
        }

        // Send Email with credentials via Mailer
        $emailBody = "
            <div style='font-family: sans-serif; padding: 20px; line-height: 1.6; color: #111827;'>
                <h2 style='color: #0b3d2e;'>Welcome to PayTrack!</h2>
                <p>Hello <strong>" . e($firstName) . " " . e($lastName) . "</strong>,</p>
                <p>Your student portal account has been officially registered with an initial assessment of <strong>" . peso($totalTuition) . "</strong> for " . e($schoolYear) . " (" . e($semester) . ").</p>
                <div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 16px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 4px 0;'><strong>Portal URL:</strong> <a href='" . APP_URL . "/public/'>" . APP_URL . "/public/</a></p>
                    <p style='margin: 4px 0;'><strong>Student ID:</strong> " . e($studentId) . "</p>
                    <p style='margin: 4px 0;'><strong>Default Password:</strong> <code>" . e($rawPassword) . "</code></p>
                </div>
                <p style='color: #6b7280; font-size: 13px;'>Note: Your default password is your Last Name (case-insensitive, e.g. " . e($rawPassword) . ").</p>
            </div>
        ";

        Mailer::send($studentEmail, "$firstName $lastName", "PayTrack Student Account Credentials", $emailBody, 'account_created', $studentDbId);

        if (!empty($parentEmail)) {
            $parentBody = "
                <div style='font-family: sans-serif; padding: 20px; line-height: 1.6; color: #111827;'>
                    <h2 style='color: #0b3d2e;'>PayTrack — Student Registration Notice</h2>
                    <p>Dear Parent/Guardian <strong>" . e($parentName) . "</strong>,</p>
                    <p>Your student <strong>" . e($firstName) . " " . e($lastName) . "</strong> (" . e($studentId) . ") has been enrolled. Initial tuition assessment: <strong>" . peso($totalTuition) . "</strong>.</p>
                    <p>You may monitor payments and view official receipts through the PayTrack portal.</p>
                </div>
            ";
            Mailer::send($parentEmail, $parentName, "PayTrack — Student Registration Notice for {$firstName}", $parentBody, 'account_created', $studentDbId);
        }

        Auth::setFlash('created_student_credentials', [
            'student_id' => $studentId,
            'student_name' => "{$firstName} {$lastName}",
            'default_password' => $rawPassword,
            'tuition' => $totalTuition
        ]);
        Auth::setFlash('success', "Student Account & Tuition Assessment Created for {$firstName} {$lastName}.");
        redirect(APP_URL . '/public/admin/');
    }

    public static function assignFee(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $studentId    = (int) ($_POST['student_id'] ?? 0);
        $totalAmount  = (float) ($_POST['amount'] ?? 0);
        $schoolYear   = trim($_POST['school_year'] ?? (date('Y') . '-' . (date('Y') + 1)));
        $semester     = trim($_POST['semester'] ?? '1st Semester');
        $dueDate      = trim($_POST['due_date'] ?? '');

        $fixedSum = FeeCategory::getFixedTotal();
        if ($studentId <= 0 || $totalAmount < $fixedSum) {
            Auth::setFlash('error', 'Please provide a valid tuition amount of at least ' . peso($fixedSum) . '.');
            redirect(APP_URL . '/public/admin/?view=fees');
        }

        TuitionFee::createWithBreakdown($studentId, $totalAmount, $schoolYear, $semester, !empty($dueDate) ? $dueDate : null);
        Auth::setFlash('success', 'Tuition Fee Assessment Assigned with Fee Breakdown.');
        redirect(APP_URL . '/public/admin/?view=fees');
    }

    public static function saveFeeCategory(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $id = (int) ($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $defaultAmount = (float) ($_POST['default_amount'] ?? 0);
        $isVariable = !empty($_POST['is_variable']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 99);

        if (empty($name)) {
            Auth::setFlash('error', 'Category name is required.');
            redirect(APP_URL . '/public/admin/?view=categories');
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

        redirect(APP_URL . '/public/admin/?view=categories');
    }

    public static function deleteFeeCategory(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $id = (int) ($_POST['category_id'] ?? 0);
        if ($id > 0) {
            FeeCategory::delete($id);
            Auth::setFlash('success', 'Fee category deleted.');
        }
        redirect(APP_URL . '/public/admin/?view=categories');
    }

    public static function deleteStudent(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $id = (int) ($_POST['student_id'] ?? 0);
        if ($id > 0) {
            Student::delete($id);
            Auth::setFlash('success', 'Student record deleted successfully.');
        }
        redirect(APP_URL . '/public/admin/');
    }

    public static function deleteFee(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $feeId = (int) ($_POST['fee_id'] ?? 0);
        if ($feeId > 0) {
            TuitionFee::delete($feeId);
            Auth::setFlash('success', 'Fee record deleted successfully.');
        }
        redirect(APP_URL . '/public/admin/?view=fees');
    }
}
