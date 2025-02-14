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
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Mensalidade();
    }

    public function allMonthlyfees(array $params = [])
    {
        $sql = "SELECT
                    m.estudante_mensalidade_id,
                    m.id,
                    JSON_OBJECT(
                        'id', em.id,
                        'uuid', em.uuid,
                        'estudante', JSON_OBJECT(
                            'id', e.id,
                            'uuid', e.uuid,
                            'nome', pf.nome
                        ),
                        'responsavel', JSON_OBJECT(
                            'id', pf_responsavel.id,
                            'uuid', pf_responsavel.uuid,
                            'nome', pf_responsavel.nome,
                            'cpf', pf_responsavel.doc
                        )
                    ) AS estudante_mensalidade,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', m.id,
                            'uuid', m.uuid,
                            'situacao', m.situacao,
                            'valor', m.valor,
                            'data_vencimento', m.data_vencimento,
                            'created_at', m.created_at
                        )
                    ) AS mensalidades,
                    MAX(m.data_vencimento) AS created_at
                FROM " . self::TABLE . " m
                LEFT JOIN estudante_mensalidade em 
                    ON em.id = m.estudante_mensalidade_id 
                LEFT JOIN estudantes e
                    ON e.id = em.estudante_id 
                LEFT JOIN pessoa_fisica pf 
                    ON e.pessoa_fisica_id = pf.id
                LEFT JOIN pessoa_contato pc 
                    ON e.pessoa_contato_id = pc.id
                LEFT JOIN pessoa_fisica pf_responsavel 
                    ON pc.pessoa_fisica_id = pf_responsavel.id
                GROUP BY m.estudante_mensalidade_id, m.id, em.id, e.id, pf.id, pf_responsavel.id;";

        $conditions = [];
        $bindings = [];

        if (isset($params['situation'])) {
        $conditions[] = "m.situacao = :situacao";
        $bindings[':situacao'] = $params['situation'];
        }

        if (isset($params['uuid'])) {
            $conditions[] = "m.uuid = :uuid";
            $bindings[':uuid'] = $params['uuid'];
        }

        if (isset($params['student_monthlyfees_id'])) {
        $conditions[] = "m.estudante_mensalidade_id = :estudante_mensalidade_id";
        $bindings[':estudante_mensalidade_id'] = $params['student_monthlyfees_id'];
        }

        if (isset($params['student_id'])) {
        $conditions[] = "em.estudante_id = :estudante_id";
        $bindings[':estudante_id'] = $params['student_id'];
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
        $conditions[] = "m.created_at BETWEEN :start_date AND :end_date";
        $bindings[':start_date'] = $params['start_date'];
        $bindings[':end_date'] = $params['end_date'];
        }

        if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY m.estudante_mensalidade_id, m.id";
        $sql .= " ORDER BY created_at DESC";

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
            return null;
        }  finally {          
            Database::getInstance()->closeConnection();
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
        } finally {          
            Database::getInstance()->closeConnection();
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
        } catch(\Throwable $th) {
            return false;
        }
    }

    public function remove($id) :?bool 
    {
        $mensalidade = $this->findById((int)$id);

        if (is_null($mensalidade)) {
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id");
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
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function allMonthlyfeesGraph(array $params = [])
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

    public function monthleesByStudentForBoleto()
    {
        $sql = "SELECT
                pf_responsavel.id AS responsavel_id,
                pf_responsavel.uuid AS responsavel_uuid,
                pf_responsavel.nome AS responsavel_nome,
                pf_responsavel.doc AS responsavel_cpf,
                m.id AS id,
                m.uuid AS mensalidade_uuid,
                m.situacao AS situacao,
                m.valor AS valor,
                m.data_vencimento AS data_vencimento,
                m.created_at AS created_at
            FROM mensalidades m
            LEFT JOIN estudante_mensalidade em 
                ON em.id = m.estudante_mensalidade_id 
            LEFT JOIN estudantes e
                ON e.id = em.estudante_id 
            LEFT JOIN pessoa_fisica pf 
                ON e.pessoa_fisica_id = pf.id
            LEFT JOIN pessoa_contato pc 
                ON e.pessoa_contato_id = pc.id
            LEFT JOIN pessoa_fisica pf_responsavel 
                ON pc.pessoa_fisica_id = pf_responsavel.id
            WHERE m.gerou_boleto = 0
            ORDER BY m.data_vencimento LIMIT 5";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();
    
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateGerouBoleto(int $mensalidadeId): void
    {
        $sql = "UPDATE mensalidades SET gerou_boleto = 1 WHERE id = :mensalidade_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':mensalidade_id' => $mensalidadeId]);
    }
}
