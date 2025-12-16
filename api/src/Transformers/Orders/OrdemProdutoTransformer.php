<?php

namespace App\Transformers\Orders;

use App\Models\Orders\OrdemProduto;
use App\Repositories\Entities\Products\ProdutoRepository;
use App\Transformers\Products\ProdutoTransformer;

class OrdemProdutoTransformer
{

    public static function transform(OrdemProduto $orderData): array
    {
        return [
            'product' => self::prepareProduct($orderData->product_id),
            'quantity' => $orderData->quantidade,
            'amount' => $orderData->valor,
            'created_at' => $orderData->created_at,
            'updated_at' => $orderData->updated_at,
        ];
    }

    public static function transformCollection(array $ordersData): array
    {
        return array_map(function (OrdemProduto $order) {
            return self::transform($order);
        }, $ordersData);
    }

    public static function KeysTransform(): array
    {
        $transformed = [];

        if (isset($data['product'])) {
            $transformed['product_id'] = $data['product'];
        }
        if (isset($data['quantity'])) {
            $transformed['quantidade'] = $data['quantity'];
        }
        if (isset($data['amount'])) {
            $transformed['valor'] = $data['amount'];
        }
        return $transformed;
    }

    private static function prepareProduct(?int $productId): ?array
    {
        if (is_null($productId)) {
            return null;
        }

        $productRepository = ProdutoRepository::getInstance();
        $product = $productRepository->findById($productId);

        if (is_null($product)) {
            return null;
        }

        return ProdutoTransformer::transform($product);
    }
}
