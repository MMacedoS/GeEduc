<?php

namespace App\Repositories\Scores;

use App\Config\Database;
use App\Models\Scores\Nota;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class NotaRepository {
    const CLASS_NAME = Nota::class;
    const TABLE = 'notas';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Nota();
    }

    public function allScores(array $params = [])
    {
        $sql = "SELECT
            n.*, 
            JSON_OBJECT(
                'id', td.id,
                'uuid', td.uuid,
                'atividades', JSON_OBJECT(
                    'id', a.id,
                    'uuid', a.uuid,
                    'tipo', a.tipo
                ) 
            ) AS turmas_details
            FROM notas n
            LEFT JOIN atividade a ON n.atividade_id = a.id
            LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
            LEFT JOIN estudante_turma et ON et.id = n.estudante_turma_id";
        
        
        $conditions = [];
        $bindings = [];
        
        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'a.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['bimester_id'])) {
            $conditions[] = 'n.bimestre_id = :bimester_id';
            $bindings[':bimester_id'] = $params['bimester_id'];
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY n.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            
            return $stmt->fetchAll(PDO::FETCH_CLASS, self::CLASS_NAME);    
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function create(array $params)
    {
        $class = $this->model->create($params);
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    atividade_id = :atividade_id,
                    bimestre_id = :bimestre_id,
                    estudante_turma_id = :estudante_turma_id,
                    nota = :nota"
            );

            $idScore = $this->checkIfExistsScore($class);
            if($idScore) {
                $this->removeScore($idScore);
            }

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':atividade_id' => $class->atividade_id,
                ':bimestre_id' => $class->bimestre_id,
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':nota' => $class->nota
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

    private function checkIfExistsScore($class) :?String {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM notas WHERE estudante_turma_id = :estudante_turma_id AND bimestre_id = :bimestre_id AND atividade_id = :atividade_id");
            $stmt->execute([
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':bimestre_id' => $class->bimestre_id,
                ':atividade_id' => $class->atividade_id
            ]);

            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $id['id'] ?? null;
        } catch(\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação select: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    private function removeScore($id) :?bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM notas WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            if($delete) {
                return true;
            }
            return false;
        } catch(\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }
}