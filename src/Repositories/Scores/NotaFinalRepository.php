<?php

namespace App\Repositories\Scores;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Scores\INotaFinalRepository;
use App\Models\Scores\NotaFinal;
use App\Repositories\Traits\FindTrait;
use PDO;

class NotaFinalRepository extends SingletonInstance implements INotaFinalRepository
{
    const CLASS_NAME = NotaFinal::class;
    const TABLE = 'nota_final';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new NotaFinal();
    }

    public function create(array $params)
    {
        $sql = "INSERT INTO nota_final 
                (uuid, turma_disciplina_id, estudante_turma_id, nota, situacao, ano_letivo, created_at, updated_at) 
                VALUES 
                (:uuid, :turma_disciplina_id, :estudante_turma_id, :nota, :situacao, :ano_letivo, NOW(), NOW())";

        try {
            $model = new NotaFinal();
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uuid' => $params['uuid'] ?? $model->generateUUID(),
                ':turma_disciplina_id' => $params['turma_disciplina_id'],
                ':estudante_turma_id' => $params['estudante_turma_id'],
                ':nota' => $params['nota'],
                ':situacao' => $params['situacao'],
                ':ano_letivo' => $params['ano_letivo'] ?? date('Y')
            ]);

            return $this->conn->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao criar nota final: " . $e->getMessage());
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $params, int $id)
    {
        $fields = [];
        $bindings = [':id' => $id];

        if (isset($params['nota'])) {
            $fields[] = "nota = :nota";
            $bindings[':nota'] = $params['nota'];
        }

        if (isset($params['situacao'])) {
            $fields[] = "situacao = :situacao";
            $bindings[':situacao'] = $params['situacao'];
        }

        if (isset($params['obs'])) {
            $fields[] = "obs = :obs";
            $bindings[':obs'] = $params['obs'];
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE nota_final SET " . implode(", ", $fields) . " WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao atualizar nota final: " . $e->getMessage());
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    public function findByStudentAndDiscipline(int $estudante_turma_id, int $turma_disciplina_id)
    {
        $sql = "SELECT * FROM nota_final 
                WHERE estudante_turma_id = :estudante_turma_id 
                AND turma_disciplina_id = :turma_disciplina_id 
                LIMIT 1";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':estudante_turma_id' => $estudante_turma_id,
                ':turma_disciplina_id' => $turma_disciplina_id
            ]);

            $result = $stmt->fetchObject(self::CLASS_NAME);
            return $result ?: null;
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar nota final: " . $e->getMessage());
        } finally {
            Database::getInstance()->closeConnection();
        }
    }
}
