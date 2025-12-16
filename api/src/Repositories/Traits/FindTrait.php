<?php

namespace App\Repositories\Traits;

use PDO;

trait FindTrait
{
    public $model;
    protected ?PDO $conn;

    public function findById(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->model->getTable()} WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $this->model->fill($result);
        }

        return null;
    }

    public function findByUuid(string $uuid)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->model->getTable()} WHERE uuid = :uuid LIMIT 1");
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $this->model->fill($result);
        }

        return null;
    }

    public function findAll(array $criteria = [])
    {
        $sql = "SELECT * FROM " . $this->model->getTable();
        $params = [];
        $conditions = [];

        if (!empty($criteria)) {
            $conditions = $this->buildWhereClause($criteria, $params);
            $sql .= " WHERE " . $conditions;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->prepareModels($results);
    }

    private function buildWhereClause(array $criteria, array &$params): string
    {
        $conditions = [];
        foreach ($criteria as $field => $value) {
            if ($field === 'name') {
                $condition[] = "$field LIKE ?";
                $criteria[] = "%$value%";
                continue;
            }

            if ($field === 'email') {
                $condition[] = "$field LIKE ?";
                $criteria[] = "%$value%";
                continue;
            }
            $conditions[] = "$field = :$field";
            $params[":$field"] = $value;
        }
        return implode(' AND ', $conditions);
    }

    private function prepareModels(array $results): array
    {
        $entities = [];
        foreach ($results as $result) {
            $entities[] = $this->model->fill($result);
        }
        return $entities;
    }

    public function findByAttribute(string $attribute, $value)
    {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->model->getTable()} WHERE {$attribute} = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->prepareModels($results);
    }

    public function existsByField(string $field, $value): bool
    {
        try {
            if (is_null($value)) {
                return false;
            }
            $query = "SELECT COUNT(*) as count FROM {$this->model->getTable()} WHERE $field = :value";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            $result = $stmt->fetch();

            return $result['count'] > 0;
        } catch (\PDOException $e) {
            dd(1);
            return false;
        }
    }
}
