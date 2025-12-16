<?php

namespace App\Models\Orders;

use App\Models\Trait\ModelTrait;

class OrdemServico
{
    use ModelTrait;

    public const TABLE = 'service_order_services';

    public int $order_id;
    public int $service_id;
    public ?float $valor;
    public ?string $created_at;
    public ?string $updated_at;

    public function fill(array $data): self
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }
}
