<?php

namespace App\Models\Sales;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class ItensVenda
{
    use ModelTrait, UuidTrait;

    public const TABLE = 'sales_items';

    public int $id;
    public string $uuid;
    public int $sale_id;
    public int $product_id;
    public int $quantidade;
    public float $preco_unitario;
    public float $total_preco;
    public string $created_at;
    public string $updated_at;

    public function fill(array $data): ItensVenda
    {
        $itemVenda = new ItensVenda();
        $itemVenda->setAttributes($data);
        $itemVenda->uuid = $data['uuid'] ?? $this->generateUuid();
        return $itemVenda;
    }
}
