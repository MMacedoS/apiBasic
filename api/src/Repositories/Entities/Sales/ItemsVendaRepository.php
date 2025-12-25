<?php

namespace App\Repositories\Entities\Sales;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Sales\ItensVenda;
use App\Repositories\Contracts\Sales\IItemsVendaRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class ItemsVendaRepository extends Singleton implements IItemsVendaRepository
{
    use StandartTrait, FindTrait;

    public function __construct()
    {
        $this->model = new ItensVenda();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        $itensVenda = $this->model->fill($data);

        return $this->toCreate($itensVenda);
    }

    public function allItemsBySaleId(int $saleId): array
    {
        try {
            $query = "SELECT * FROM {$this->model->getTable()} WHERE sale_id = :sale_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sale_id', $saleId, \PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->prepareModels($results);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return [];
        }
    }

    public function addItemToSale(array $data)
    {
        $create = $this->model->fill($data);
        return $this->toCreate($create);
    }
    public function removeItemFromSale(int $itemId): bool
    {
        $query = "DELETE FROM {$this->model->getTable()} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $itemId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateItemQuantity(int $itemId, int $quantity): bool
    {
        $query = "UPDATE {$this->model->getTable()} SET quantidade = :quantidade WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantidade', $quantity, \PDO::PARAM_INT);
        $stmt->bindParam(':id', $itemId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateItemPrice(int $itemId, float $price): bool
    {
        $query = "UPDATE {$this->model->getTable()} SET preco_unitario = :preco_unitario WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':preco_unitario', $price);
        $stmt->bindParam(':id', $itemId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getItemsByProductId(int $productId): array
    {
        $query = "SELECT * FROM {$this->model->getTable()} WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->prepareModels($results);
    }

    public function removeAllItemsBySaleId(int $saleId): bool
    {
        $query = "DELETE FROM {$this->model->getTable()} WHERE sale_id = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $saleId, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
