<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Models\Classrooms\Turma;
use App\Repositories\Coordination\CoordenadorTurmaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class TurmaRepository {
    const CLASS_NAME = Turma::class;
    const TABLE = 'turmas';

    use FindTrait;
    protected $conn;
    protected $model;
    protected $coordenadorTurmaRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Turma();
        $this->coordenadorTurmaRepository = new CoordenadorTurmaRepository();
    }

    public function allClassRooms(array $params = [])
    {
        $sql = "SELECT 
                t.*,
                JSON_OBJECT(
                    'coordenadores', 
                    COALESCE(
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'nome', pf.nome,
                                'email', pf.email
                            )
                        ),
                        JSON_ARRAY()
                    )
                ) AS details 
            FROM " . self::TABLE . " t 
            LEFT JOIN coordenador_as_turma ct ON ct.turma_id = t.id
            LEFT JOIN coordenadores c ON ct.coordenador_id = c.id AND c.ativo = 1
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

        $sql .= " GROUP BY t.id ORDER BY t.created_at DESC";

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
                    ordem = :ordem
                "
            );

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':nome' => $class->nome,
                ':turno' => $class->turno,
                ':ordem' => $class->ordem
            ]);

            if (!$create) {
                return null;
            }

            $created = $this->findByUuid($class->uuid);

            $this->coordenadorTurmaRepository->saveAll($data, $created->id);

            return $created;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
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
                    ativo = :ativo
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':nome' => $class->nome,
                ':turno' => $class->turno,
                ':ordem' => $class->ordem,
                ':ativo' => $class->ativo,
                ':id' => $id
            ]);

            if (!$update) {
                return null;
            }

            $this->coordenadorTurmaRepository->saveAll($data, $id);

            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }


    public function findByName(string $name): ?array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE nome = :name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
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