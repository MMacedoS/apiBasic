<?php

namespace App\Transformers\Products;

use App\Models\Products\Produto;

class ProdutoTransformer
{
    public static function transform(Produto $productData): array
    {
        return [
            'id' => $productData->uuid,
            'code' => $productData->id,
            'name' => $productData->nome,
            'description' => $productData->descricao,
            'price' => $productData->preco,
            'status' => $productData->situacao,
            'stock' => $productData->estoque,
            'created_at' => $productData->created_at,
            'updated_at' => $productData->updated_at,
        ];
    }

    public static function transformCollection(array $productsData): array
    {
        return array_map(function (Produto $product) {
            return self::transform($product);
        }, $productsData);
    }

    public static function KeysTransform(array $data): array
    {
        $transformed = [];

        if (isset($data['name'])) {
            $transformed['nome'] = $data['name'];
        }
        if (isset($data['description'])) {
            $transformed['descricao'] = $data['description'];
        }
        if (isset($data['price'])) {
            $transformed['preco'] = $data['price'];
        }
        if (isset($data['status'])) {
            $transformed['situacao'] = $data['status'];
        }
        if (isset($data['stock'])) {
            $transformed['estoque'] = $data['stock'];
        }
        return $transformed;
    }
}
