<?php

namespace App\Repositories\Contracts;

use App\Config\Database;
use App\Interfaces\Contracts\IContratoRepository;
use App\Models\Contracts\Contrato;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ContratoRepository implements IContratoRepository {
    const CLASS_NAME = Contrato::class;
    const TABLE = 'contratos';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Contrato();
    }

    public function create(array $data)
    {
        $contract = $this->model->create($data);
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    estudante_id = :estudante_id,
                    ano_letivo = :ano_letivo,
                    document_id = :document_id,
                    conteudo = :content,
                    quantidade_assinaturas = :quantidade_assinaturas
                "
            );

            $create = $stmt->execute([
                ':uuid' => $contract->uuid,
                ':estudante_id' => $contract->estudante_id,
                ':ano_letivo' => $contract->ano_letivo,
                ':document_id' => $contract->document_id,
                ':content' => $contract->conteudo,
                ':quantidade_assinaturas' => $contract->quantidade_assinaturas ?? null
            ]);

            if (!$create) {
                return null;
            }

            $created = $this->findByUuid($contract->uuid);
            return $created;
        } catch (\Throwable $th) {
            dd($th->getMessage());
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function updateSignature(string $document_id, string $contract) 
    {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    quantidade_assinaturas = quantidade_assinaturas + 1,
                    conteudo = :content
                WHERE document_id = :document_id
                "
            );

            $update = $stmt->execute([
                ':content' => $contract,
                ':document_id' => $document_id
            ]);

            if (!$update) {
                return null;
            }

            return $this->findByPublicID($document_id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação update: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function findByPublicID($document_id) {
        try {
            $sql = "SELECT * FROM " . self::TABLE . " WHERE document_id = :document_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':document_id' => $document_id]);

            $contract = $stmt->fetch(\PDO::FETCH_OBJ);

            return $contract;
        } catch(\Throwable $th) {
            LoggerHelper::logError("Erro na consulta: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }
}