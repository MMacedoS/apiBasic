<?php

namespace App\Core;

use App\Config\Container;

class Core
{

    public static function dispatch(array $request): void
    {
        $url = $_SERVER['REQUEST_URI'];
        $methodUrl = $_SERVER['REQUEST_METHOD'];

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

                            $controller = Container::getInstance()->get($controllerName);

                            if (method_exists($controller, $methodName)) {
                                $response = call_user_func_array([$controller, $methodName], $matches);
                                header('Content-Type: application/json');
                                echo json_encode($response);
                                return;
                            }
                            http_response_code(500);
                            echo json_encode(['error' => 'Método do controlador não encontrado.']);
                            return;
                        }
                        if (is_callable($handler)) {
                            $response = call_user_func_array($handler, $matches);
                            header('Content-Type: application/json');
                            echo json_encode($response);
                            return;
                        }
                        http_response_code(500);
                        echo json_encode(['error' => 'Handler inválido.']);
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
