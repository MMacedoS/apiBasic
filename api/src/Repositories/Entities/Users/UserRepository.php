<?php

namespace App\Repositories\Entities\Users;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Users\User;
use App\Repositories\Contracts\Users\IUserRepository;
use App\Repositories\Traits\FindTrait;

class UserRepository extends Singleton implements IUserRepository
{
    use FindTrait;

    public function __construct()
    {
        $this->model = new User();
        $this->conn =  Database::getInstance()->getConnection();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            $user = $this->model->fill($data);
            $hashedPassword = password_hash($user->senha, PASSWORD_BCRYPT);
            $query = "INSERT INTO 
                {$this->model->getTable()} 
                    (uuid, nome, email, senha) 
                VALUES 
                    (:uuid, :name, :email, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':uuid', $user->uuid);
            $stmt->bindParam(':name', $user->nome);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();

            return $this->findByUuid($user->uuid);
        } catch (\PDOException $e) {
            dd("Error creating user: " . $e->getMessage());
            return null;
        }
    }


    public function update(int $id, array $data)
    {
        if (empty($data) || is_null($id) || $id <= 0) {
            return null;
        }

        try {
            $user = $this->model->fill($data);
            $fieldsToUpdate = [];
            $params = [];

            if (!empty($user->nome)) {
                $fieldsToUpdate[] = 'nome = :name';
                $params[':name'] = $user->nome;
            }
            if (!empty($user->email)) {
                $fieldsToUpdate[] = 'email = :email';
                $params[':email'] = $user->email;
            }
            if (!empty($user->senha)) {
                $fieldsToUpdate[] = 'senha = :password';
                $hashedPassword = password_hash($user->senha, PASSWORD_BCRYPT);
                $params[':password'] = $hashedPassword;
            }
            if (!empty($user->acesso)) {
                $fieldsToUpdate[] = 'acesso = :access';
                $params[':access'] = $user->acesso;
            }
            if (!empty($user->situacao)) {
                $fieldsToUpdate[] = 'situacao = :status';
                $params[':status'] = $user->situacao;
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
        // Implementação para deletar um usuário
        return false;
    }

    public function authenticate(string $email, string $password)
    {
        if (empty($email) || empty($password)) {
            return null;
        }

        $query = "SELECT * FROM {$this->model->getTable()} WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result && password_verify($password, $result['senha'])) {
            return $this->model->fill($result);
        }

        return null;
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
