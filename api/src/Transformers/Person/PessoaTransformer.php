<?php

namespace App\Transformers\Person;

use App\Models\Person\Pessoa;
use App\Models\Users\User;
use App\Repositories\Entities\Users\UserRepository;
use App\Transformers\Users\UserTransformer;

class PessoaTransformer
{
    public static function transform(Pessoa $data): array
    {
        return [
            'id' => $data->uuid ?? null,
            'code' => $data->id ?? null,
            'username' => (object)self::prepareUser($data->user_id) ?? null,
            'name' => $data->nome ?? null,
            'email' => $data->email ?? null,
            'phone' => $data->telefone ?? null,
            'type_doc' => $data->tipo_doc ?? null,
            'doc' => $data->doc ?? null,
            'birth_date' => $data->data_nascimento ?? null,
            'gender' => $data->genero ?? null,
            'photo' => $data->foto ?? null,
            'address' => $data->endereco ?? null,
            'city' => $data->cidade ?? null,
            'uf' => $data->uf ?? null,
            'zip_code' => $data->cep ?? null,
            'country' => $data->pais ?? null,
            'status' => $data->situacao ?? null,
            'created_at' => $data->created_at ?? null,
            'updated_at' => $data->updated_at ?? null,
        ];
    }

    public static function transformCollection(array $data): array
    {
        return array_map(fn($transformData) => self::transform($transformData), $data);
    }

    public static function keysTransform(array $data): array
    {
        $transformed = [];

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
        if (isset($data['password'])) {
            $transformed['senha'] = $data['password'];
        }
        if (isset($data['access'])) {
            $transformed['acesso'] = $data['access'];
        }
        if (isset($data['status'])) {
            $transformed['situacao'] = $data['status'];
        }

        return $transformed;
    }

    private static function prepareUser(?int $userId): ?array
    {
        if (is_null($userId)) {
            return null;
        }

        $userRepository = UserRepository::getInstance();
        $user = $userRepository->findById($userId);
        if (is_null($user)) {
            return null;
        }

        $userTransformer = UserTransformer::class;
        $transformedUser = $userTransformer::transform($user);
        return $transformedUser;
    }
}
