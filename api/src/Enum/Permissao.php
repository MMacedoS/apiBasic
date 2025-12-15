<?php

namespace App\Enum;

enum Permissao: string
{
    case ADMIN = 'admin';
    case STANDART = 'padrao';
    case CLIENT = 'cliente';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::STANDART => 'padrao',
            self::CLIENT => 'cliente',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                'create-user',
                'update-user',
                'delete-user',
                'view-user',
                'create-customer',
                'update-customer',
                'delete-customer',
                'view-customer',
                'create-service',
                'update-service',
                'delete-service',
                'view-service',
            ],
            self::STANDART => [
                'create-customer',
                'update-customer',
                'view-customer',
                'create-service',
                'update-service',
                'view-service',
            ],
            self::CLIENT => [
                'view-service',
                'view-customer',
            ],
        };
    }

    public static function hasPermission(string $role, string $permission): bool
    {
        $perm = self::from($role);
        return in_array($permission, $perm->permissions());
    }
}
