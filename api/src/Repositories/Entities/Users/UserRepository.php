<?php

namespace App\Repositories\Entities\Users;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Users\User;
use App\Repositories\Contracts\Users\IUserRepository;
use App\Repositories\Traits\FindTrait;

class UserRepository extends Singleton implements IUserRepository
{
    use FindTrait;

    public function __construct()
    {
        $this->model = new User();
        $this->conn =  Database::getInstance()->getConnection();
        dd('UserRepository instantiated');
    }

    public function findAll(array $criteria = [])
    {
        return [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@gmail.com'
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@gmail.com'
            ]
        ];
    }

    public function create(array $data)
    {
        // Implementação para criar um novo usuário
        return [];
    }


    public function update(int $id, array $data)
    {
        // Implementação para atualizar um usuário existente
        return null;
    }

    public function delete(int $id)
    {
        // Implementação para deletar um usuário
        return false;
    }

    public function authenticate(string $email, string $password)
    {
        // Implementação para autenticar um usuário
        return null;
    }
}
