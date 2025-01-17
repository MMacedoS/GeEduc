<?php

namespace App\Repositories\Person;

use App\Config\Database;
use App\Models\Person\PessoaFisica;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PessoaFisicaRepository {
    const CLASS_NAME = PessoaFisica::class;
    const TABLE = 'pessoa_fisica';
    
    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new PessoaFisica();
    }

    public function allPersons()
    {
        $stmt = $this->conn->query(
        "SELECT 
           p.*
            FROM " . self::TABLE . " p 
        ");
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function create(array $data)
    {
        $existingPerson = $this->findPessoaFisica($data);
        if ($existingPerson) {
            return $existingPerson;
        }

        $pessoa_fisica = $this->model->create($data);
    
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    usuario_id = :usuario_id,
                    nome = :nome,
                    doc = :doc,
                    tipo_doc = :tipo_doc,
                    telefone = :telefone,
                    nome_mae = :nome_mae,
                    nome_pai = :nome_pai,
                    genero = :genero,
                    endereco = :endereco,
                    email = :email"
            );
    
            $create = $stmt->execute([
                ':uuid' => $pessoa_fisica->uuid,
                ':usuario_id' => $pessoa_fisica->usuario_id,
                ':nome' => $pessoa_fisica->nome,
                ':doc' => $pessoa_fisica->doc,
                ':tipo_doc' => $pessoa_fisica->tipo_doc,
                ':telefone' => $pessoa_fisica->telefone,
                ':nome_pai' => $pessoa_fisica->nome_pai,
                ':nome_mae' => $pessoa_fisica->nome_mae,
                ':genero' => $pessoa_fisica->genero,
                ':endereco' => $pessoa_fisica->endereco,
                ':email' => $pessoa_fisica->email
            ]);
    
            if (!$create) {
                return null;
            }
    
            return $this->findByUuid($pessoa_fisica->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }    

    public function update(array $data, int $id)
    {
        $pessoa_fisica = $this->model->create(
            $data
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . "
                    set 
                    nome = :nome,
                    doc = :doc,
                    tipo_doc = :tipo_doc,
                    telefone = :telefone,
                    nome_mae = :nome_mae,
                    nome_pai = :nome_pai,
                    genero = :genero,
                    endereco = :endereco,
                    ativo = :ativo,
                    email = :email,
                    updated_at = NOW()
                WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':nome' => $pessoa_fisica->nome,
                ':doc' => $pessoa_fisica->doc,
                ':tipo_doc' => $pessoa_fisica->tipo_doc,
                ':nome_pai' => $pessoa_fisica->nome_pai,
                ':nome_mae' => $pessoa_fisica->nome_mae,
                ':genero' => $pessoa_fisica->genero,
                ':telefone' => $pessoa_fisica->telefone,
                ':endereco' => $pessoa_fisica->endereco,
                ':ativo' => $pessoa_fisica->ativo,
                ':email' => $pessoa_fisica->email
            ]);

            if (!$updated) {        
                return null;
            }
            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function findPessoaFisica(array $criteria): ?array
    {
        try {
            $conditions = [];
            $params = [];

            if (!empty($criteria['nome'])) {
                $conditions[] = "nome = :nome";
                $params[':nome'] = $criteria['nome'];
            }
            if (!empty($criteria['email'])) {
                $conditions[] = "email = :email";
                $params[':email'] = $criteria['email'];
            }
            if (!empty($criteria['doc'])) {
                $conditions[] = "doc = :doc";
                $params[':doc'] = $criteria['doc'];
            }

            if (empty($conditions)) {
                return null; 
            }

            $sql = "SELECT * FROM " . self::TABLE . " WHERE " . implode(' AND ', $conditions);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);          

            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            $result = $stmt->fetch();  

            return $result ?: null; 

            return $result ?: null; 
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function delete(int $id) 
    {
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . " 
             SET ativo = 0 
             WHERE id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }
}