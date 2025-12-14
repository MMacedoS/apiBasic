<?php

namespace App\Transformers\Employees;

use App\Models\Employe\Funcionario;
use App\Repositories\Entities\Person\PessoaRepository;
use App\Transformers\Person\PessoaTransformer;

class FuncionarioTransformer
{

    public static function transform(Funcionario $data): array
    {
        return [
            'id' => $data->uuid ?? null,
            'code' => $data->id ?? null,
            'person' => self::preparePerson($data->person_id),
            'job' => $data->cargo,
            'amount' => $data->salario,
            'departure' => $data->departamento,
            'date_admission' => $data->data_admissao,
            'date_dismissal' => $data->data_demissao,
            'status' => $data->situacao,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
        ];
    }

    private static function preparePerson(?int $personId): ?array
    {
        if (is_null($personId)) {
            return null;
        }

        $pessoaRepository = PessoaRepository::getInstance();
        $pessoa = $pessoaRepository->findById($personId);

        if (is_null($pessoa)) {
            return null;
        }

        return PessoaTransformer::transform($pessoa);
    }

    public static function transformCollection(array $data): array
    {
        return array_map(fn($transformData) => self::transform($transformData), $data);
    }

    public static function keysTransform(array $data): array
    {
        $transformed = [];

        if (isset($data['person'])) {
            $transformed['person_id'] = $data['person'];
        }
        if (isset($data['job'])) {
            $transformed['cargo'] = $data['job'];
        }
        if (isset($data['amount'])) {
            $transformed['salario'] = $data['amount'];
        }
        if (isset($data['departure'])) {
            $transformed['departamento'] = $data['departure'];
        }
        if (isset($data['date_admission'])) {
            $transformed['data_admissao'] = $data['date_admission'];
        }
        if (isset($data['date_dismissal'])) {
            $transformed['data_demissao'] = $data['date_dismissal'];
        }
        if (isset($data['status'])) {
            $transformed['situacao'] = $data['status'];
        }

        if (isset($data['name'])) {
            $transformed['nome'] = $data['name'];
        }

        if (isset($data['email'])) {
            $transformed['email'] = $data['email'];
        }
        if (isset($data['phone'])) {
            $transformed['telefone'] = $data['phone'];
        }
        if (isset($data['type_doc'])) {
            $transformed['tipo_doc'] = $data['type_doc'];
        }
        if (isset($data['doc'])) {
            $transformed['doc'] = $data['doc'];
        }
        if (isset($data['birth_date'])) {
            $transformed['data_nascimento'] = $data['birth_date'];
        }
        if (isset($data['gender'])) {
            $transformed['genero'] = $data['gender'];
        }
        if (isset($data['photo'])) {
            $transformed['foto'] = $data['photo'];
        }
        if (isset($data['address'])) {
            $transformed['endereco'] = $data['address'];
        }

        if (isset($data['city'])) {
            $transformed['cidade'] = $data['city'];
        }
        if (isset($data['uf'])) {
            $transformed['uf'] = $data['uf'];
        }
        if (isset($data['zip_code'])) {
            $transformed['cep'] = $data['zip_code'];
        }
        if (isset($data['country'])) {
            $transformed['pais'] = $data['country'];
        }

        return $transformed;
    }
}
