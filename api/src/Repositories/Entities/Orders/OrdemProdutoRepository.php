<?php

namespace App\Repositories\Entities\Orders;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Orders\OrdemProduto;
use App\Repositories\Contracts\Orders\IOrdemProdutoRepository;
use App\Repositories\Entities\Products\ProdutoRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class OrdemProdutoRepository extends Singleton implements IOrdemProdutoRepository
{
    use StandartTrait, FindTrait;

    public function __construct()
    {
        $this->model = new OrdemProduto();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function assignProdutoToOrdem(int $ordemId, int $produtoId, float $valor, int $quantidade): bool
    {
        if (empty($ordemId) || empty($produtoId)) {
            return false;
        }

        $service_order = $this->model->fill([
            'order_id' => $ordemId,
            'product_id' => $produtoId,
            'valor' => $valor,
            'quantidade' => $quantidade
        ]);

        $created = $this->toCreate($service_order);
        return $created !== null;
    }

    public function removeProdutoFromOrdem(int $ordemId, int $produtoId): bool
    {
        if (empty($ordemId) || empty($produtoId)) {
            return false;
        }

        $query = "DELETE FROM {$this->model->getTable()} WHERE order_id = :ordem_id AND product_id = :produto_id";
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

        $query = "SELECT SUM(p.preco) AS total_cost
            FROM {$this->model->getTable()} op
            JOIN products p ON op.product_id = p.id
            WHERE op.order_id = :ordem_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem_id', $ordemId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_cost'] ?? 0.0;
    }

    public function loadProductByProductUuid(string $productUuid)
    {
        $productRepository = ProdutoRepository::getInstance();
        return $productRepository->findByUuid($productUuid);
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
}
