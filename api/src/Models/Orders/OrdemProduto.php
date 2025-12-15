<?php

namespace App\Models\Orders;

use App\Models\Trait\ModelTrait;

class OrdemProduto
{
    use ModelTrait;

    public const TABLE = 'service_order_products';

    public int $order_id;
    public int $product_id;
    public ?int $quantidade;
    public ?float $valor;
    public ?string $created_at;
    public ?string $updated_at;

    public function fill(array $data): OrdemProduto
    {
        $ordemProduto = new OrdemProduto();
        $ordemProduto->setAttributes($data);
        return $ordemProduto;
    }
}
