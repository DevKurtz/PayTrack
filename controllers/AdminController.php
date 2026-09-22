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
        $middleName    = trim($_POST['middle_name'] ?? '');
        $parentName    = trim(strip_tags($_POST['parent_name'] ?? ''));
        $parentEmail   = trim($_POST['parent_email'] ?? '');
        $studentEmail  = trim($_POST['student_email'] ?? '');
        $parentContact = trim(preg_replace('/[^0-9+\-\s\(\)]/', '', $_POST['parent_contact'] ?? ''));

        $course        = trim($_POST['course'] ?? 'BSCS');
        $sectionCode   = trim($_POST['section_code'] ?? '11A1');
        $schoolYear    = trim($_POST['school_year'] ?? (date('Y') . '-' . (date('Y') + 1)));

        $classGrade = "{$course} {$sectionCode}";

        $formData = [
            'student_id' => $studentId, 'first_name' => $firstName, 'last_name' => $lastName,
            'middle_name' => $middleName, 'student_email' => $studentEmail, 'parent_name' => $parentName,
            'parent_email' => $parentEmail, 'parent_contact' => $parentContact, 'course' => $course,
            'section_code' => $sectionCode, 'school_year' => $schoolYear,
        ];
        $fail = static function (array $errors) use ($formData): void {
            Auth::setFlash('student_form_errors', $errors);
            Auth::setFlash('student_form_data', $formData);
            redirect(APP_URL . '/public/admin/?view=users');
        };

        // Validations
        if (!preg_match('/^\d{4}-\d{5}$/', $studentId)) {
            $fail(['student_id' => 'Use the format 2024-12345.']);
        }

        if (!isValidName($firstName) || !isValidName($lastName)) {
            $fail(['first_name' => 'Enter a valid first name.', 'last_name' => 'Enter a valid last name.']);
        }

        if (!isValidEmail($studentEmail)) {
            $fail(['student_email' => 'Enter a valid student email.']);
        }

        if (!empty($parentEmail) && !isValidEmail($parentEmail)) {
            $fail(['parent_email' => 'Enter a valid parent email.']);
        }

        if ($parentEmail !== '' && strcasecmp($studentEmail, $parentEmail) === 0) {
            $fail(['student_email' => 'Student and parent emails must be different.', 'parent_email' => 'Student and parent emails must be different.']);
        }

        // Check if student id or username already exists
        if (User::findByUsername($studentId)) {
            $fail(['student_id' => 'This Student ID is already registered.']);
        }
        if (User::findByEmail($studentEmail)) {
            $fail(['student_email' => 'This student email is already registered.']);
        }

        // Default password = Student's Last Name (stored cleanly as upper)
        $rawPassword = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($lastName));
        if (empty($rawPassword)) {
            $rawPassword = 'STUDENT' . date('Y');
        }

        // Create User & Student Record with Database Transaction
        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $fullName = trim("{$firstName} {$lastName}");
            $userId = User::create($studentId, $rawPassword, 'student', $fullName, $studentEmail);

            $studentDbId = Student::create([
                'user_id'        => $userId,
                'student_id'     => $studentId,
                'first_name'     => $firstName,
                'last_name'      => $lastName,
                'middle_name'    => $middleName,
                'email'          => $studentEmail,
                'grade_level'    => $classGrade,
                'school_year'    => $schoolYear,
                'parent_name'    => $parentName,
                'parent_email'   => $parentEmail,
                'contact_number' => $parentContact,
            ]);

            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            Auth::setFlash('error', 'Failed to enroll student: ' . $e->getMessage());
            redirect(APP_URL . '/public/admin/?view=users');
        }

        // 3-Way Notification: Student, Parent, and Accounting Office
        $studentData = [
            'id'           => $studentDbId,
            'student_id'   => $studentId,
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'email'        => $studentEmail,
            'parent_name'  => $parentName,
            'parent_email' => $parentEmail,
        ];
        Mailer::sendStudentRegistrationEmails($studentData, $rawPassword);

        Auth::setFlash('created_student_credentials', [
            'student_id'       => $studentId,
            'student_name'     => "{$firstName} {$lastName}",
            'default_password' => $rawPassword,
        ]);
        Auth::setFlash('success', "Student account registered for {$firstName} {$lastName}. Notification emails have been sent to the Student, Parent, and Accounting Office.");
        Auth::setFlash('account_created', ['type' => 'student', 'name' => "{$firstName} {$lastName}"]);
        redirect(APP_URL . '/public/admin/?view=users');
    }

    public static function createAccounting(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $username = trim($_POST['username'] ?? '');
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $name === '' || $email === '' || $password === '') {
            Auth::setFlash('error', 'Please fill in all required fields for the Accounting staff account.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        if (!isValidEmail($email)) {
            Auth::setFlash('error', 'Please provide a valid email address.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        if (strlen($password) < 6) {
            Auth::setFlash('error', 'Password must be at least 6 characters long.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        if (User::findByUsername($username)) {
            Auth::setFlash('error', "Username '{$username}' is already taken.");
            redirect(APP_URL . '/public/admin/?view=users');
        }

        User::createAccounting($username, $name, $email, $password);

        // Send Email to Accounting Staff with login credentials
        Mailer::sendAccountingCredentials($email, $name, $username, $password);

        Auth::setFlash('success', "Accounting Staff account created for {$name} ({$username}). Login credentials sent via email.");
        Auth::setFlash('account_created', ['type' => 'accounting', 'name' => $name]);
        redirect(APP_URL . '/public/admin/?view=users');
    }

    public static function deleteUser(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            Auth::setFlash('error', 'Invalid user ID.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        if ($userId === Auth::userId()) {
            Auth::setFlash('error', 'Action prohibited: You cannot delete your own logged-in administrator account.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        $user = User::findById($userId);
        if (!$user) {
            Auth::setFlash('error', 'User account not found.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        User::deleteUser($userId);
        Auth::setFlash('success', "User account '{$user['username']}' deleted successfully.");
        redirect(APP_URL . '/public/admin/?view=users');
    }

    public static function updateUserStatus(): void
    {
        Auth::requireRole('admin');
        verify_csrf();

        $userId = (int) ($_POST['user_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'active');

        if ($userId <= 0) {
            Auth::setFlash('error', 'Invalid user ID.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        if ($userId === Auth::userId()) {
            Auth::setFlash('error', 'Action prohibited: You cannot change status on your own logged-in account.');
            redirect(APP_URL . '/public/admin/?view=users');
        }

        User::updateStatus($userId, $status);
        Auth::setFlash('success', "User account status updated to " . ucfirst($status) . ".");
        redirect(APP_URL . '/public/admin/?view=users');
    }
}
