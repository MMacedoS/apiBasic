<?php

namespace App\Config;

use App\Http\Controllers\Middleware\Auth;

class Route
{
    private static array $routes = [];
    private static array $groupStack = [];


    public static function get(string $path, mixed $handler): void
    {
        self::addRoute('GET', $path, $handler);
    }

    public static function post(string $path, mixed $handler): void
    {
        self::addRoute('POST', $path, $handler);
    }

    public static function put(string $path, mixed $handler): void
    {
        self::addRoute('PUT', $path, $handler);
    }

    public static function delete(string $path, mixed $handler): void
    {
        self::addRoute('DELETE', $path, $handler);
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    public static function middlewares(array $middlewares, callable $callback): void
    {
        self::group(['middleware' => $middlewares], $callback);
    }

    private static function addRoute(string $method, string $path, mixed $handler): void
    {
        $middlewares = self::getGroupMiddlewares();
        $fullPath = $_ENV['API_PREFIX'] . $path;

        self::$routes[$method][$fullPath] = [
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    private static function getGroupMiddlewares(): array
    {
        $middlewares = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['middleware'])) {
                $middlewares = array_merge($middlewares, (array)$group['middleware']);
            }
        }

        return $middlewares;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }
}
