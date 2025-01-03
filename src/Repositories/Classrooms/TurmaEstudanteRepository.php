<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Models\Classrooms\TurmaEstudante;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class TurmaEstudanteRepository {
    const CLASS_NAME = TurmaEstudante::class;
    const TABLE = 'turma_estudante';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new TurmaEstudante();
    }

    public function allClassStudents(array $params = [])
    {
        $sql = "SELECT 
           te.*,
           (
            SELECT 
               JSON_OBJECT(
                   'id', t.id,
                   'nome', t.nome,
                   'turno', t.turno
                )
            FROM turmas t
            WHERE t.id = te.turma_id and t.ativo = 1
        ) AS turma,
           (
            SELECT 
               JSON_OBJECT(
                   'id', e.id,
                   'pessoa_fisica_id', e.pessoa_fisica_id
                )
            FROM estudantes e
            WHERE e.id = te.estudante_id and e.ativo = 1
        ) AS estudante
        FROM " . self::TABLE . " te";

        $conditions = [];
        $bindings = [];

        if (isset($params['search'])) {
            $conditions[] = "t.nome LIKE :nome";
            $bindings[':nome'] = '%' . $params['search'] . '%';
        }

        if (isset($params['ativo'])) {
            $conditions[] = "te.ativo = :ativo";
            $bindings[':ativo'] = $params['ativo'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY te.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function create(array $data)
    {
        $class = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    turma_id = :turma_id,
                    estudante_id = :estudante_id,
                    ano_letivo = :ano_letivo
                "
            );

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':turma_id' => $class->turma_id,
                ':estudante_id' => $class->estudante_id,
                ':ano_letivo' => $class->ano_letivo
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
        $turma_estudante = $this->findById($id);
        if (!$turma_estudante) {
            return null;
        }

        $turma_estudante->update($data);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    turma_id = :turma_id,
                    estudante_id = :estudante_id,
                    ano_letivo = :ano_letivo
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':id' => $id,
                ':turma_id' => $turma_estudante->turma_id,
                ':estudante_id' => $turma_estudante->estudante_id,
                ':ano_letivo' => $turma_estudante->ano_letivo
            ]);

            if (!$update) {
                return null;
            }

            return $this->findById($id);
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