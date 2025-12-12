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
        $this->id = $data['id'] ?? 0;
        $this->uuid = $data['uuid'] ?? $this->generateUuid();
        $this->nome = $data['nome'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->senha = $data['senha'] ?? '';
        $this->acesso = $data['acesso'] ?? '';
        $this->situacao = $data['situacao'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->updated_at = $data['updated_at'] ?? '';

        return $this;
    }
}
