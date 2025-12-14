<?php

namespace App\Repositories\Entities\Person;

use App\Config\Database;
use App\Config\Singleton;
use App\Models\Person\Pessoa;
use App\Repositories\Contracts\Person\IPessoaRepository;
use App\Repositories\Entities\Users\UserRepository;
use App\Repositories\Traits\FindTrait;

class PessoaRepository extends Singleton implements IPessoaRepository
{
    use FindTrait;

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

            $stmt = $this->conn->prepare(
                "INSERT INTO {$this->model->getTable()} 
                (
                    uuid, 
                    user_id,
                    nome, 
                    email, 
                    telefone, 
                    tipo_doc, 
                    doc, 
                    data_nascimento, 
                    genero, 
                    foto, 
                    endereco, 
                    cidade, 
                    uf, 
                    cep, 
                    pais, 
                    situacao
                ) 
                VALUES 
                (
                    :uuid, 
                    :user_id,
                    :nome, 
                    :email, 
                    :telefone, 
                    :tipo_doc, 
                    :doc, 
                    :data_nascimento, 
                    :genero, 
                    :foto, 
                    :endereco, 
                    :cidade, 
                    :uf, 
                    :cep, 
                    :pais, 
                    :situacao
                )"
            );
            $stmt->bindParam(':uuid', $pessoa->uuid);
            $stmt->bindParam(':user_id', $pessoa->user_id);
            $stmt->bindParam(':nome', $pessoa->nome);
            $stmt->bindParam(':email', $pessoa->email);
            $stmt->bindParam(':telefone', $pessoa->telefone);
            $stmt->bindParam(':tipo_doc', $pessoa->tipo_doc);
            $stmt->bindParam(':doc', $pessoa->doc);
            $stmt->bindParam(':data_nascimento', $pessoa->data_nascimento);
            $stmt->bindParam(':genero', $pessoa->genero);
            $stmt->bindParam(':foto', $pessoa->foto);
            $stmt->bindParam(':endereco', $pessoa->endereco);
            $stmt->bindParam(':cidade', $pessoa->cidade);
            $stmt->bindParam(':uf', $pessoa->uf);
            $stmt->bindParam(':cep', $pessoa->cep);
            $stmt->bindParam(':pais', $pessoa->pais);
            $stmt->bindParam(':situacao', $pessoa->situacao);
            $stmt->execute();
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
            $pessoa = $this->model->fill($data);
            $fieldsToUpdate = [];
            $params = [];

            if (!empty($pessoa->nome)) {
                $fieldsToUpdate[] = 'nome = :nome';
                $params[':nome'] = $pessoa->nome;
            }

            if (!empty($pessoa->email)) {
                $fieldsToUpdate[] = 'email = :email';
                $params[':email'] = $pessoa->email;
            }

            if (!is_null($pessoa->telefone)) {
                $fieldsToUpdate[] = 'telefone = :telefone';
                $params[':telefone'] = $pessoa->telefone;
            }

            if (!is_null($pessoa->tipo_doc)) {
                $fieldsToUpdate[] = 'tipo_doc = :tipo_doc';
                $params[':tipo_doc'] = $pessoa->tipo_doc;
            }

            if (!is_null($pessoa->doc)) {
                $fieldsToUpdate[] = 'doc = :doc';
                $params[':doc'] = $pessoa->doc;
            }

            if (!is_null($pessoa->data_nascimento)) {
                $fieldsToUpdate[] = 'data_nascimento = :data_nascimento';
                $params[':data_nascimento'] = $pessoa->data_nascimento;
            }

            if (!is_null($pessoa->genero)) {
                $fieldsToUpdate[] = 'genero = :genero';
                $params[':genero'] = $pessoa->genero;
            }

            if (!is_null($pessoa->foto)) {
                $fieldsToUpdate[] = 'foto = :foto';
                $params[':foto'] = $pessoa->foto;
            }

            if (!is_null($pessoa->endereco)) {
                $fieldsToUpdate[] = 'endereco = :endereco';
                $params[':endereco'] = $pessoa->endereco;
            }

            if (!is_null($pessoa->cidade)) {
                $fieldsToUpdate[] = 'cidade = :cidade';
                $params[':cidade'] = $pessoa->cidade;
            }

            if (!is_null($pessoa->uf)) {
                $fieldsToUpdate[] = 'uf = :uf';
                $params[':uf'] = $pessoa->uf;
            }

            if (!is_null($pessoa->cep)) {
                $fieldsToUpdate[] = 'cep = :cep';
                $params[':cep'] = $pessoa->cep;
            }

            if (!is_null($pessoa->pais)) {
                $fieldsToUpdate[] = 'pais = :pais';
                $params[':pais'] = $pessoa->pais;
            }

            if (!is_null($pessoa->situacao)) {
                $fieldsToUpdate[] = 'situacao = :situacao';
                $params[':situacao'] = $pessoa->situacao;
            }

            if (empty($fieldsToUpdate)) {
                return $this->findById($id);
            }

            $setClause = implode(', ', $fieldsToUpdate);
            $query = "UPDATE {$this->model->getTable()} SET {$setClause} WHERE id = :id";
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
