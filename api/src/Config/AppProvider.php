<?php

namespace App\Config;

use App\Repositories\Contracts\Customers\IClienteRepository;
use App\Repositories\Contracts\Employees\IFuncionarioRepository;
use App\Repositories\Contracts\Orders\IOrdemProdutoRepository;
use App\Repositories\Contracts\Orders\IOrdemRepository;
use App\Repositories\Contracts\Orders\IOrdemServicoRepository;
use App\Repositories\Contracts\Person\IPessoaRepository;
use App\Repositories\Contracts\Products\IProdutoRepository;
use App\Repositories\Contracts\Services\IServiceRepository;
use App\Repositories\Contracts\Users\IUserRepository;
use App\Repositories\Entities\Customers\ClienteRepository;
use App\Repositories\Entities\Employees\FuncionarioRepository;
use App\Repositories\Entities\Orders\OrdemProdutoRepository;
use App\Repositories\Entities\Orders\OrdemRepository;
use App\Repositories\Entities\Orders\OrdemServicoRepository;
use App\Repositories\Entities\Person\PessoaRepository;
use App\Repositories\Entities\Products\ProdutoRepository;
use App\Repositories\Entities\Services\ServiceRepository;
use App\Repositories\Entities\Users\UserRepository;

class AppProvider
{
    public static function registerServices(Container $container): void
    {
        $container->set(IUserRepository::class, UserRepository::getInstance());
        $container->set(IServiceRepository::class, ServiceRepository::getInstance());
        $container->set(IPessoaRepository::class, PessoaRepository::getInstance());
        $container->set(IClienteRepository::class, ClienteRepository::getInstance());
        $container->set(IFuncionarioRepository::class, FuncionarioRepository::getInstance());
        $container->set(IOrdemRepository::class, OrdemRepository::getInstance());
        $container->set(IOrdemServicoRepository::class, OrdemServicoRepository::getInstance());
        $container->set(IOrdemProdutoRepository::class, OrdemProdutoRepository::getInstance());
        $container->set(IProdutoRepository::class, ProdutoRepository::getInstance());
    }
}
