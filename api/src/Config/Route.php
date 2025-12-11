<?php

namespace App\Config;

class Route
{
    public const API_PREFIX = '/api/v1';

    private static array $routes = [];


    public static function get(string $path, mixed $handler): void
    {
        self::$routes['GET'][self::API_PREFIX . $path] = $handler;
    }

    public static function post(string $path, mixed $handler): void
    {
        self::$routes['POST'][self::API_PREFIX . $path] = $handler;
    }

    public static function put(string $path, mixed $handler): void
    {
        self::$routes['PUT'][self::API_PREFIX . $path] = $handler;
    }

    public static function delete(string $path, mixed $handler): void
    {
        self::$routes['DELETE'][self::API_PREFIX . $path] = $handler;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }
}
