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
}
