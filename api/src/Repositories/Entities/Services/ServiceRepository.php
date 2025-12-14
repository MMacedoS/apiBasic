<?php

namespace App\Repositories\Entities\Services;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Services\Service;
use App\Repositories\Contracts\Services\IServiceRepository;
use App\Repositories\Traits\FindTrait;

class ServiceRepository extends Singleton implements IServiceRepository
{
    use FindTrait;

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
            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->model->getTable()} 
                (uuid, nome, descricao, valor, categoria, duracao, situacao) 
                VALUES 
                (:uuid, :nome, :descricao, :valor, :categoria, :duracao, :situacao)"
            );
            $stmt->bindParam(':uuid', $service->uuid);
            $stmt->bindParam(':nome', $service->nome);
            $stmt->bindParam(':descricao', $service->descricao);
            $stmt->bindParam(':valor', $service->valor);
            $stmt->bindParam(':categoria', $service->categoria);
            $stmt->bindParam(':duracao', $service->duracao);
            $stmt->bindParam(':situacao', $service->situacao);
            $stmt->execute();
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
            $service = $this->model->fill($data);
            $fieldsToUpdate = [];
            $params = [];

            if (!empty($service->nome)) {
                $fieldsToUpdate[] = 'nome = :nome';
                $params[':nome'] = $service->nome;
            }
            if (!empty($service->descricao)) {
                $fieldsToUpdate[] = 'descricao = :descricao';
                $params[':descricao'] = $service->descricao;
            }

            if (!is_null($service->valor)) {
                $fieldsToUpdate[] = 'valor = :valor';
                $params[':valor'] = $service->valor;
            }

            if (!is_null($service->categoria)) {
                $fieldsToUpdate[] = 'categoria = :categoria';
                $params[':categoria'] = $service->categoria;
            }

            if (!is_null($service->duracao)) {
                $fieldsToUpdate[] = 'duracao = :duracao';
                $params[':duracao'] = $service->duracao;
            }

            if (!is_null($service->situacao)) {
                $fieldsToUpdate[] = 'situacao = :situacao';
                $params[':situacao'] = $service->situacao;
            }

            if (empty($fieldsToUpdate)) {
                return $this->findById($id);
            }

            $query = "UPDATE {$this->model->getTable()} SET " . implode(', ', $fieldsToUpdate) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->bindValue(':id', $id);
            $stmt->execute();
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

    public function existsByField(string $field, $value): bool
    {
        $query = "SELECT COUNT(*) as count FROM {$this->model->getTable()} WHERE $field = :value";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }
}
