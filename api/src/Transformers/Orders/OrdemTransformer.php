<?php

namespace App\Transformers\Orders;

use App\Models\Orders\Ordem;
use App\Repositories\Entities\Customers\ClienteRepository;
use App\Repositories\Entities\Orders\OrdemProdutoRepository;
use App\Repositories\Entities\Orders\OrdemServicoRepository;
use App\Transformers\Customers\ClienteTransformer;

class OrdemTransformer
{
    public static function transform(Ordem $orderData): array
    {
        return [
            'id' => $orderData->uuid,
            'code' => $orderData->id,
            'customer' => self::prepareCustomer($orderData->customer_id),
            'status' => $orderData->situacao,
            'services' => self::prepareServices($orderData->id),
            'products' => self::prepareProducts($orderData->id),
            'total_amount' => self::calculateTotalAmount($orderData->id),
            'description' => $orderData->descricao,
            'technical_report' => $orderData->laudo_tecnico ?? null,
            'observations' => $orderData->observacoes ?? null,
            'opened_at' => $orderData->data_abertura,
            'closed_at' => $orderData->data_fechamento ?? null,
            'created_at' => $orderData->created_at,
            'updated_at' => $orderData->updated_at,
        ];
    }

    public static function transformCollection(array $ordersData): array
    {
        return array_map(function (Ordem $order) {
            return self::transform($order);
        }, $ordersData);
    }

    public static function KeysTransform(array $data): array
    {
        $transformed = [];

        if (isset($data['customer_id'])) {
            $transformed['customer_id'] = $data['customer_id'];
        }
        if (isset($data['status'])) {
            $transformed['situacao'] = $data['status'];
        }
        if (isset($data['description'])) {
            $transformed['descricao'] = $data['description'];
        }
        if (isset($data['technical_report'])) {
            $transformed['laudo_tecnico'] = $data['technical_report'];
        }
        if (isset($data['observations'])) {
            $transformed['observacoes'] = $data['observations'];
        }
        if (isset($data['opened_at'])) {
            $transformed['data_abertura'] = $data['opened_at'];
        }
        if (isset($data['closed_at'])) {
            $transformed['data_fechamento'] = $data['closed_at'];
        }
        if (isset($data['services'])) {
            $transformed['servicos'] = $data['services'];
        }
        if (isset($data['products'])) {
            $transformed['produtos'] = $data['products'];
        }
        return $transformed;
    }

    private static function prepareServices(int $orderId): array
    {
        $orderServiceRepository = OrdemServicoRepository::getInstance();
        $services = $orderServiceRepository->listServicosByOrdem($orderId);
        if (empty($services)) {
            return [];
        }
        return OrdemServicoTransformer::transformCollection($services);
    }

    private static function prepareProducts(int $orderId): array
    {
        $orderProductRepository = OrdemProdutoRepository::getInstance();
        $products = $orderProductRepository->listProdutosByOrdem($orderId);
        if (empty($products)) {
            return [];
        }
        return OrdemProdutoTransformer::transformCollection($products);
    }

    private static function calculateTotalAmount(int $orderId): float
    {
        $orderServiceRepository = OrdemServicoRepository::getInstance();
        $orderProductRepository = OrdemProdutoRepository::getInstance();

        $services = $orderServiceRepository->calculateTotalCost($orderId);
        $products = $orderProductRepository->calculateTotalCost($orderId);
        $total = $services + $products;
        return number_format($total, 2, '.', '');
    }

    private static function prepareCustomer(?int $customerId): ?array
    {
        if (is_null($customerId)) {
            return null;
        }

        $clienteRepository = ClienteRepository::getInstance();
        $cliente = $clienteRepository->findById($customerId);

        if (is_null($cliente)) {
            return null;
        }

        return ClienteTransformer::transform($cliente);
    }
}
