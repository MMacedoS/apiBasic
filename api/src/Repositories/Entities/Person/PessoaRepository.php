<?php

namespace App\Repositories\Entities\Person;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Person\Pessoa;
use App\Repositories\Contracts\Person\IPessoaRepository;
use App\Repositories\Entities\Users\UserRepository;
use App\Repositories\Traits\FindTrait;
use App\Repositories\Traits\StandartTrait;

class PessoaRepository extends Singleton implements IPessoaRepository
{
    use FindTrait, StandartTrait;

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->model = new Pessoa();
        $this->conn = Database::getInstance()->getConnection();
        $this->userRepository = UserRepository::getInstance();
    }

    public function findByEmail(string $email)
    {
        $table = $this->model->getTable();
        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            $pessoa = new Pessoa();
            $pessoa->setAttributes($data);
            return $pessoa;
        }

        return null;
    }

    public function findByUserId(string $userId)
    {
        $table = $this->model->getTable();
        $stmt = $this->conn->prepare("SELECT * FROM {$table} WHERE user_id = :user_id LIMIT 1");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data) {
            $pessoa = new Pessoa();
            $pessoa->setAttributes($data);
            return $pessoa;
        }

        return null;
    }

    public function create(array $data)
    {
        if (empty($data)) {
            return null;
        }

        if (isset($data['email'])) {
            $pessoaExists = $this->findByEmail($data['email']);
            if (!is_null($pessoaExists)) {
                return $pessoaExists;
            }
        }

        try {
            $pessoa = $this->model->fill($data);

            $user = $this->userRepository->create([
                'email' => $pessoa->email,
                'nome' => $pessoa->nome,
                'situacao' => 'active',
            ]);

            if (is_null($user)) {
                return null;
            }

            $pessoa->user_id = $user->id;

            $create = $this->toCreate($pessoa);

            if (!$create) {
                return null;
            }
            return $this->findByUuid($pessoa->uuid);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function update(int $id, array $data)
    {
        if (empty($data)) {
            return null;
        }

        $pessoaCurrent = $this->findById($id);
        if (is_null($pessoaCurrent)) {
            return null;
        }

        try {
            $saved = $this->save($data, $pessoaCurrent);

            if (!$saved) {
                return null;
            }

            if (!is_null($pessoaCurrent->user_id)) {
                $this->userRepository->update($pessoaCurrent->user_id, $data);
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $pessoa = $this->findById($id);
        if (is_null($pessoa)) {
            return false;
        }

        $this->conn->beginTransaction();
        try {
            $query = "DELETE FROM {$this->model->getTable()} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();

            if ($pessoa->user_id) {
                $userDeleted = $this->userRepository->delete($pessoa->user_id);
                if (!$userDeleted) {
                    $this->conn->rollBack();
                    return false;
                }
            }
            $this->conn->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
