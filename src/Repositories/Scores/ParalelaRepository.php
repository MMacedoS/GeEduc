<?php

namespace App\Repositories\Scores;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Scores\IParalelaRepository;
use App\Models\Scores\Paralela;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class ParalelaRepository extends SingletonInstance implements IParalelaRepository
{
    const CLASS_NAME = Paralela::class;
    const TABLE = 'paralela';

    use FindTrait;


    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Paralela();
    }

    public function allScoresParallel(array $params = [])
    {
        $sql = "SELECT
            p.*, 
            JSON_OBJECT(
                'id', td.id,
                'uuid', td.uuid
            ) AS turmas_details
            FROM paralela p
            LEFT JOIN turma_disciplina td ON td.id = p.turma_disciplina_id
            LEFT JOIN estudante_turma et ON et.id = p.estudante_turma_id";


        $conditions = [];
        $bindings = [];

        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'p.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['period_id'])) {
            $conditions[] = 'p.periodo_id = :period_id';
            $bindings[':period_id'] = $params['period_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY p.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->fetchAll(PDO::FETCH_CLASS);
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
                    turma_disciplina_id = :turma_disciplina_id,
                    periodo_id = :periodo_id,
                    estudante_turma_id = :estudante_turma_id,
                    nota = :nota"
            );

            $idScore = $this->checkIfExistsScoreParallel($class);
            if ($idScore) {
                $this->removeScoreParallel($idScore);
            }

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':turma_disciplina_id' => $class->turma_disciplina_id,
                ':periodo_id' => $class->periodo_id,
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

    private function checkIfExistsScoreParallel($class): ?String
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id 
                FROM paralela 
                WHERE 
                    estudante_turma_id = :estudante_turma_id AND 
                    periodo_id = :periodo_id AND 
                    turma_disciplina_id = :turma_disciplina_id"
            );

            $stmt->execute([
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':periodo_id' => $class->periodo_id,
                ':turma_disciplina_id' => $class->turma_disciplina_id
            ]);

            $id = $stmt->fetch(PDO::FETCH_ASSOC);

            return $id['id'] ?? null;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação select: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    private function removeScoreParallel($id): ?bool
    {
        $scores_parallel = $this->findById((int)$id);
        if (is_null($scores_parallel)) {
            return null;
        }

        try {
            $stmt = $this->conn->prepare("DELETE FROM paralela WHERE id = :id");
            $delete = $stmt->execute([
                ':id' => $id
            ]);
            if ($delete) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }
}
