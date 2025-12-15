<?php

namespace App\Repositories\Contracts\Orders;

interface IOrdemServicoRepository
{
    public function assignServicoToOrdem(int $ordemId, int $servicoId): bool;
    public function removeServicoFromOrdem(int $ordemId, int $servicoId): bool;
    public function listServicosByOrdem(int $ordemId): array;
    public function calculateTotalCost(int $ordemId): float;
}
