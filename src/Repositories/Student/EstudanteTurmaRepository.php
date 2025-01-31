<?php

namespace App\Repositories\Student;

use App\Config\Database;
use App\Models\Student\EstudanteTurma;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class EstudanteTurmaRepository {
    const CLASS_NAME = EstudanteTurma::class;
    const TABLE = 'estudante_turma';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new EstudanteTurma();
    }

    public function allClassStudents(array $params = [])
    {
        $sql = "SELECT 
                    et.*,
                    JSON_OBJECT(
                        'id', t.uuid,
                        'nome', t.nome,
                        'coordenadores', JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'nome', pf.nome,
                                'email', pf.email
                            )
                        )
                    ) AS turma,
                    JSON_OBJECT(
                        'id', e.id,
                        'pessoa_fisica_id', e.pessoa_fisica_id,
                        'nome', pfe.nome
                    ) AS estudante
                FROM estudante_turma et
                LEFT JOIN turmas t ON t.id = et.turma_id AND t.ativo = 1
                LEFT JOIN coordenador_as_turma ct ON ct.turma_id = t.id
                LEFT JOIN coordenadores c ON ct.coordenador_id = c.id
                LEFT JOIN pessoa_fisica pf ON pf.id = c.pessoa_fisica_id
                LEFT JOIN estudantes e ON e.id = et.estudante_id AND e.ativo = 1
                LEFT JOIN pessoa_fisica pfe ON pfe.id = e.pessoa_fisica_id
        ";
    
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
    
        if (isset($params['class_id'])) {
            $conditions[] = "et.turma_id = :turma_id";
            $bindings[':turma_id'] = $params['class_id'];
        }

        if (isset($params['school_year'])) {
            $conditions[] = "et.ano_letivo = :ano_letivo";
            $bindings[':ano_letivo'] = $params['school_year'];
        }
    
        if (isset($params['active'])) {
            $conditions[] = "et.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }
    
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $sql .= " GROUP BY et.id, e.id, pfe.id ORDER BY et.id DESC";
    
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
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id)
    {
        $turma_estudante = $this->findById($id);

        if (!$turma_estudante) {
            return null;
        }

        $turma_estudante = $turma_estudante->update($data, $turma_estudante);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    turma_id = :turma_id,
                    estudante_id = :estudante_id,
                    ano_letivo = :ano_letivo,
                    ativo = :active 
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':id' => $id,
                ':turma_id' => $turma_estudante->turma_id,
                ':estudante_id' => $turma_estudante->estudante_id,
                ':ano_letivo' => $turma_estudante->ano_letivo,
                ':active' => $turma_estudante->ativo
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
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . " 
             SET ativo = 0 
             WHERE id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }

    public function remove($id) :?bool 
    {
        $estudante = $this->findById((int)$id);

        if (is_null($estudante)) {
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

    public function studentClassByStudentId(int $student_id){

        $sql = "SELECT
            et.*
            FROM " . self::TABLE . " et
            WHERE et.estudante_id = :id
        ";

        $sql .= " ORDER BY et.created_at DESC LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([':id' => $student_id]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetch();  
    }
}