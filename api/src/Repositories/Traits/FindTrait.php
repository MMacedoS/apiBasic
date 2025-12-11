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
}
