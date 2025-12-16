<?php

namespace App\Http\Controllers\Middleware;

use App\Enum\Permissao;
use App\Http\Request\Request;
use App\Http\Request\Response;

class Role
{
    public static function handle(Request $request, $next, $role)
    {
        $user = (object)$request->user();

        $roleLabel = Permissao::from($user->access)->label();

        if ($roleLabel === 'admin') {
            return $next($request);
        }

        if (!$user || ($roleLabel !== $role)) {
            return Response::json(['message' => 'Não autorizado'], 403);
        }

        return $next($request);
    }
}
