<?php

namespace App\Repositories\Contracts\Products;

use App\Repositories\Contracts\BaseInterface;

interface IProdutoRepository extends BaseInterface
{
    public function getProdutosByCategory(string $category): array;
    public function searchProdutosByName(string $name): array;
    public function updateProdutoStock(int $produtoId, int $newStock): bool;
}
