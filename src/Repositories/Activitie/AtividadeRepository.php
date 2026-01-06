<?php

namespace App\Repositories\Activitie;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Activitie\IAtividadeRepository;
use App\Models\Activitie\Atividade;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class AtividadeRepository extends SingletonInstance implements IAtividadeRepository
{
    const CLASS_NAME = Atividade::class;
    const TABLE = 'atividade';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
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
            FROM atividade a 
            LEFT JOIN turma_disciplina td on td.id = a.turma_disciplina_id";

        $conditions = [];
        $bindings = [];

        // Verifica condição de igualdade
        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'a.turma_disciplina_id = :turma_disciplina_id';
            $bindings[':turma_disciplina_id'] = $params['class_discipline_id'];
        }

        if (isset($params['class_room_id'])) {
            $conditions[] = 'td.turma_id = :turma_id';
            $bindings[':turma_id'] = $params['class_room_id'];
        }

        if (isset($params['active'])) {
            $conditions[] = 'a.ativo = :ativo';
            $bindings[':ativo'] = $params['active'];
        }


        if (isset($params['class_discipline_ids'])) {
            $conditions[] = "a.turma_disciplina_id IN ($params[class_discipline_ids])";
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY a.tipo ASC";

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

    public function duplicateForNewYear(int $turmaId, int $newYear): bool
    {
        try {
            $sql = "
                INSERT INTO " . self::TABLE . " (uuid, turma_disciplina_id, tipo, valor, ativo)
                SELECT 
                    UUID(),
                    td_new.id,
                    a.tipo,
                    a.valor,
                    1
                FROM atividade a
                INNER JOIN turma_disciplina td_old ON td_old.id = a.turma_disciplina_id
                INNER JOIN professor_disciplina pd_old ON pd_old.id = td_old.professor_disciplina_id
                INNER JOIN professor_disciplina pd_new ON pd_new.professor_id = pd_old.professor_id
                    AND pd_new.disciplina_id = pd_old.disciplina_id
                    AND pd_new.ano_letivo = :new_year
                INNER JOIN turma_disciplina td_new ON td_new.turma_id = td_old.turma_id
                    AND td_new.professor_disciplina_id = pd_new.id
                    AND td_new.ano_letivo = :new_year
                WHERE td_old.turma_id = :turma_id
                AND a.ativo = 1
                AND td_old.ativo = 1
                AND td_old.ano_letivo < :new_year
                AND NOT EXISTS (
                    SELECT 1 FROM atividade a2
                    INNER JOIN turma_disciplina td2 ON td2.id = a2.turma_disciplina_id
                    WHERE td2.id = td_new.id
                    AND a2.tipo = a.tipo
                    AND a2.valor = a.valor
                )
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':turma_id' => $turmaId,
                ':new_year' => $newYear
            ]);

            return true;
        } catch (\Throwable $th) {
            LoggerHelper::logError("Erro ao duplicar atividades: {$th->getMessage()}");
            LoggerHelper::logError("Trace: " . $th->getTraceAsString());
            return false;
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
