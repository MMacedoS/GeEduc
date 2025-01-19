<?php

namespace App\Repositories\Activitie;

use App\Config\Database;
use App\Models\Activitie\Atividade;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class AtividadeRepository {
    const CLASS_NAME = Atividade::class;
    const TABLE = 'atividade';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Atividade();
    }

    public function allActivities(array $params = [])
    {
        $sql = "SELECT 
                a.*, JSON_OBJECT(
                    'id', a.id,
                    'uuid', a.uuid,
                    'turma_disciplina_id', a.turma_disciplina_id,
                    'tipo', a.tipo,
                    'valor', a.valor
                ) AS activies_details
            FROM atividade a";
    
        $conditions = [];
        $bindings = [];
    
        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'a.turma_disciplina_id = :turma_disciplina_id';
            $bindings[':turma_disciplina_id'] = $params['class_discipline_id'];
        }
    
        if (isset($params['active'])) {
            $conditions[] = 'a.ativo = :ativo';
            $bindings[':ativo'] = $params['active'];
        }
    
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " ORDER BY a.created_at DESC";
    
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function create(array $params) 
    {
        $class = $this->model->create($params);

        LoggerHelper::logInfo(json_encode($class));

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    turma_disciplina_id = :turma_disciplina_id,
                    tipo = :tipo,
                    valor = :valor,
                    ativo = :ativo                    
                "
            );

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':turma_disciplina_id' => $class->turma_disciplina_id,
                ':valor' => $class->valor,
                ':tipo' => $class->tipo,
                ':ativo' => $class->ativo
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($class->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function update(array $data, int $id) 
    {
        $atividades = $this->findById($id);

        $class = $this->model->update($data, $atividades);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    turma_disciplina_id = :turma_disciplina_id,
                    tipo = :tipo,
                    valor = :valor,
                    ativo = :ativo    
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':turma_disciplina_id' => $class->turma_disciplina_id,
                ':valor' => $class->valor,
                ':tipo' => $class->tipo,
                ':ativo' => $class->ativo,
                ':id' => $id
            ]);

            if (!$update) {
                return null;
            }

            return $class;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
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