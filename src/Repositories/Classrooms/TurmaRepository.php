<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Models\Classrooms\Turma;
use App\Repositories\Coordination\CoordenadorTurmaRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class TurmaRepository extends SingletonInstance implements ITurmaRepository {
    const CLASS_NAME = Turma::class;
    const TABLE = 'turmas';

    use FindTrait;

    protected $coordenadorTurmaRepository;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Turma();
        $this->coordenadorTurmaRepository = CoordenadorTurmaRepository::getInstance();
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
                ) AS detalhes 
            FROM " . self::TABLE . " t 
            LEFT JOIN coordenador_as_turma ct ON ct.turma_id = t.id
            LEFT JOIN coordenadores c ON ct.coordenador_id = c.id AND c.ativo = 1
            LEFT JOIN pessoa_fisica pf ON pf.id = c.pessoa_fisica_id
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['classroom'])) {
            $conditions[] = "(t.nome LIKE :classroom)";
            $bindings[':classroom'] = '%' . $params['classroom'] . '%';
        }   

        if (isset($params['shift']) && $params['shift'] != '') {
            $conditions[] = "t.turno = :shift";
            $bindings[':shift'] = $params['shift'];
        }

        if (isset($params['coordinator'])) {
            $conditions[] = "pf.nome LIKE :coordinator ";
            $bindings[':coordinator'] = '%' . $params['coordinator'] . '%';
        } 

        if (isset($params['situation']) && $params['situation'] != '') {
            $conditions[] = "t.ativo = :situation";
            $bindings[':situation'] = $params['situation'];
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

    public function allClassroomsByTeacherID(?int $id) {
        if (is_null($id)) {
            return null;
        }
        
        try {
            $sql = "SELECT t.* 
                    FROM " . self::TABLE ." t
                    INNER JOIN turma_disciplina td ON td.turma_id = t.id
                    INNER JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id
                    INNER JOIN professores p ON p.id = pd.professor_id
                    WHERE p.id = :id";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);  
        } catch (\Throwable $th) {
            LoggerHelper::logError($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }    
    public function findByName(string $name): ?Turma
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE nome = :name";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch(); 
        if (!$register) {
            return null;
        }
    
        return $register;
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