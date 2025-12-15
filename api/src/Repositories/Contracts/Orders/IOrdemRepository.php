<?php

namespace App\Repositories\Contracts\Orders;

use App\Repositories\Contracts\BaseInterface;

interface IOrdemRepository extends BaseInterface
{
    public function getOrdensByCustomerId(int $customerId): array;
    public function getOrdensByStatus(string $status): array;
    public function updateOrdemStatus(int $ordemId, string $status): bool;
}
