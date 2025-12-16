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
        $this->conn = Database::getInstance()->getConnection();
        $this->ordemServicoRepository = OrdemServicoRepository::getInstance();
        $this->ordemProdutoRepository = OrdemProdutoRepository::getInstance();
    }

    public function getOrdensByCustomerId(int $customerId): array
    {
        $query = "SELECT * FROM {$this->model->getTable()} WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $customerId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrdensByStatus(string $status): array
    {
        $query = "SELECT * FROM {$this->model->getTable()} WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateOrdemStatus(int $ordemId, string $status): bool
    {
        $query = "UPDATE {$this->model->getTable()} SET situacao = :status WHERE id = :id";
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

        $this->conn->beginTransaction();
        try {
            $exists = $this->findAll(
                [
                    'customer_id' => $data['customer_id'] ?? null,
                    'situacao' => $data['situacao'] ?? null,
                    'descricao' => $data['descricao'] ?? null,
                ]
            );

            if (count($exists) > 0) {
                $this->conn->rollBack();
                return null;
            }

            $order = $this->model->fill($data);
            $create = $this->toCreate($order);
            if (!$create) {
                $this->conn->rollBack();
                return null;
            }

            $order->id = (int) $this->conn->lastInsertId();

            if (isset($data['servicos']) && is_array($data['servicos'])) {
                foreach ($data['servicos'] as $servicoId) {
                    $servico = $this->ordemServicoRepository->loadServiceByServiceUuid($servicoId);
                    if (is_null($servico)) {
                        continue;
                    }
                    $this->ordemServicoRepository
                        ->assignServicoToOrdem(
                            $order->id,
                            $servico->id,
                            $servico->valor
                        );
                }
            }

            if (isset($data['produtos']) && is_array($data['produtos'])) {
                foreach ($data['produtos'] as $produtoId) {
                    $produto = $this->ordemProdutoRepository->loadProductByProductUuid($produtoId);
                    if (is_null($produto)) {
                        continue;
                    }
                    $this->ordemProdutoRepository
                        ->assignProdutoToOrdem(
                            $order->id,
                            $produto->id,
                            $produto->preco,
                            1
                        );
                }
            }

            $this->conn->commit();
            return $this->findByUuid($order->uuid);
        } catch (\Throwable $th) {
            dd($th->getMessage());
            $this->conn->rollBack();
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $order = $this->findById($id);
            if (is_null($order)) {
                return null;
            }

            $updated = $this->save($data, $order);

            if (!$updated) {
                return null;
            }

            if (isset($data['servicos']) && is_array($data['servicos'])) {
                $existingServicos = $this->ordemServicoRepository->listServicosByOrdem($order->id);
                $existingServicoIds = array_column($existingServicos, 'servico_id');

                foreach ($data['servicos'] as $servicoId) {
                    if (!in_array($servicoId, $existingServicoIds)) {
                        $servico = $this->ordemServicoRepository->loadServiceByServiceUuid($servicoId);
                        if (is_null($servico)) {
                            continue;
                        }
                        $this->ordemServicoRepository
                            ->assignServicoToOrdem(
                                $order->id,
                                $servico->id,
                                $servico->valor
                            );
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
                        $produto = $this->ordemProdutoRepository->loadProductByProductUuid($produtoId);
                        if (is_null($produto)) {
                            continue;
                        }
                        $this->ordemProdutoRepository
                            ->assignProdutoToOrdem(
                                $order->id,
                                $produto->id,
                                $produto->preco,
                                1
                            );
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
            dd($th->getMessage());
            return null;
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
            $this->ordemServicoRepository->deleteByOrdemId($order->id);
            $this->ordemProdutoRepository->deleteByOrdemId($order->id);
        }

        return $this->toDelete($order->id);
    }
}
