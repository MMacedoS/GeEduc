<?php

namespace App\Repositories\Plan;

use App\Config\Database;
use App\Models\Plan\Plano;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class PlanoRepository {
    const CLASS_NAME = Plano::class;
    const TABLE = 'planos';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $usuarioRepository;
    protected $pessoaFisicaRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Plano();
        $this->usuarioRepository = new UsuarioRepository(); 
        $this->pessoaFisicaRepository = new PessoaFisicaRepository(); 
    }

    public function allPlans(array $params = [])
    {
        $sql = "SELECT 
            p.*
            FROM " . self::TABLE . " p   
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['nome'])) {
            $conditions[] = "p.nome = :nome";
            $bindings[':nome'] = $params['nome'];
        }

        if (isset($params['descricao'])) {
            $conditions[] = "p.descricao = :descricao";
            $bindings[':descricao'] = $params['descricao'];
        }

        if (isset($params['ativo'])) {
            $conditions[] = "p.ativo = :ativo";
            $bindings[':ativo'] = $params['ativo'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function create(array $data)
    {
        $plan = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    nome = :nome,
                    descricao = :descricao,
                    valor = :valor,
                    ativo = :ativo
                "
            );

            $create = $stmt->execute([
                ':uuid' => $plan->uuid,
                ':nome' => $plan->nome,
                ':descricao' => $plan->descricao,
                ':valor' => $plan->valor,
                ':ativo' => $plan->ativo
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($plan->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id) 
    {
        $plan = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    nome = :nome,
                    descricao = :descricao,
                    valor = :valor,
                    ativo = :ativo 
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':nome' => $plan->nome,
                ':descricao' => $plan->descricao,
                ':valor' => $plan->valor,
                ':ativo' => $plan->ativo,
                ':id' => $id
            ]);

            if (!$update) {
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
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

    public function planByAmmount(string $valor): ?Plano
    {
        try {
            // Base SQL
            $sql = "SELECT p.* FROM " . self::TABLE . " p";
            
            // Inicializa condições e bindings
            $conditions = [];
            $bindings = [];
    
            // Condições dinâmicas
            if (!empty($params['active'])) {
                $conditions[] = "p.ativo = :ativo";
                $bindings[':ativo'] = $valor;
            }
    
            // Adiciona condições ao SQL
            if ($conditions) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
    
            $sql .= " ORDER BY p.created_at DESC LIMIT 1";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
    
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            $result = $stmt->fetch(); 
            return $result !== false ? $result : null;
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}