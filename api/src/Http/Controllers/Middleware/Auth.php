<?php

namespace App\Http\Controllers\Middleware;

use App\Http\JWT\JWT;
use App\Http\Request\Request;

class Auth
{
    public static function handle(Request $request, $next)
    {
        $token = $request->header('Authorization');

        if (is_null($token)) {
            return [
                'status' => 401,
                'body' => ['message' => 'autorização não fornecida']
            ];
        }

        $payload = JWT::validateToken($token);
        if (is_null($payload)) {
            return [
                'status' => 401,
                'body' => ['message' => 'Token inválido ou expirado']
            ];
        }

        $request->setUser($payload);

        return $next($request);
    }
}
