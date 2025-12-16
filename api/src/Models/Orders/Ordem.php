<?php

namespace App\Models\Orders;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Ordem
{
    use UuidTrait, ModelTrait;

    public const TABLE = 'service_orders';

    public ?int $id = null;
    public string $uuid;
    public int $customer_id;
    public ?string $data_abertura;
    public ?string $data_fechamento;
    public ?string $descricao;
    public ?string $observacoes;
    public ?string $laudo_tecnico;
    public ?string $situacao;
    public ?string $created_at;
    public ?string $updated_at;

    public function fill(array $data): Ordem
    {
        $ordem = new Ordem();
        $ordem->setAttributes($data);
        $ordem->uuid = $data['uuid'] ?? $this->generateUuid();
        return $ordem;
    }
}
