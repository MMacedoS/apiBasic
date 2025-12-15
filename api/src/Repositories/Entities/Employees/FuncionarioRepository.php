<?php

namespace App\Repositories\Entities\Employees;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Employe\Funcionario;
use App\Repositories\Contracts\Employees\IFuncionarioRepository;
use App\Repositories\Entities\Person\PessoaRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class FuncionarioRepository extends Singleton implements IFuncionarioRepository
{
    use FindTrait, StandartTrait;
    private PessoaRepository $pessoaRepository;

    public function __construct()
    {
        $this->model = new Funcionario();
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

            $funcionario = $this->model->fill($data);
            $create = $this->toCreate($funcionario);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($funcionario->uuid);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data) || is_null($id) || $id <= 0) {
            return null;
        }

        try {
            $funcionario = $this->findById($id);
            if (is_null($funcionario)) {
                return null;
            }

            $saved = $this->save($data, $funcionario);

            if (!$saved) {
                return null;
            }

            $this->pessoaRepository->update($funcionario->person_id, $data);

            return $this->findById($id);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function delete(int $id)
    {
        $funcionario = $this->findById($id);
        if (is_null($funcionario)) {
            return false;
        }

        $this->conn->beginTransaction();
        try {
            $deleted = $this->toDelete($funcionario->id);

            if (!$deleted) {
                $this->conn->rollBack();
                return false;
            }
            if ($funcionario->person_id) {
                $personRepo = PessoaRepository::getInstance();
                $personDeleted = $personRepo->toDelete($funcionario->person_id);
                if (!$personDeleted) {
                    $this->conn->rollBack();
                    return false;
                }
            }
            $this->conn->commit();
            return $deleted;
        } catch (\PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
