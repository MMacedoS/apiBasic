<?php

namespace App\Models\Customer;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Cliente
{
    use UuidTrait, ModelTrait;

    private const TABLE = 'customers';

    public int $id;
    public string $uuid;
    public int $person_id;
    public ?string $situacao;
    public ?string $created_at;
    public ?string $updated_at;

    public function fill(array $data): Cliente
    {
        $customer = new Cliente();
        $customer->setAttributes($data);
        $customer->uuid = $data['uuid'] ?? $this->generateUuid();
        return $customer;
    }
}
