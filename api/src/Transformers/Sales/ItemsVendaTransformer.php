<?php

namespace App\Transformers\Sales;

use App\Models\Sales\ItensVenda;
use App\Repositories\Entities\Products\ProdutoRepository;
use App\Transformers\Products\ProdutoTransformer;

class ItemsVendaTransformer
{
    public static function transform(ItensVenda $itemVendaData): array
    {
        return [
            'id' => $itemVendaData->uuid,
            'code' => $itemVendaData->id,
            'product' => self::prepareProduct($itemVendaData->product_id),
            'quantidade' => $itemVendaData->quantidade,
            'preco_unitario' => $itemVendaData->preco_unitario,
            'total_preco' => $itemVendaData->total_preco,
            'created_at' => $itemVendaData->created_at,
            'updated_at' => $itemVendaData->updated_at,
        ];
    }

    public static function transformCollection(array $itemsVendaData): array
    {
        return array_map(function ($itemVenda) {
            return self::transform($itemVenda);
        }, $itemsVendaData);
    }

    private static function prepareProduct(int $productId): array
    {
        if (empty($productId) || $productId <= 0) {
            return [];
        }

        $produtoRepository = ProdutoRepository::getInstance();
        $product = $produtoRepository->findById($productId);

        return ProdutoTransformer::transform($product);
    }
}
