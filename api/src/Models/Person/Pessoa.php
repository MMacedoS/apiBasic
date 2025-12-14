<?php

namespace App\Models\Person;

use App\Models\Model;
use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Pessoa
{
    use UuidTrait, ModelTrait;

    private const TABLE = 'persons';

    public int $id;
    public string $uuid;
    public ?int $user_id;
    public string $nome;
    public string $email;
    public ?string $telefone;
    public ?string $tipo_doc;
    public ?string $doc;
    public ?string $data_nascimento;
    public ?string $genero;
    public ?string $foto;
    public ?string $endereco;
    public ?string $cidade;
    public ?string $uf;
    public ?string $cep;
    public ?string $pais;
    public string $situacao;
    public ?string $created_at;
    public ?string $updated_at;


    public function fill(array $data): Pessoa
    {
        $pessoa = new Pessoa();
        $pessoa->setAttributes($data);
        $pessoa->uuid = $data['uuid'] ?? $this->generateUuid();
        return $pessoa;
    }
}
