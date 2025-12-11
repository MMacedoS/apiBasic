<?php

namespace App\Http\Request;

use App\Config\Singleton;

class Request extends Singleton
{
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function url(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getRequestData(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        return is_array($data) ? $data : [];
    }
}
