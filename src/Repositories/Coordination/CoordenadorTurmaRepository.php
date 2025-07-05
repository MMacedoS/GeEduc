<?php

namespace App\Repositories\Coordination;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Coordination\ICoordenadorTurmaRepository;
use App\Models\Coordination\CoordenadorTurma;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class CoordenadorTurmaRepository extends SingletonInstance implements ICoordenadorTurmaRepository {
    const CLASS_NAME = CoordenadorTurma::class;
    const TABLE = 'coordenador_as_turma';

    use FindTrait;


    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new CoordenadorTurma();
    }

    public function allCoordinatorClass(array $params = []) 
    {
        $sql = "SELECT 
                ct.*, 
                JSON_OBJECT(
                    'id', t.id,
                    'uuid', t.uuid,
                    'nome', t.nome,
                    'turno', t.turno,
                    'visivel', t.visivel,
                    'ativo', t.ativo
                ) AS turma_details
            FROM " . self::TABLE . " ct
            LEFT JOIN turmas t ON t.id = ct.turma_id
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['class_id'])) {
            $conditions[] = "ct.turma_id = :turma_id";
            $bindings[':turma_id'] = $params['class_id'];
        }
        if (isset($params['coordenador_id'])) {
            $conditions[] = "ct.coordenador_id = :coordenador_id";
            $bindings[':coordenador_id'] = $params['coordenador_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY t.ordem DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS);
    }

    public function saveAll(array $data, $turma_id)
    {
        if (empty($data) || empty($data['coordinator_id'])) {
            return null;
        }
        
        try {           
            $this->conn->beginTransaction();

            $this->deleteByClassId($turma_id);
            
            foreach ($data['coordinator_id'] as $key => $value) {
                $data['coordenador_id'] = $value;
                $data['turma_id'] = $turma_id;
                $coordinator = $this->create($data);
            }
           
            $this->conn->commit();
            return $coordinator;
    
        } catch (\Throwable $th) {
            $this->conn->rollBack();
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function create(array $data): ?CoordenadorTurma
    {
        $coordinator = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET                 
                    coordenador_id = :coordenador_id,
                    turma_id = :turma_id,
                    uuid = :uuid
                "
            );

            $create = $stmt->execute([
                ':coordenador_id' => $coordinator->coordenador_id,
                ':turma_id' => $coordinator->turma_id,
                ':uuid' => $coordinator->uuid
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($coordinator->uuid);
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

    public function deleteByClassId(int $id)
    {
        $stmt = $this->conn
        ->prepare(
            "DELETE FROM " . self::TABLE . " 
             WHERE turma_id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }
}