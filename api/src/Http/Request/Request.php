<?php

namespace App\Http\Request;

use App\Config\Singleton;

class Request extends Singleton
{
    private ?array $user = null;

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function url(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function all(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        $data = match (self::method()) {
            'GET' => $_GET,
            'POST', 'PUT', 'PATCH', 'DELETE' => $data
        };

        return is_array($data) ? $data : [];
    }

    public function header(string $key): ?string
    {
        $headers = getallheaders();
        $tokenHeader = $headers[$key] ?? null;
        if (is_null($tokenHeader)) {
            return null;
        }
        $token = str_replace('Bearer ', '', $tokenHeader);

        return $token ?? null;
    }

    public function setUser(array $user): void
    {
        $this->user = $user;
    }

    public function user(): ?array
    {
        return $this->user;
    }
}
