<?php

namespace App\Repositories\Bank_account;

use App\Config\Database;
use App\Models\Bank_account\ContaBancaria;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ContaBancariaRepository {
    const CLASS_NAME = ContaBancaria::class;
    const TABLE = 'contas_bancarias';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new ContaBancaria();
    }

    public function allBanks(array $params = [])
    {
        $sql = "SELECT 
            c.*
            FROM " . self::TABLE . " c   
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['agency'])) {
            $conditions[] = "c.agencia = :agencia";
            $bindings[':agencia'] = $params['agency'];
        }

        if (isset($params['account'])) {
            $conditions[] = "c.conta = :conta";
            $bindings[':conta'] = $params['account'];
        }

        if (isset($params['bank'])) {
            $conditions[] = "c.banco = :banco";
            $bindings[':banco'] = $params['bank'];
        }

        if (isset($params['active'])) {
            $conditions[] = "c.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function create(array $data)
    {
        $conta = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    convenio = :convenio, 
                    agencia = :agencia, 
                    conta = :conta, 
                    banco = :banco, 
                    codigo_banco = :codigo_banco
                "
            );

            $create = $stmt->execute([
                ':uuid' => $conta->uuid,
                ':convenio' => $conta->convenio,
                ':agencia' => $conta->agencia,
                ':conta' => $conta->conta,
                ':banco' => $conta->banco,
                ':codigo_banco' => $conta->codigo_banco
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($conta->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id): ?ContaBancaria
    {
        $conta = $this->findById($id);
        if (!$conta) {
            return null;
        }

        $conta->update($data);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    convenio = :convenio, 
                    agencia = :agencia, 
                    conta = :conta, 
                    banco = :banco, 
                    codigo_banco = :codigo_banco, 
                    ativo = :ativo
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':convenio' => $conta->convenio,
                ':agencia' => $conta->agencia,
                ':conta' => $conta->conta,
                ':banco' => $conta->banco,
                ':codigo_banco' => $conta->codigo_banco,
                ':ativo' => $conta->ativo,
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
}