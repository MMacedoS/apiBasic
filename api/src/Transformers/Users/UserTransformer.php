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

    public static function keysTransform(array $data): array
    {
        $transformed = [];

        if (isset($data['name'])) {
            $transformed['nome'] = $data['name'];
        }
        if (isset($data['email'])) {
            $transformed['email'] = $data['email'];
        }
        if (isset($data['password'])) {
            $transformed['senha'] = $data['password'];
        }
        if (isset($data['access'])) {
            $transformed['acesso'] = $data['access'];
        }
        if (isset($data['status'])) {
            $transformed['situacao'] = $data['status'];
        }

        return $transformed;
    }
}
