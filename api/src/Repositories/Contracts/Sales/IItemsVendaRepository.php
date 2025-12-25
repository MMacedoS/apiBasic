<?php

namespace App\Repositories\Contracts\Sales;

interface IItemsVendaRepository
{
    public function allItemsBySaleId(int $saleId): array;
    public function addItemToSale(array $data);
    public function removeItemFromSale(int $itemId): bool;
    public function updateItemQuantity(int $itemId, int $quantity): bool;
    public function updateItemPrice(int $itemId, float $price): bool;
    public function getItemsByProductId(int $productId): array;
    public function removeAllItemsBySaleId(int $saleId): bool;
}
