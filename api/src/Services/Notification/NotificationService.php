<?php

namespace App\Services\Notification;

use App\Config\Singleton;
use App\Services\Email\EmailService;
use App\Services\Email\Templates\TemplateRenderer;

class NotificationService extends Singleton
{
    private EmailService $emailService;
    private TemplateRenderer $templateRenderer;

    public function __construct()
    {
        $this->emailService = EmailService::getInstance();
        $this->templateRenderer = TemplateRenderer::getInstance();
    }

    public function sendWelcomeNotification(string $email, string $title, string $name, string $actionUrl): bool
    {
        $subject = $title;
        $data = [
            'name' => $name,
            'app_name' => $_ENV['MAIL_FROM_NAME'],
            'action_url' => $actionUrl,
            'year' => date('Y')
        ];

        $body = $this->templateRenderer->renderWelcome($data);

        return $this->emailService->send($email, $subject, $body, $name);
    }

    public function sendPasswordResetNotification(string $email, string $name, string $resetUrl, int $expirationHours = 1): bool
    {
        $subject = 'Redefinição de Senha';
        $data = [
            'name' => $name,
            'reset_url' => $resetUrl,
            'expiration_time' => $expirationHours,
            'app_name' => $_ENV['MAIL_FROM_NAME'],
            'year' => date('Y')
        ];

        $body = $this->templateRenderer->renderResetPassword($data);

        return $this->emailService->send($email, $subject, $body, $name);
    }

    public function sendCustomNotification(string $email, string $name, string $title, string $message, string $description): bool
    {
        $subject = $title;
        $data = [
            'name' => $name,
            'title' => $title,
            'message' => $message,
            'description' => $description,
            'app_name' => $_ENV['MAIL_FROM_NAME'],
            'year' => date('Y')
        ];

        $body = $this->templateRenderer->renderNotification($data);

        return $this->emailService->send($email, $subject, $body, $name);
    }

    public function sendBulkNotifications(array $recipients, string $title, string $message, string $description): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $email = $recipient['email'];
            $name = $recipient['name'];
            $results[$email] = $this->sendCustomNotification($email, $name, $title, $message, $description);
        }

        return $results;
    }
}
