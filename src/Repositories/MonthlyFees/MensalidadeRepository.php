<?php

namespace App\Repositories\MonthlyFees;

use App\Config\Database;
use App\Models\MonthlyFees\Mensalidade;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class MensalidadeRepository {
    const CLASS_NAME = Mensalidade::class;
    const TABLE = 'mensalidades';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Mensalidade();
    }

    public function allMonthlyfees(array $params = [])
    {
        $sql = "SELECT
           m.*
        FROM " . self::TABLE . " m";

        $conditions = [];
        $bindings = [];

        if (isset($params['situation'])) {
            $conditions[] = "m.situacao = :situacao";
            $bindings[':situacao'] = $params['situation'];
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $conditions[] = "m.created_at between :start_date and :end_date";
            $bindings[':start_date'] = $params['start_date'];
            $bindings[':end_date'] = $params['end_date'];
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
                    estudante_mensalidade_id = :estudante_mensalidade_id,
                    valor = :valor,
                    data_vencimento = :data_vencimento,
                    dia_vencimento = :dia_vencimento
                "
            );

            $create = $stmt->execute([
                ':uuid' => $monthly->uuid,
                ':estudante_mensalidade_id' => $monthly->estudante_mensalidade_id,
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
                    estudante_mensalidade_id = :estudante_mensalidade_id,
                    valor = :valor,
                    data_vencimento = :data_vencimento,
                    dia_vencimento = :dia_vencimento
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':id' => $id,
                ':estudante_mensalidade_id' => $monthly->estudante_mensalidade_id,
                ':valor' => $monthly->valor,
                ':data_vencimento' => $monthly->data_vencimento,
                ':dia_vencimento' => $monthly->dia_vencimento
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
        try {
            $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . "
                 SET situacao = 'cancelado'
                 WHERE id = :id"
            );

            $updated = $stmt->execute(['id' => $id]);

            return $updated;
        } catch(\Trowable $th) {
            return false;
        }
    }
}
