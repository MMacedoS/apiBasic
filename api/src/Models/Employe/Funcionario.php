<?php

namespace App\Models\Employe;

use App\Models\Trait\ModelTrait;
use App\Models\Trait\UuidTrait;

class Funcionario
{
    use UuidTrait, ModelTrait;

    private const TABLE = 'employees';

    public int $id;
    public string $uuid;
    public int $person_id;
    public ?string $cargo;
    public ?float $salario;
    public ?string $departamento;
    public ?string $data_admissao;
    public ?string $data_demissao;
    public ?string $situacao;
    public ?string $created_at;
    public ?string $updated_at;

    public function fill(array $data): Funcionario
    {
        $funcionario = new Funcionario();
        $funcionario->setAttributes($data);
        $funcionario->uuid = $data['uuid'] ?? $this->generateUuid();
        return $funcionario;
    }
}
