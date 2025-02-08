<?php

namespace App\Repositories\Person;

use App\Config\Database;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Models\Person\PessoaFisica;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PessoaFisicaRepository implements IPessoaFisicaRepository {
    const CLASS_NAME = PessoaFisica::class;
    const TABLE = 'pessoa_fisica';
    
    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
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

    public function personByUserId(int $user_id)
    {
        $stmt = $this->conn->query(
            "SELECT 
            p.*
            FROM " . self::TABLE . " p 
            WHERE p.usuario_id = '$user_id'
            LIMIT 1
            "
        );

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  
        if (is_null($register)) {
            return null;
        }
    
        return $register;       
    }

    public function create(array $data)
    {
        $existingPerson = $this->findPessoaFisica($data);
        if (!is_null($existingPerson)) {
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
                    data_nascimento = :data_nascimento,
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
                ':data_nascimento' => $pessoa_fisica->data_nascimento,
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
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }    

    public function update(array $data, int $id)
    {
        $pessoa_fisica = $this->findById($id);

        if (is_null($pessoa_fisica)) {
            return null;
        }

        $pessoa_fisica = $this->model->update(
            $data,
            $pessoa_fisica
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
                    data_nascimento = :data_nascimento,
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
                ':data_nascimento' => $pessoa_fisica->data_nascimento,
                ':email' => $pessoa_fisica->email
            ]);

            if (!$updated) {        
                return null;
            }
            return $this->findById($id);
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function findPessoaFisica(array $criteria): ?PessoaFisica
    {
        try {
            $conditions = [];
            $params = [];
            if (!empty($criteria['name'])) {
                $conditions[] = "nome = :nome";
                $params[':nome'] = $criteria['name'];
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

            return $result; 
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
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

    public function remove($id) :?bool 
    {
        
        $pessoa_fisica = $this->findById((int)$id);
       
        if (is_null($pessoa_fisica)) {
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            if($delete) {
                return true;
            }
            return false;
        } catch(\Throwable $th) {
            dd($th->getMessage());
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}