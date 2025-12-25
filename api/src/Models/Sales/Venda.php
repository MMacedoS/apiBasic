<?php

namespace App\Models\Sales;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Venda
{
    use ModelTrait, UuidTrait;

    public const TABLE = 'sales';

    public int $id;
    public string $uuid;
    public int $customer_id;
    public float $valor;
    public string $situacao = 'aberta';
    public ?string $created_at;
    public ?string $updated_at;


    public function fill(array $data): Venda
    {
        $sale = new Venda();
        $sale->setAttributes($data);
        $sale->uuid = $data['uuid'] ?? $this->generateUuid();
        return $sale;
    }
}
