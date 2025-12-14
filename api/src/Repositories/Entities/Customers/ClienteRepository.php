<?php

namespace App\Repositories\Entities\Customers;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Customer\Cliente;
use App\Repositories\Contracts\Customers\IClienteRepository;
use App\Repositories\Entities\Person\PessoaRepository;
use App\Repositories\Traits\FindTrait;

class ClienteRepository extends Singleton implements IClienteRepository
{
    use FindTrait;
    private PessoaRepository $pessoaRepository;

    public function __construct()
    {
        $this->model = new Cliente();
        $this->conn = Database::getInstance()->getConnection();
        $this->pessoaRepository = PessoaRepository::getInstance();
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        try {
            if (isset($data['email'])) {
                $pessoaExists = $this->pessoaRepository->create($data);
                if (is_null($pessoaExists)) {
                    return null;
                }
                $data['person_id'] = $pessoaExists->id;
            }

            $exists = $this->findByAttribute('person_id', $data['person_id'] ?? 0);

            if (!empty($exists)) {
                return $exists[0];
            }

            $cliente = $this->model->fill($data);
            $query = "INSERT INTO 
                {$this->model->getTable()} 
                    (uuid, person_id, situacao) 
                VALUES 
                    (:uuid, :person_id, :situacao)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':uuid', $cliente->uuid);
            $stmt->bindParam(':person_id', $cliente->person_id);
            $stmt->bindParam(':situacao', $cliente->situacao);
            $stmt->execute();

            return $this->findByUuid($cliente->uuid);
        } catch (\PDOException $e) {
            dd("Error creating customer: " . $e->getMessage());
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data) || is_null($id) || $id <= 0) {
            return null;
        }

        try {
            $cliente = $this->findById($id);
            if (is_null($cliente)) {
                return null;
            }

            $fields = [];
            $params = [];
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
            $fieldsStr = implode(', ', $fields);
            $query = "UPDATE {$this->model->getTable()} SET {$fieldsStr} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return $this->findById($id);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function delete(int $id)
    {
        if (is_null($id) || $id <= 0) {
            return false;
        }

        $customer = $this->findById($id);
        if (is_null($customer)) {
            return false;
        }

        try {
            $query = "DELETE FROM {$this->model->getTable()} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $register = $stmt->execute();

            if ($register) {
                if ($customer->person_id) {
                    $userRepo = PessoaRepository::getInstance();
                    $userRepo->delete($customer->person_id);
                    if (!$userRepo) {
                        return false;
                    }
                }
            }

            return $register;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
