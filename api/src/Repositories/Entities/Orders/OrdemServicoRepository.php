<?php

namespace App\Repositories\Entities\Orders;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Orders\OrdemServico;
use App\Repositories\Contracts\Orders\IOrdemServicoRepository;
use App\Repositories\Entities\Services\ServiceRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class OrdemServicoRepository extends Singleton implements IOrdemServicoRepository
{
    use StandartTrait, FindTrait;

    public function __construct()
    {
        $this->model = new OrdemServico();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function assignServicoToOrdem(int $ordemId, int $servicoId, ?float $valor = 0.0): bool
    {
        if (empty($ordemId) || empty($servicoId)) {
            return false;
        }

        $service_order = $this->model->fill([
            'order_id' => $ordemId,
            'service_id' => $servicoId,
            'valor' => $valor,
        ]);
        $created = $this->toCreate($service_order);
        return $created !== null;
    }

    public function removeServicoFromOrdem(int $ordemId, int $servicoId): bool
    {
        if (empty($ordemId) || empty($servicoId)) {
            return false;
        }

        $query = "DELETE FROM {$this->model->getTable()} WHERE order_id = :ordem_id AND servico_id = :servico_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->bindParam(':servico_id', $servicoId);
        return $stmt->execute();
    }

    public function listServicosByOrdem(int $ordemId): array
    {
        if (empty($ordemId)) {
            return [];
        }

        $query = "SELECT * FROM {$this->model->getTable()} WHERE order_id = :ordem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->prepareModels($results);
    }

    public function calculateTotalCost(int $ordemId): float
    {
        if (empty($ordemId)) {
            return 0.0;
        }

        $query = "SELECT SUM(s.valor) as total_cost 
                  FROM {$this->model->getTable()} os 
                  JOIN services s ON os.service_id = s.id 
                  WHERE os.order_id = :ordem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_cost'] ?? 0.0;
    }

    public function deleteByOrdemId(int $ordemId): bool
    {
        if (empty($ordemId)) {
            return false;
        }

        $query = "DELETE FROM {$this->model->getTable()} WHERE order_id = :ordem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        return $stmt->execute();
    }

    public function loadServiceByServiceUuid(string $serviceUuid)
    {
        $serviceRepository = ServiceRepository::getInstance();
        return $serviceRepository->findByUuid($serviceUuid);
    }
}
