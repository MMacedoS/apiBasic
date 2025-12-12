<?php

namespace App\Http\Trait;

use App\Services\Notification\NotificationService;

trait SendEmailTrait
{
    public function sendEmail(string $to, string $subject, string $body, ?string $toName = null, string $link = ''): void
    {
        $notificationService = NotificationService::getInstance();
        $notificationService->sendWelcomeNotification(
            $to,
            $subject,
            $toName ?? $to,
            $link
        );
    }
}
