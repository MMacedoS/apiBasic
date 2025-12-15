<?php

namespace App\Repositories\Entities\Products;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Products\Produto;
use App\Repositories\Contracts\Products\IProdutoRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class ProdutoRepository extends Singleton implements IProdutoRepository
{
    use FindTrait, StandartTrait;

    public function __construct()
    {
        $this->model = new Produto();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getProdutosByCategory(string $categoryId): array
    {
        $query = "SELECT * FROM produtos WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProdutosByPriceRange(float $minPrice, float $maxPrice): array
    {
        $query = "SELECT * FROM produtos WHERE price BETWEEN :min_price AND :max_price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':min_price', $minPrice);
        $stmt->bindParam(':max_price', $maxPrice);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateProdutoStock(int $produtoId, int $newStock): bool
    {
        $query = "UPDATE produtos SET stock = :stock WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stock', $newStock);
        $stmt->bindParam(':id', $produtoId);
        return $stmt->execute();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $produto = $this->model->fill($data);
            $create = $this->toCreate($produto);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($produto->uuid);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data) || is_null($id) || $id <= 0) {
            return null;
        }

        try {
            $produto = $this->findById($id);
            if (is_null($produto)) {
                return null;
            }

            $saved = $this->save($data, $produto);
            if (!$saved) {
                return null;
            }

            return $this->findById($id);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function delete(int $id)
    {
        $produto = $this->findById($id);
        if (is_null($produto)) {
            return false;
        }

        return $this->toDelete($produto);
    }

    public function reduceStock(int $produtoId, int $quantity): bool
    {
        $produto = $this->findById($produtoId);
        if (is_null($produto) || $produto->stock < $quantity) {
            return false;
        }

        $newStock = $produto->stock - $quantity;
        return $this->updateProdutoStock($produtoId, $newStock);
    }
    public function increaseStock(int $produtoId, int $quantity): bool
    {
        $produto = $this->findById($produtoId);
        if (is_null($produto)) {
            return false;
        }

        $newStock = $produto->stock + $quantity;
        return $this->updateProdutoStock($produtoId, $newStock);
    }

    public function searchProdutosByName(string $name): array
    {
        $query = "SELECT * FROM produtos WHERE name LIKE :name";
        $stmt = $this->conn->prepare($query);
        $likeName = '%' . $name . '%';
        $stmt->bindParam(':name', $likeName);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
