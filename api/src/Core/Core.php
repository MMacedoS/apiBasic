<?php

namespace App\Core;

use App\Config\AppProvider;
use App\Config\Container;
use App\Http\Request\Request;
use App\Http\Request\Response;

class Core
{
    public static function dispatch(array $request): void
    {
        $url = Request::url();
        $methodUrl = Request::method();
        $requestObject = Request::getInstance();

        $normalizedRequestUri = self::normalizePath($url);

        foreach ($request as $method => $routes) {
            if ($methodUrl === $method) {
                foreach ($routes as $route => $handler) {
                    $normalizedRoute = rtrim($route, '/');
                    if ($normalizedRoute === '') {
                        $normalizedRoute = '/';
                    }

                    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $normalizedRoute);
                    $patternPath = '/^' . str_replace('/', '\/', $pattern) . '$/';

                    if (preg_match($patternPath, $normalizedRequestUri, $matches)) {
                        array_shift($matches);

                        $routeHandler = $handler;
                        $middlewares = [];

                        if (is_array($handler) && isset($handler['handler'])) {
                            $routeHandler = $handler['handler'];
                            $middlewares = $handler['middlewares'] ?? [];
                        }

                        if (!empty($middlewares)) {
                            $middlewareResponse = self::runMiddlewares($requestObject, $middlewares);
                            if ($middlewareResponse !== null) {
                                Response::json($middlewareResponse['body'], $middlewareResponse['status']);
                                return;
                            }
                        }

                        if (is_array($routeHandler) && isset($routeHandler[0]) && isset($routeHandler[1])) {
                            $controllerName = $routeHandler[0];
                            $methodName = $routeHandler[1];

                            $controller = Container::getInstance();
                            AppProvider::registerServices($controller);
                            $controller = $controller->get($controllerName);

                            if (method_exists($controller, $methodName)) {
                                call_user_func_array([$controller, $methodName], array_merge([$requestObject], $matches));
                                return;
                            }
                            Response::json(['error' => 'Método do controlador não encontrado.'], 500);
                            return;
                        }
                        if (is_callable($routeHandler)) {
                            $response = call_user_func_array($routeHandler, $matches);
                            Response::json($response);
                            return;
                        }
                        Response::json(['error' => 'Handler inválido.'], 500);
                        return;
                    }
                }
            }
        }
    }


    private static function runMiddlewares($request, array $middlewares): ?array
    {
        foreach ($middlewares as $middleware) {
            $middlewareClass = "App\\Http\\Controllers\\Middleware\\" . ucfirst($middleware);

            if (!class_exists($middlewareClass)) {
                return [
                    'status' => 500,
                    'body' => ['error' => "Middleware {$middleware} não encontrado."]
                ];
            }

            $next = function ($req) {
                return null;
            };
            $result = $middlewareClass::handle($request, $next);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    private static function normalizePath($path)
    {
        $normalized = rtrim(parse_url($path, PHP_URL_PATH), '/');
        return $normalized === '' ? '/' : $normalized;
    }
}
