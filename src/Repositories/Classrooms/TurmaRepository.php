<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Models\Classrooms\Turma;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class TurmaRepository {
    const CLASS_NAME = Turma::class;
    const TABLE = 'turmas';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new Turma();
    }

    public function allClassRooms(array $params = [])
    {
        $sql = "SELECT 
            t.*,
            JSON_OBJECT(
                    'nome', pf.nome,
                    'email', pf.email
                ) AS coordenador 
            FROM " . self::TABLE . " t 
            LEFT JOIN coordenadores c ON c.id = t.coordenador_id AND c.ativo = 1
            LEFT JOIN pessoa_fisica pf ON pf.id = c.pessoa_fisica_id  
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['search'])) {
            $conditions[] = "(t.nome LIKE :search OR pf.nome LIKE :search)";
            $bindings[':search'] = '%' . $params['search'] . '%';
        }   

        if (isset($params['shift'])) {
            $conditions[] = "t.turno = :turno";
            $bindings[':turno'] = $params['shift'];
        }

        if (isset($params['active'])) {
            $conditions[] = "t.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY t.created_at DESC";

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
                    nome = :nome,
                    turno = :turno,
                    ordem = :ordem,
                    coordenador_id = :coordenador_id
                "
            );

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':nome' => $class->nome,
                ':turno' => $class->turno,
                ':ordem' => $class->ordem,
                ':coordenador_id' => $class->coordenador_id
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
        $class = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    nome = :nome,
                    turno = :turno,
                    ordem = :ordem,
                    ativo = :ativo,
                    coordenador_id = :coordenador_id
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':nome' => $class->nome,
                ':turno' => $class->turno,
                ':ordem' => $class->ordem,
                ':ativo' => $class->ativo,
                ':coordenador_id' => $class->coordenador_id,
                ':id' => $id
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