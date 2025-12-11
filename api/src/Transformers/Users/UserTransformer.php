<?php

namespace App\Transformers\Users;

use App\Models\Users\User;

class UserTransformer
{
    public static function transform(User $user): array
    {
        return [
            'code' => $user->id,
            'id' => $user->uuid,
            'name' => $user->nome,
            'email' => $user->email,
            'access' => $user->acesso,
            'status' => $user->situacao,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    public static function transformCollection(array $users): array
    {
        return array_map(function (User $user) {
            return self::transform($user);
        }, $users);
    }
}
