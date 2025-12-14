<?php

namespace App\Transformers\Services;

use App\Models\Services\Service;

class ServiceTransformer
{
    public static function transform(Service $service): array
    {
        return [
            'code' => $service->id ?? null,
            'id' => $service->uuid ?? null,
            'name' => $service->nome ?? null,
            'description' => $service->descricao ?? null,
            'value' => $service->valor ?? null,
            'category' => $service->categoria ?? null,
            'duration' => $service->duracao ?? null,
            'status' => $service->situacao ?? null,
            'created_at' => $service->created_at ?? null,
            'updated_at' => $service->updated_at ?? null,
        ];
    }

    public static function transformCollection(array $servicesData): array
    {
        return array_map(fn($serviceData) => self::transform($serviceData), $servicesData);
    }

    public static function keysTransform(array $data): array
    {
        $transformed = [];

        if (isset($data['name'])) {
            $transformed['nome'] = $data['name'];
        }
        if (isset($data['description'])) {
            $transformed['descricao'] = $data['description'];
        }
        if (isset($data['value'])) {
            $transformed['valor'] = $data['value'];
        }
        if (isset($data['category'])) {
            $transformed['categoria'] = $data['category'];
        }
        if (isset($data['duration'])) {
            $transformed['duracao'] = $data['duration'];
        }
        if (isset($data['status'])) {
            $transformed['situacao'] = $data['status'];
        }

        return $transformed;
    }
}
