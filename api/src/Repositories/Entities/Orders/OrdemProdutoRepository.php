<?php

namespace App\Repositories\Entities\Orders;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Orders\OrdemProduto;
use App\Repositories\Contracts\Orders\IOrdemProdutoRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class OrdemProdutoRepository extends Singleton implements IOrdemProdutoRepository
{
    use StandartTrait, FindTrait;

    public function __construct()
    {
        $this->model = new OrdemProduto();
        $this->conn = Database::getInstance()->getConnections();
    }

    public function assignProdutoToOrdem(int $ordemId, int $produtoId): bool
    {
        if (empty($ordemId) || empty($produtoId)) {
            return false;
        }

        $query = "INSERT INTO ordem_produtos (ordem_id, produto_id) VALUES (:ordem_id, :produto_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->bindParam(':produto_id', $produtoId);
        return $stmt->execute();
    }

    public function removeProdutoFromOrdem(int $ordemId, int $produtoId): bool
    {
        if (empty($ordemId) || empty($produtoId)) {
            return false;
        }

        $query = "DELETE FROM ordem_produtos WHERE ordem_id = :ordem_id AND produto_id = :produto_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->bindParam(':produto_id', $produtoId);
        return $stmt->execute();
    }

    public function listProdutosByOrdem(int $ordemId): array
    {
        if (empty($ordemId)) {
            return [];
        }

        $query = "SELECT * FROM ordem_produtos WHERE ordem_id = :ordem_id";
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

        $query = "
            SELECT SUM(p.preco) as total_cost
            FROM ordem_produtos op
            JOIN produtos p ON op.produto_id = p.id
            WHERE op.ordem_id = :ordem_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['total_cost'] ?? 0.0;
    }
}
