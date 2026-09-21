<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// Load Composer autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class Mailer
{
    /**
     * Send an email via PHPMailer (SMTP) with automatic audit logging
     */
    public static function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $type = 'other',
        ?int $relatedId = null
    ): bool {
        // If placeholder credentials are used, simulate sending gracefully for local dev
        if (
            empty(MAIL_USER) ||
            MAIL_USER === 'your_email@gmail.com' ||
            MAIL_PASS === 'your_app_password'
        ) {
            self::logEmail($toEmail, $subject, $type, $relatedId, 'sent', 'Simulated: To send live emails, configure real SMTP credentials in config/config.php');
            return true;
        }

        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                // SMTP Server Configuration
                $mail->isSMTP();
                $mail->Host       = MAIL_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = MAIL_USER;
                $mail->Password   = MAIL_PASS;
                $mail->CharSet    = 'UTF-8';
                $mail->Timeout    = 10; // 10-second timeout

                // Port & Encryption Detection
                if (MAIL_PORT == 465) {
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                } else {
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                }
                $mail->Port = MAIL_PORT;

                // Windows / XAMPP SSL certificate options
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ]
                ];

                // Sender & Recipient
                $mail->setFrom(MAIL_FROM, MAIL_NAME);
                $mail->addAddress($toEmail, $toName);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlBody;
                $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody));

                $mail->send();
                self::logEmail($toEmail, $subject, $type, $relatedId, 'sent');
                return true;
            } catch (\Exception $e) {
                $errMsg = $mail->ErrorInfo ?? $e->getMessage();
                self::logEmail($toEmail, $subject, $type, $relatedId, 'failed', $errMsg);
                return false;
            }
        }

        // Fallback simulation
        self::logEmail($toEmail, $subject, $type, $relatedId, 'sent', 'PHPMailer package not found. Sent via fallback simulation.');
        return true;
    }

    private static function logEmail(
        string $email,
        string $subject,
        string $type,
        ?int $relatedId,
        string $status,
        ?string $err = null
    ): void {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO email_logs (recipient_email, subject, type, related_id, status, error_message, sent_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$email, $subject, $type, $relatedId, $status, $err]);
        } catch (\Exception $e) {
            // Error logging failure should not break app flow
        }
    }

    public static function sendStudentRegistrationEmails(array $student, string $rawPassword): void
    {
        $loginUrl = APP_URL . '/public/';
        $studentName = "{$student['first_name']} {$student['last_name']}";

        // 1. Student Email
        $studentHtml = "
            <div style='font-family: sans-serif; padding: 24px; color: #1e293b; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 600px; margin: auto;'>
                <div style='background: #0b3d2e; color: #ffffff; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h2 style='margin: 0; font-size: 20px;'>Welcome to PayTrack!</h2>
                    <p style='margin: 4px 0 0; opacity: 0.9; font-size: 13px;'>Student Tuition Fee Management System</p>
                </div>
                <p>Hello <strong>{$studentName}</strong>,</p>
                <p>Your student account has been registered by the administration. You can now log in to check your tuition assessment, official receipts, and account updates.</p>
                <div style='background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin: 16px 0;'>
                    <p style='margin: 4px 0;'><strong>Student ID (Username):</strong> <code style='font-size: 15px; color: #0b3d2e;'>{$student['student_id']}</code></p>
                    <p style='margin: 4px 0;'><strong>Default Password:</strong> <code style='font-size: 15px; color: #0b3d2e;'>{$rawPassword}</code> (Your Last Name)</p>
                    <p style='margin: 8px 0 0; font-size: 12px; color: #64748b;'><em>Note: Your default password is your Last Name, shared with your parents for tuition transparency.</em></p>
                </div>
                <p style='margin-top: 20px;'>
                    <a href='{$loginUrl}' style='display: inline-block; background: #0b3d2e; color: #ffffff; padding: 10px 22px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Login to PayTrack Portal &rarr;</a>
                </p>
            </div>
        ";
        self::send($student['email'], $studentName, "PayTrack — Student Portal Account Created ({$student['student_id']})", $studentHtml, 'account_created', $student['id'] ?? null);

        // 2. Parent Email
        if (!empty($student['parent_email'])) {
            $parentName = $student['parent_name'] ?? 'Parent / Guardian';
            $parentHtml = "
                <div style='font-family: sans-serif; padding: 24px; color: #1e293b; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 600px; margin: auto;'>
                    <div style='background: #0b3d2e; color: #ffffff; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px;'>
                        <h2 style='margin: 0; font-size: 20px;'>PayTrack — Parent Account Access</h2>
                        <p style='margin: 4px 0 0; opacity: 0.9; font-size: 13px;'>Tuition Monitoring for {$studentName}</p>
                    </div>
                    <p>Dear <strong>{$parentName}</strong>,</p>
                    <p>A PayTrack account has been created for your student, <strong>{$studentName}</strong>. You may use the student's ID and shared password to log in and monitor balances, review official electronic receipts, and track payment schedules.</p>
                    <div style='background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin: 16px 0;'>
                        <p style='margin: 4px 0;'><strong>Student ID:</strong> <code style='font-size: 15px; color: #0b3d2e;'>{$student['student_id']}</code></p>
                        <p style='margin: 4px 0;'><strong>Shared Password:</strong> <code style='font-size: 15px; color: #0b3d2e;'>{$rawPassword}</code> (Student's Last Name)</p>
                    </div>
                    <p style='margin-top: 20px;'>
                        <a href='{$loginUrl}' style='display: inline-block; background: #0b3d2e; color: #ffffff; padding: 10px 22px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Open PayTrack Portal &rarr;</a>
                    </p>
                </div>
            ";
            self::send($student['parent_email'], $parentName, "PayTrack — Account Access for {$studentName}", $parentHtml, 'account_created', $student['id'] ?? null);
        }

        // 3. Accounting Staff Email (Alerting Accounting Office of newly registered student)
        $accountingUser = User::findFirstByRole('accounting');
        $accountingEmail = $accountingUser['email'] ?? 'accounting@paytrack.edu.ph';
        $accountingName  = $accountingUser['name'] ?? 'Accounting Office';

        $accountingHtml = "
            <div style='font-family: sans-serif; padding: 24px; color: #1e293b; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 600px; margin: auto;'>
                <div style='background: #0f172a; color: #ffffff; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h2 style='margin: 0; font-size: 20px;'>New Student Registration Notice</h2>
                    <p style='margin: 4px 0 0; opacity: 0.9; font-size: 13px;'>Action Required: Class Details & Tuition Assessment</p>
                </div>
                <p>Attention: <strong>{$accountingName}</strong>,</p>
                <p>A new student has been registered into PayTrack by the administration and is awaiting class assignment and tuition assessment:</p>
                <div style='background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin: 16px 0;'>
                    <p style='margin: 4px 0;'><strong>Student Name:</strong> {$studentName}</p>
                    <p style='margin: 4px 0;'><strong>Student ID:</strong> <code style='font-size: 14px; color: #0f172a;'>{$student['student_id']}</code></p>
                    <p style='margin: 4px 0;'><strong>Email:</strong> {$student['email']}</p>
                    <p style='margin: 4px 0;'><strong>Status:</strong> <span style='color: #b45309; font-weight: bold;'>Pending Tuition Assessment</span></p>
                </div>
                <p>Please log in to the Accounting Portal to assign the student's class schedule, review fee categories, and post the assessment.</p>
                <p style='margin-top: 20px;'>
                    <a href='{$loginUrl}' style='display: inline-block; background: #0f172a; color: #ffffff; padding: 10px 22px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Go to Accounting Portal &rarr;</a>
                </p>
            </div>
        ";
        self::send($accountingEmail, $accountingName, "PayTrack Notice: New Student Enrolled ({$student['student_id']}) — Assessment Required", $accountingHtml, 'new_student_accounting_alert', $student['id'] ?? null);
    }

    public static function sendAccountingCredentials(string $toEmail, string $toName, string $username, string $rawPassword): void
    {
        $loginUrl = APP_URL . '/public/';
        $html = "
            <div style='font-family: sans-serif; padding: 24px; color: #1e293b; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 600px; margin: auto;'>
                <div style='background: #0f172a; color: #ffffff; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h2 style='margin: 0; font-size: 20px;'>PayTrack — Accounting Staff Credentials</h2>
                    <p style='margin: 4px 0 0; opacity: 0.9; font-size: 13px;'>Finance & Assessment Operations Access</p>
                </div>
                <p>Hello <strong>{$toName}</strong>,</p>
                <p>An Accounting Staff account has been created for you by the System Administrator. You can sign in using the unified login form to manage student tuition fees, assign assessments, and record payment transactions.</p>
                <div style='background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; margin: 16px 0;'>
                    <p style='margin: 4px 0;'><strong>Portal Username:</strong> <code style='font-size: 15px; color: #0f172a;'>{$username}</code></p>
                    <p style='margin: 4px 0;'><strong>Password:</strong> <code style='font-size: 15px; color: #0f172a;'>{$rawPassword}</code></p>
                    <p style='margin: 4px 0;'><strong>Assigned Role:</strong> <strong>Accounting Staff</strong></p>
                </div>
                <p style='margin-top: 20px;'>
                    <a href='{$loginUrl}' style='display: inline-block; background: #0f172a; color: #ffffff; padding: 10px 22px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Login to Accounting Portal &rarr;</a>
                </p>
            </div>
        ";
        self::send($toEmail, $toName, "PayTrack — Accounting Staff Account Created", $html, 'accounting_account_created');
    }

    public static function sendTuitionAssessmentNotice(array $student, array $fee, array $items = []): void
    {
        $loginUrl = APP_URL . '/public/';
        $studentName = "{$student['first_name']} {$student['last_name']}";
        $itemsHtml = '';
        foreach ($items as $it) {
            $amt = peso($it['amount'] ?? 0);
            $catName = htmlspecialchars($it['category_name'] ?? 'Fee Item');
            $itemsHtml .= "<tr><td style='padding: 6px 10px; border-bottom: 1px solid #f1f5f9;'>{$catName}</td><td style='padding: 6px 10px; border-bottom: 1px solid #f1f5f9; text-align: right; font-weight: bold;'>{$amt}</td></tr>";
        }
        $totalFormatted = peso($fee['total_amount'] ?? 0);

        $html = "
            <div style='font-family: sans-serif; padding: 24px; color: #1e293b; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; max-width: 600px; margin: auto;'>
                <div style='background: #0b3d2e; color: #ffffff; padding: 16px 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h2 style='margin: 0; font-size: 20px;'>Tuition Assessment Posted</h2>
                    <p style='margin: 4px 0 0; opacity: 0.9; font-size: 13px;'>PayTrack — {$fee['description']}</p>
                </div>
                <p>Hello <strong>{$studentName}</strong>,</p>
                <p>Your tuition fee assessment and class schedule details have been officially evaluated and posted by the Accounting Office.</p>
                
                <table style='width: 100%; border-collapse: collapse; margin: 16px 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px;'>
                    <thead>
                        <tr style='background: #e2e8f0; text-align: left;'>
                            <th style='padding: 8px 10px;'>Assessment Breakdown</th>
                            <th style='padding: 8px 10px; text-align: right;'>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHtml}
                        <tr style='background: #f1f5f9; font-size: 14px;'>
                            <td style='padding: 10px; font-weight: bold;'>Total Assessment Due</td>
                            <td style='padding: 10px; text-align: right; font-weight: bold; color: #0b3d2e;'>{$totalFormatted}</td>
                        </tr>
                    </tbody>
                </table>
                <p>Due Date: <strong>" . (!empty($fee['due_date']) ? date('M d, Y', strtotime($fee['due_date'])) : 'Per Academic Calendar') . "</strong></p>
                <p style='margin-top: 20px;'>
                    <a href='{$loginUrl}' style='display: inline-block; background: #0b3d2e; color: #ffffff; padding: 10px 22px; text-decoration: none; border-radius: 6px; font-weight: bold;'>View Breakdown & Pay Online &rarr;</a>
                </p>
            </div>
        ";

        // Send to Student
        self::send($student['email'], $studentName, "PayTrack — Tuition Assessment: {$fee['description']}", $html, 'tuition_assessed', $fee['id'] ?? null);

        // Send to Parent if email exists
        if (!empty($student['parent_email'])) {
            self::send($student['parent_email'], $student['parent_name'] ?? 'Parent', "PayTrack — Tuition Assessment for {$studentName}", $html, 'tuition_assessed', $fee['id'] ?? null);
        }
    }
}
