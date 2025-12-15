<?php

namespace App\Repositories\Entities\Orders;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Orders\Ordem;
use App\Repositories\Contracts\Orders\IOrdemRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class OrdemRepository extends Singleton implements IOrdemRepository
{
    use FindTrait, StandartTrait;
    private OrdemServicoRepository $ordemServicoRepository;
    private OrdemProdutoRepository $ordemProdutoRepository;

    public function __construct()
    {
        $this->model = new Ordem();
        $this->conn = Database::getInstance()->getConnections();
        $this->ordemServicoRepository = OrdemServicoRepository::getInstance();
        $this->ordemProdutoRepository = OrdemProdutoRepository::getInstance();
    }

    public function getOrdensByCustomerId(int $customerId): array
    {
        $query = "SELECT * FROM ordens WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customerId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrdensByStatus(string $status): array
    {
        $query = "SELECT * FROM ordens WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateOrdemStatus(int $ordemId, string $status): bool
    {
        $query = "UPDATE ordens SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $ordemId);
        return $stmt->execute();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $order = $this->model->fill($data);
            $create = $this->toCreate($order);
            if (!$create) {
                return null;
            }

            if (isset($data['servicos']) && is_array($data['servicos'])) {
                foreach ($data['servicos'] as $servicoId) {
                    $this->ordemServicoRepository->assignServicoToOrdem($order->id, $servicoId);
                }
            }

            if (isset($data['produtos']) && is_array($data['produtos'])) {
                foreach ($data['produtos'] as $produtoId) {
                    $this->ordemProdutoRepository->assignProdutoToOrdem($order->id, $produtoId);
                }
            }

            return $this->findByUuid($order->uuid);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return false;
        }

        try {
            $order = $this->findById($id);
            if (is_null($order)) {
                return false;
            }

            $updated = $this->save($order->id, $data);

            if (!$updated) {
                return false;
            }

            if (isset($data['servicos']) && is_array($data['servicos'])) {
                $existingServicos = $this->ordemServicoRepository->listServicosByOrdem($order->id);
                $existingServicoIds = array_column($existingServicos, 'servico_id');

                foreach ($data['servicos'] as $servicoId) {
                    if (!in_array($servicoId, $existingServicoIds)) {
                        $this->ordemServicoRepository->assignServicoToOrdem($order->id, $servicoId);
                    }
                }

                foreach ($existingServicoIds as $existingServicoId) {
                    if (!in_array($existingServicoId, $data['servicos'])) {
                        $this->ordemServicoRepository->removeServicoFromOrdem($order->id, $existingServicoId);
                    }
                }
            }

            if (isset($data['produtos']) && is_array($data['produtos'])) {
                $existingProdutos = $this->ordemProdutoRepository->listProdutosByOrdem($order->id);
                $existingProdutoIds = array_column($existingProdutos, 'produto_id');

                foreach ($data['produtos'] as $produtoId) {
                    if (!in_array($produtoId, $existingProdutoIds)) {
                        $this->ordemProdutoRepository->assignProdutoToOrdem($order->id, $produtoId);
                    }
                }

                foreach ($existingProdutoIds as $existingProdutoId) {
                    if (!in_array($existingProdutoId, $data['produtos'])) {
                        $this->ordemProdutoRepository->removeProdutoFromOrdem($order->id, $existingProdutoId);
                    }
                }
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        if (is_null($id) || $id <= 0) {
            return false;
        }

        $order = $this->findById($id);

        if (is_null($order)) {
            return false;
        }

        if (isset($order->id)) {
            $servicos = $this->ordemServicoRepository->listServicosByOrdem($order->id);
            foreach ($servicos as $servico) {
                $this->ordemServicoRepository->removeServicoFromOrdem($order->id, $servico['servico_id']);
            }

            $produtos = $this->ordemProdutoRepository->listProdutosByOrdem($order->id);
            foreach ($produtos as $produto) {
                $this->ordemProdutoRepository->removeProdutoFromOrdem($order->id, $produto['produto_id']);
            }
        }

        return $this->toDelete($order->id);
    }
}
