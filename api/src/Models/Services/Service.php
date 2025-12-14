<?php

namespace App\Models\Services;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Service
{
    use UuidTrait, ModelTrait;

    private const TABLE = 'services';

    public int $id;
    public string $uuid;
    public string $nome;
    public string $descricao;
    public float $valor;
    public ?string $categoria;
    public ?int $duracao;
    public string $situacao;
    public ?string $created_at;
    public ?string $updated_at;

    public function fill(array $data): Service
    {
        $service = new Service();
        $service->setAttributes($data);
        $service->uuid = $data['uuid'] ?? $this->generateUuid();
        return $service;
    }
}
