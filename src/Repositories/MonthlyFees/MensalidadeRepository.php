<?php

namespace App\Repositories\MonthlyFees;

use App\Config\Database;
use App\Models\MonthlyFees\Mensalidade;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class MensalidadeRepository {
    const CLASS_NAME = Mensalidade::class;
    const TABLE = 'estudante_turma';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Mensalidade();
    }

    public function allClassStudents(array $params = [])
    {
        $sql = "SELECT 
           m.*,
           (
            SELECT 
               JSON_OBJECT(
                   'id', p.id,
                   'nome', p.nome,
                   'valor', p.valor
                )
            FROM planos p
            WHERE p.id = m.plano_id
        ) AS planos,
           (
            SELECT 
               JSON_OBJECT(
                   'id', e.id,
                   'pessoa_fisica_id', e.pessoa_fisica_id
                )
            FROM estudantes e
            WHERE e.id = m.estudante_id
        ) AS estudante
        FROM " . self::TABLE . " m";

        $conditions = [];
        $bindings = [];

        if (isset($params['search'])) {
            $conditions[] = "t.nome LIKE :nome";
            $bindings[':nome'] = '%' . $params['search'] . '%';
        }

        if (isset($params['student_id'])) {
            $conditions[] = "et.estudante_id = :estudante_id";
            $bindings[':estudante_id'] = $params['student_id'];
        }

        if (isset($params['plan_id'])) {
            $conditions[] = "m.plano_id = :plano_id";
            $bindings[':plano_id'] = $params['plan_id'];
        }

        if (isset($params['situation'])) {
            $conditions[] = "m.situacao = :situacao";
            $bindings[':situacao'] = $params['situation'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY m.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);        
    }

    public function create(array $data)
    {
        $monthly = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    plano_id = :plano_id,
                    estudante_id = :estudante_id,
                    valor = :valor,
                    data_vencimento = :data_vencimento,
                    dia_vencimento = :dia_vencimento
                "
            );

            $create = $stmt->execute([
                ':uuid' => $monthly->uuid,
                ':plano_id' => $monthly->plano_id,
                ':estudante_id' => $monthly->estudante_id,
                ':valor' => $monthly->valor,
                ':data_vencimento' => $monthly->data_vencimento,
                ':dia_vencimento' => $monthly->dia_vencimento
            ]);

            if (!$create) {
                return null;
            }

            return $this->findByUuid($monthly->uuid);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        $monthly = $this->findById($id);

        if (!$monthly) {
            return null;
        }

        $monthly = $monthly->update($data, $monthly);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    plano_id = :plano_id,
                    estudante_id = :estudante_id,
                    valor = :valor,
                    data_vencimento = :data_vencimento,
                    dia_vencimento = :dia_vencimento,
                    situacao = :situacao 
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':id' => $id,
                ':plano_id' => $monthly->plano_id,
                ':estudante_id' => $monthly->estudante_id,
                ':valor' => $monthly->valor,
                ':data_vencimento' => $monthly->data_vencimento,
                ':dia_vencimento' => $monthly->dia_vencimento,
                ':situacao' => $monthly->situacao
            ]);

            if (!$update) {
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
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