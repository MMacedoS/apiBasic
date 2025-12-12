<?php

namespace App\Services\Email\Templates;

use App\Config\Singleton;

class TemplateRenderer extends Singleton
{
    private string $templatesPath;

    public function __construct(?string $templatesPath = null)
    {
        $this->templatesPath = $templatesPath ?? __DIR__ . '/views/';
    }

    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->getTemplatePath($template);

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        $content = file_get_contents($templatePath);

        return $this->replaceVariables($content, $data);
    }

    private function getTemplatePath(string $template): string
    {
        if (!str_ends_with($template, '.html')) {
            $template .= '.html';
        }

        return $this->templatesPath . $template;
    }

    private function replaceVariables(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }

        return $content;
    }

    public function renderWelcome(array $data): string
    {
        return $this->render('welcome', $data);
    }

    public function renderResetPassword(array $data): string
    {
        return $this->render('reset-password', $data);
    }

    public function renderNotification(array $data): string
    {
        return $this->render('notification', $data);
    }
}
