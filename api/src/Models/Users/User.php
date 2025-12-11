<?php

namespace App\Models\Users;

class User
{
    public int $id;
    public string $uuid;
    public string $name;
    public string $email;
    public string $password;
    public string $created_at;
    public string $updated_at;

    public function getTable(): string
    {
        return 'users';
    }

    public function fill(array $data): User
    {
        $this->id = $data['id'] ?? 0;
        $this->uuid = $data['uuid'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
        $this->updated_at = $data['updated_at'] ?? '';

        return $this;
    }
}
