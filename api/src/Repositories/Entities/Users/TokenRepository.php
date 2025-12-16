<?php

namespace App\Repositories\Entities\Users;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Users\Token;
use App\Repositories\Traits\FindTrait;

class TokenRepository extends Singleton
{
    use FindTrait;

    public function __construct()
    {
        $this->model = new Token();
        $this->conn =  Database::getInstance()->getConnection();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $token = $this->model->fill($data);
            $query = "INSERT INTO 
                {$this->model->getTable()} 
                    (token) 
                VALUES 
                    (:token)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':token', $token->token);
            $stmt->execute();

            return $this->findById($this->conn->lastInsertId());
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function delete(string $token)
    {
        if (empty($token)) {
            return false;
        }

        try {
            $query = "DELETE FROM {$this->model->getTable()} WHERE token = :token";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
            return $stmt->execute();
        } catch (\PDOException $e) {
            return false;
        }
    }
}
