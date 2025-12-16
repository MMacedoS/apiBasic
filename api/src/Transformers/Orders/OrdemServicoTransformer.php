<?php

namespace App\Transformers\Orders;

use App\Models\Orders\OrdemServico;
use App\Repositories\Entities\Services\ServiceRepository;
use App\Transformers\Services\ServiceTransformer;

class OrdemServicoTransformer
{
    public static function transform(OrdemServico $orderData): array
    {
        return [
            'service' => self::prepareService($orderData->service_id),
            'created_at' => $orderData->created_at,
            'updated_at' => $orderData->updated_at,
        ];
    }

    public static function transformCollection(array $ordersData): array
    {
        return array_map(function (OrdemServico $order) {
            return self::transform($order);
        }, $ordersData);
    }

    private static function prepareService(?int $serviceId): ?array
    {
        if (is_null($serviceId)) {
            return null;
        }

        $serviceRepository = ServiceRepository::getInstance();
        $service = $serviceRepository->findById($serviceId);

        if (is_null($service)) {
            return null;
        }

        return ServiceTransformer::transform($service);
    }
}
