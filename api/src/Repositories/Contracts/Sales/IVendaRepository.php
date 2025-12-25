<?php

namespace App\Repositories\Contracts\Sales;

use App\Repositories\Contracts\BaseInterface;

interface IVendaRepository extends BaseInterface
{
    public function getVendasByClienteId(int $clienteId): array;
    public function getVendasBySituacao(string $situacao): array;
    public function updateVendaSituacao(int $vendaId, string $situacao): bool;
}
