<?php

namespace App\Repositories\Contracts\Orders;

interface IOrdemProdutoRepository
{
    public function assignProdutoToOrdem(int $ordemId, int $produtoId): bool;
    public function removeProdutoFromOrdem(int $ordemId, int $produtoId): bool;
    public function listProdutosByOrdem(int $ordemId): array;
    public function calculateTotalCost(int $ordemId): float;
}
