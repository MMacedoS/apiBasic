<?php

namespace App\Models\Users;

use App\Models\Trait\ModelTrait;

class Token
{
    use ModelTrait;

    public const TABLE = 'token_access';

    public int $id;
    public string $token;

    public function fill(array $data): Token
    {
        $this->id = $data['id'] ?? 0;
        $this->token = $data['token'] ?? '';

        return $this;
    }
}
