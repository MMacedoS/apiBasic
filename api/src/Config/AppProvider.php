<?php

namespace App\Config;

use App\Repositories\Contracts\Users\IUserRepository;
use App\Repositories\Entities\Users\UserRepository;

class AppProvider
{
    public static function registerServices(Container $container): void
    {
        $container->set(IUserRepository::class, new UserRepository());
    }
}
