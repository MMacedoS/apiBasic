<?php

namespace App\Models\Users;

class Token
{
    public int $id;
    public string $token;

    public function getTable(): string
    {
        return 'token_access';
    }

    public function fill(array $data): Token
    {
        $this->id = $data['id'] ?? 0;
        $this->token = $data['token'] ?? '';

        return $this;
    }
}
