<?php

namespace App\Models\Users;

use App\Models\Trait\UuidTrait;

class User
{
    use UuidTrait;

    public int $id;
    public string $uuid;
    public string $nome;
    public string $email;
    public string $senha;
    public string $acesso;
    public string $situacao;
    public string $created_at;
    public string $updated_at;

    public function getTable(): string
    {
        return 'users';
    }

    public function fill(array $data): User
    {
        $user = new User();
        $user->id = $data['id'] ?? 0;
        $user->uuid = $data['uuid'] ?? $this->generateUuid();
        $user->nome = $data['nome'] ?? '';
        $user->email = $data['email'] ?? '';
        $user->senha = $data['senha'] ?? '';
        $user->acesso = $data['acesso'] ?? '';
        $user->situacao = $data['situacao'] ?? '';
        $user->created_at = $data['created_at'] ?? '';
        $user->updated_at = $data['updated_at'] ?? '';

        return $user;
    }
}
