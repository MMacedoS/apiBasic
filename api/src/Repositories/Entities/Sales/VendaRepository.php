<?php

namespace App\Repositories\Entities\Sales;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Products\Produto;
use App\Models\Sales\Venda;
use App\Repositories\Contracts\Sales\IVendaRepository;
use App\Repositories\Entities\Products\ProdutoRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class VendaRepository extends Singleton implements IVendaRepository
{
    use FindTrait, StandartTrait;

    public function __construct()
    {
        $this->model = new Venda();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getVendasByClienteId(int $clienteId): array
    {
        $query = "SELECT * FROM {$this->model->getTable()} WHERE cliente_id = :cliente_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cliente_id', $clienteId);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->prepareModels($results);
    }

    public function getVendasBySituacao(string $situacao): array
    {
        $query = "SELECT * FROM {$this->model->getTable()} WHERE situacao = :situacao";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':situacao', $situacao);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->prepareModels($results);
    }

    public function updateVendaSituacao(int $vendaId, string $situacao): bool
    {
        $query = "UPDATE {$this->model->getTable()} SET situacao = :situacao WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':situacao', $situacao);
        $stmt->bindParam(':id', $vendaId);
        return $stmt->execute();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }


        $venda = $this->model->fill($data);

        $exists = $this->findAll(['customer_id' => $venda->customer_id, 'situacao' => $venda->situacao]);

        if (count($exists) > 0) {
            return $exists[0];
        }

        $this->conn->beginTransaction();
        try {
            $created = $this->toCreate($venda);
            if (!$created) {
                $this->conn->rollBack();
                return null;
            }
            $venda->id = (int)$this->conn->lastInsertId();

            if ($data['itens'] ?? false) {
                $itemsCreated = $this->createItemsVenda($venda->id, $data['itens']);
                if (!$itemsCreated) {
                    $this->conn->rollBack();
                    return null;
                }
            }

            $this->conn->commit();
            return $venda;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function createItemsVenda(int $vendaId, array $items): bool
    {
        $itemsVendaRepo = ItemsVendaRepository::getInstance();

        foreach ($items as $itemData) {
            $itemData['sale_id'] = $vendaId;
            $product = ProdutoRepository::getInstance()->findByUuid($itemData['product_id']);
            if (is_null($product)) {
                continue;
            }
            $itemData['product_id'] = $product->id;
            $itemData['total_preco'] = $itemData['quantidade'] * $itemData['preco_unitario'];
            $createdItem = $itemsVendaRepo->addItemToSale($itemData);
            if (!$createdItem) {
                return false;
            }
        }

        return true;
    }

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $sale = $this->findById($id);
            if (is_null($sale)) {
                return null;
            }

            $updated = $this->save($data, $sale);
            if (!$updated) {
                return null;
            }

            if (isset($data['itens'])) {
                $itemsVendaRepo = ItemsVendaRepository::getInstance();
                $itemsVendaRepo->removeAllItemsBySaleId($sale->id);
                $itemsCreated = $this->createItemsVenda($sale->id, $data['itens']);
                if (!$itemsCreated) {
                    return null;
                }
            }

            return $sale;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function delete(int $id)
    {
        try {
            $sale = $this->findById($id);
            if (is_null($sale)) {
                return false;
            }

            $itemsVendaRepo = ItemsVendaRepository::getInstance();
            $itemsDeleted = $itemsVendaRepo->removeAllItemsBySaleId($sale->id);
            if (!$itemsDeleted) {
                return false;
            }
            return $this->toDelete($sale->id);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
