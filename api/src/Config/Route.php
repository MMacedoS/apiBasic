<?php

namespace App\Config;

class Route
{
    private static array $routes = [];


    public static function get(string $path, mixed $handler): void
    {
        self::$routes['GET'][$_ENV['API_PREFIX'] . $path] = $handler;
    }

    public static function post(string $path, mixed $handler): void
    {
        self::$routes['POST'][$_ENV['API_PREFIX'] . $path] = $handler;
    }

    public static function put(string $path, mixed $handler): void
    {
        self::$routes['PUT'][$_ENV['API_PREFIX'] . $path] = $handler;
    }

    public static function delete(string $path, mixed $handler): void
    {
        self::$routes['DELETE'][$_ENV['API_PREFIX'] . $path] = $handler;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }
}
