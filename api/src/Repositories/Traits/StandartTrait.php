<?php

namespace App\Repositories\Traits;

trait StandartTrait
{
    private function prepareUpdatingFields(array $data, $object): array
    {
        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $fields[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        return [$fields, $params];
    }

    public function save($params, $object)
    {
        if (empty($params) || !$object) {
            return false;
        }

        [$fields, $params] = $this->prepareUpdatingFields($params, $object);

        $fieldsStr = implode(', ', $fields);
        $query = "UPDATE {$this->model->getTable()} SET {$fieldsStr} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->prepareBindings($stmt, $params);
        $stmt->bindValue(':id', $object->id);
        return $stmt->execute();
    }

    public function toCreate($params)
    {
        [$fields, $params] = $this->prepareCreatingFields($params);

        $fieldsStr = implode(', ', $fields);
        $query = "INSERT INTO {$this->model->getTable()} SET {$fieldsStr}";
        $stmt = $this->conn->prepare($query);
        $this->prepareBindings($stmt, $params);
        return $stmt->execute();
    }

    private function prepareCreatingFields($object): array
    {
        $fields = [];
        $params = [];

        foreach (get_object_vars($object) as $key => $value) {
            if ($value !== null) {
                $fields[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        return [$fields, $params];
    }

    private function prepareWhereClause(array $criteria, array &$params): string
    {
        $conditions = [];
        foreach ($criteria as $field => $value) {
            $paramKey = ':' . $field;
            $conditions[] = "{$field} = {$paramKey}";
            $params[$paramKey] = $value;
        }
        return implode(' AND ', $conditions);
    }

    private function prepareBindings($stmt, array $params): void
    {
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
    }

    public function toDelete(int $id): bool
    {
        $query = "DELETE FROM {$this->model->getTable()} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
