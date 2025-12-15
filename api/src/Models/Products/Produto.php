<?php

namespace App\Models\Products;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Produto
{
    use UuidTrait, ModelTrait;

    public const TABLE = 'products';

    public int $id;
    public string $uuid;
    public string $nome;
    public ?string $descricao;
    public ?float $preco;
    public ?int $estoque;
    public ?string $situacao;
    public ?string $created_at;
    public ?string $updated_at;


    public function fill(array $data): Produto
    {
        $produto = new Produto();
        $produto->setAttributes($data);
        $produto->uuid = $data['uuid'] ?? $this->generateUuid();
        return $produto;
    }
}
