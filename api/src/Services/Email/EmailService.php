<?php

namespace App\Services\Email;

use App\Config\Singleton;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService extends Singleton
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['SMTP_HOST'];
        $this->mailer->SMTPAuth = filter_var($_ENV['SMTP_AUTH'], FILTER_VALIDATE_BOOLEAN);
        $this->mailer->Username = $_ENV['SMTP_USER'];
        $this->mailer->Password = $_ENV['SMTP_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
        $this->mailer->Port = $_ENV['SMTP_PORT'];
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }

    public function send(string $to, string $subject, string $body, ?string $toName = null): bool
    {
        try {
            $this->mailer->addAddress($to, $toName ?? $to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email sending failed: {$this->mailer->ErrorInfo}");
            return false;
        } finally {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
        }
    }

    public function sendWithAttachment(string $to, string $subject, string $body, array $attachments, ?string $toName = null): bool
    {
        try {
            $this->mailer->addAddress($to, $toName ?? $to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;

            foreach ($attachments as $attachment) {
                $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
            }

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email sending failed: {$this->mailer->ErrorInfo}");
            return false;
        } finally {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
        }
    }

    public function sendBulk(array $recipients, string $subject, string $body): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $email = $recipient['email'];
            $name = $recipient['name'] ?? null;
            $results[$email] = $this->send($email, $subject, $body, $name);
        }

        return $results;
    }
}
