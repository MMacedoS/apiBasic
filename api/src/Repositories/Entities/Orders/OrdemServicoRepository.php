<?php

namespace App\Repositories\Entities\Orders;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Orders\OrdemServico;
use App\Repositories\Contracts\Orders\IOrdemServicoRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class OrdemServicoRepository extends Singleton implements IOrdemServicoRepository
{
    use StandartTrait, FindTrait;

    public function __construct()
    {
        $this->model = new OrdemServico();
        $this->conn = Database::getInstance()->getConnections();
    }

    public function assignServicoToOrdem(int $ordemId, int $servicoId): bool
    {
        if (empty($ordemId) || empty($servicoId)) {
            return false;
        }

        $query = "INSERT INTO ordem_servicos (ordem_id, servico_id) VALUES (:ordem_id, :servico_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->bindParam(':servico_id', $servicoId);
        return $stmt->execute();
    }

    public function removeServicoFromOrdem(int $ordemId, int $servicoId): bool
    {
        if (empty($ordemId) || empty($servicoId)) {
            return false;
        }

        $query = "DELETE FROM ordem_servicos WHERE ordem_id = :ordem_id AND servico_id = :servico_id";
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

        $query = "SELECT * FROM ordem_servicos WHERE ordem_id = :ordem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function calculateTotalCost(int $ordemId): float
    {
        if (empty($ordemId)) {
            return 0.0;
        }

        $query = "SELECT SUM(s.preco) as total_cost 
                  FROM ordem_servicos os 
                  JOIN servicos s ON os.servico_id = s.id 
                  WHERE os.ordem_id = :ordem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_cost'] ?? 0.0;
    }
}
