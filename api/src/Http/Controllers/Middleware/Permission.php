<?php

namespace App\Http\Controllers\Middleware;

use App\Enum\Permissao;
use App\Http\Request\Request;
use App\Http\Request\Response;

class Permission
{
    public static function handle(Request $request, $next, mixed $permissions)
    {
        $user = (object)$request->user();

        if (!is_array($permissions)) {
            $permission = $permissions;
        }
        if (is_array($permissions)) {
            $permission = null;
            foreach ($permissions as $perm) {
                if (Permissao::hasPermission($user->access, $perm)) {
                    $permission = $perm;
                    break;
                }
            }
        }

        if (!$user || !Permissao::hasPermission($user->access, (string)$permission)) {
            return Response::json(['message' => 'Não autorizado'], 403);
        }

        return $next($request);
    }
}
