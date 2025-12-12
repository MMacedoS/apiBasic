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
                        if (is_array($handler)) {
                            $controllerName = $handler[0];
                            $methodName = $handler[1];

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
                        if (is_callable($handler)) {
                            $response = call_user_func_array($handler, $matches);
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


    private static function normalizePath($path)
    {
        $normalized = rtrim(parse_url($path, PHP_URL_PATH), '/');
        // Se o caminho era só "/", mantém ele
        return $normalized === '' ? '/' : $normalized;
    }
}
