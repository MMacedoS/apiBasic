<?php

namespace App\Repositories\Entities\Services;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Services\Service;
use App\Repositories\Contracts\Services\IServiceRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class ServiceRepository extends Singleton implements IServiceRepository
{
    use FindTrait, StandartTrait;

    public function __construct()
    {
        $this->model = new Service();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function findByNome(string $nome)
    {
        return $this->findByAttribute('nome', $nome);
    }

    public function findByCategoria(string $categoria)
    {
        return $this->findByAttribute('categoria', $categoria);
    }

    public function findBySituacao(string $situacao)
    {
        return $this->findByAttribute('situacao', $situacao);
    }

    public function findByValorRange(float $minValor, float $maxValor)
    {
        $table = $this->model->getTable();
        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE valor BETWEEN :minValor AND :maxValor");
        $stmt->bindParam(':minValor', $minValor);
        $stmt->bindParam(':maxValor', $maxValor);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $services = [];
        foreach ($results as $row) {
            $services[] = $this->model->fill($row);
        }
        return $services;
    }

    public function findByDuracao(int $duracao)
    {
        return $this->findByAttribute('duracao', $duracao);
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $service = $this->model->fill($data);

            $create = $this->toCreate($service);

            if (!$create) {
                return null;
            }
            return $this->findByUuid($service->uuid);
        } catch (\Throwable $th) {
            dd("Error creating service: " . $th->getMessage());
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return null;
        }

        $serviceCurrent = $this->findById($id);
        if (is_null($serviceCurrent)) {
            return null;
        }

        try {
            $saved = $this->save($data, $serviceCurrent);

            if (!$saved) {
                return null;
            }
            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function delete(int $id)
    {
        if (is_null($id) || $id <= 0) {
            return false;
        }

        try {
            $stmt = $this->conn
                ->prepare("DELETE FROM {$this->model->getTable()} WHERE id = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
