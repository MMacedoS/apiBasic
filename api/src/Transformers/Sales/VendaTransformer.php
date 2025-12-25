<?php

namespace App\Transformers\Sales;

use App\Models\Sales\Venda;
use App\Repositories\Entities\Customers\ClienteRepository;
use App\Repositories\Entities\Sales\ItemsVendaRepository;
use App\Transformers\Customers\ClienteTransformer;

class VendaTransformer
{
    public static function transform(Venda $vendaData): array
    {
        return [
            'id' => $vendaData->uuid,
            'code' => $vendaData->id,
            'customer' => self::prepareCustomer($vendaData->customer_id),
            'items' => self::prepareItems($vendaData->id),
            'total' => $vendaData->valor,
            'status' => $vendaData->situacao,
            'created_at' => $vendaData->created_at,
            'updated_at' => $vendaData->updated_at,
        ];
    }

    public static function transformCollection(array $vendasData): array
    {
        return array_map(function ($venda) {
            return self::transform($venda);
        }, $vendasData);
    }

    private static function prepareCustomer(int $customerId): array
    {
        if (empty($customerId) || $customerId <= 0) {
            return [];
        }

        $customer = ClienteRepository::getInstance()->findById($customerId);

        return ClienteTransformer::transform($customer);
    }

    private static function prepareItems(int $saleId): array
    {
        $itemsVendaRepository = ItemsVendaRepository::getInstance();
        $items = $itemsVendaRepository->allItemsBySaleId($saleId);

        return ItemsVendaTransformer::transformCollection($items);
    }

    public static function keysTransform(array $keys): array
    {
        $transformedKeys = [];
        foreach ($keys as $key) {
            switch ($key) {
                case 'cliente_id':
                    $transformedKeys[] = 'customer_id';
                    break;
                case 'valor':
                    $transformedKeys[] = 'total';
                    break;
                case 'situacao':
                    $transformedKeys[] = 'status';
                    break;
                case 'code':
                    $transformedKeys[] = 'id';
                    break;

                default:
                    $transformedKeys[] = $key;
                    break;
            }
        }
        return $transformedKeys;
    }

    public static function transformKeysToSnakeCase(array $data): array
    {
        $transformed = [];

        foreach ($data as $key => $value) {
            $snakeKey = self::convertToSnakeCase($key);

            if (!is_array($value)) {
                $transformed[$snakeKey] = $value;
                continue;
            }

            if (array_keys($value) !== range(0, count($value) - 1)) {
                $transformed[$snakeKey] = self::transformKeysToSnakeCase($value);
                continue;
            }

            $transformed[$snakeKey] = array_map(function ($item) {
                return is_array($item) ? self::transformKeysToSnakeCase($item) : $item;
            }, $value);
        }

        return $transformed;
    }

    private static function convertToSnakeCase(string $key): string
    {
        $keyMap = [
            'items' => 'itens',
            'quantity' => 'quantidade',
            'unit_price' => 'preco_unitario',
            'total' => 'valor',
            'status' => 'situacao',
        ];

        if (isset($keyMap[$key])) {
            return $keyMap[$key];
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
    }
}
