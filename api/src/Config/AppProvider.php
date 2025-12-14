<?php

namespace App\Config;

use App\Repositories\Contracts\Person\IPessoaRepository;
use App\Repositories\Contracts\Services\IServiceRepository;
use App\Repositories\Contracts\Users\IUserRepository;
use App\Repositories\Entities\Person\PessoaRepository;
use App\Repositories\Entities\Services\ServiceRepository;
use App\Repositories\Entities\Users\UserRepository;

class AppProvider
{
    public static function registerServices(Container $container): void
    {
        $container->set(IUserRepository::class, UserRepository::getInstance());
        $container->set(IServiceRepository::class, ServiceRepository::getInstance());
        $container->set(IPessoaRepository::class, PessoaRepository::getInstance());
    }
}
