<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Models\Classrooms\TurmaDisciplina;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class TurmaDisciplinaRepository extends SingletonInstance implements ITurmaDisciplinaRepository
{
    const CLASS_NAME = TurmaDisciplina::class;
    const TABLE = 'turma_disciplina';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new TurmaDisciplina();
    }

    public function allClassDisciplines(array $params = [])
    {
        $sql = "SELECT 
                td.*
            FROM turma_disciplina td
            LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id AND pd.ativo = 1
            LEFT JOIN professores p ON p.id = pd.professor_id AND p.ativo = 1
            LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
            LEFT JOIN disciplinas d ON d.id = pd.disciplina_id
            LEFT JOIN turmas t ON t.id = td.turma_id
            LEFT JOIN carga_horaria ch ON ch.id = td.carga_horaria_id
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['class_id'])) {
            $conditions[] = 'td.turma_id = :turma_id';
            $bindings[':turma_id'] = $params['class_id'];
        }

        if (isset($params['teacher_discipline_id'])) {
            $conditions[] = 'pd.id = :professor_disciplina_id';
            $bindings[':professor_disciplina_id'] = $params['teacher_discipline_id'];
        }

        if (isset($params['academic_year'])) {
            $conditions[] = 'td.ano_letivo = :ano_letivo';
            $bindings[':ano_letivo'] = $params['academic_year'];
        }

        if (isset($params['uuid'])) {
            $conditions[] = 'td.uuid = :uuid';
            $bindings[':uuid'] = $params['uuid'];
        }

        if (isset($params['id'])) {
            $conditions[] = 'td.id = :id';
            $bindings[':id'] = $params['id'];
        }

        if (isset($params['active'])) {
            $conditions[] = 'td.ativo = :ativo';
            $bindings[':ativo'] = $params['active'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY td.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function classDisciplineByParams(array $params = [])
    {
        $sql = "SELECT 
                td.*,
                JSON_OBJECT(
                    'id', pd.id,
                    'disciplina_id', pd.disciplina_id,
                    'professor_id', pd.professor_id,
                    'professor', JSON_OBJECT(
                        'id', pf.id,
                        'nome', pf.nome,
                        'email', pf.email
                    ),
                    'disciplina', JSON_OBJECT(
                        'id', d.id,
                        'nome', d.nome
                    )
                ) AS professor_disciplina,
                JSON_OBJECT(
                    'id', t.id,
                    'nome', t.nome,
                    'uuid', t.uuid
                ) AS turma,
                JSON_OBJECT(
                    'id', d.id,
                    'nome', d.nome,
                    'uuid', d.uuid
                ) AS disciplinas,
                JSON_OBJECT(
                    'id', ch.id,
                    'carga_horaria', ch.carga
                ) AS carga_horaria
            FROM turma_disciplina td
            LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id AND pd.ativo = 1
            LEFT JOIN professores p ON p.id = pd.professor_id AND p.ativo = 1
            LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
            LEFT JOIN disciplinas d ON d.id = pd.disciplina_id
            LEFT JOIN turmas t ON t.id = td.turma_id
            LEFT JOIN carga_horaria ch ON ch.id = td.carga_horaria_id
        ";

        $conditions = [];
        $bindings = [];

        if (isset($params['id'])) {
            $conditions[] = 'td.id = :id';
            $bindings[':id'] = $params['id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY td.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function create(array $data)
    {
        $class = $this->model->create($data);

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    professor_disciplina_id = :professor_disciplina_id,
                    carga_horaria_id = :carga_horaria_id,
                    turma_id = :turma_id,
                    ano_letivo = :ano_letivo                    
                "
            );

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':professor_disciplina_id' => $class->professor_disciplina_id,
                ':carga_horaria_id' => $class->carga_horaria_id,
                ':turma_id' => $class->turma_id,
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
        $class_disciplina = $this->findById($id);

        $class = $this->model->update($data, $class_disciplina);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                   professor_disciplina_id = :professor_disciplina_id,
                   carga_horaria_id = :carga_horaria_id,
                   turma_id = :turma_id,
                   ano_letivo = :ano_letivo,
                   ativo = :ativo  
                WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':professor_disciplina_id' => $class->professor_disciplina_id,
                ':carga_horaria_id' => $class->carga_horaria_id,
                ':turma_id' => $class->turma_id,
                ':ano_letivo' => $class->ano_letivo,
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

    public function classDisciplinesByTeacherDisciplineId(int $teacherDisciplineId)
    {

        $sql = "SELECT 
                td.*,
                JSON_OBJECT(
                    'id', pd.id,
                    'disciplina_id', pd.disciplina_id,
                    'professor_id', pd.professor_id,
                    'professor', JSON_OBJECT(
                        'id', pf.id,
                        'nome', pf.nome,
                        'email', pf.email
                    ),
                    'disciplina', JSON_OBJECT(
                        'id', d.id,
                        'nome', d.nome
                    )
                ) AS professor_disciplina,
                JSON_OBJECT(
                    'id', t.id,
                    'uuid', t.uuid
                ) AS turma,
                JSON_OBJECT(
                    'id', ch.id,
                    'carga_horaria', ch.carga
                ) AS carga_horaria
            FROM turma_disciplina td
            LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id AND pd.ativo = 1
            LEFT JOIN professores p ON p.id = pd.professor_id AND p.ativo = 1
            LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
            LEFT JOIN disciplinas d ON d.id = pd.disciplina_id
            LEFT JOIN turmas t ON t.id = td.turma_id
            LEFT JOIN carga_horaria ch ON ch.id = td.carga_horaria_id
            WHERE td.professor_disciplina_id = :id
        ";

        $sql .= " ORDER BY created_at DESC LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([':id' => $teacherDisciplineId]);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        return $stmt->fetch();
    }

    public function classDisciplinesByTeacherId(int $teacherId, ?string $academicYear = null)
    {

        $sql = "SELECT 
                td.*,
                    JSON_OBJECT(
                        'id', pd.id,
                        'disciplina_id', pd.disciplina_id,
                        'professor_id', pd.professor_id,
                        'professor', JSON_OBJECT(
                            'id', pf.id,
                            'nome', pf.nome,
                            'email', pf.email
                        ),
                        'disciplina', JSON_OBJECT(
                            'id', d.id,
                            'nome', d.nome
                        )
                    ) AS professor_disciplina,
                    JSON_OBJECT(
                        'id', t.id,
                        'nome', t.nome,
                        'uuid', t.uuid
                    ) AS turma
            FROM professores p
            INNER JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
            INNER JOIN professor_disciplina pd ON pd.professor_id = p.id
            INNER JOIN disciplinas d ON d.id = pd.disciplina_id
            INNER JOIN turma_disciplina td ON td.professor_disciplina_id = pd.id
            INNER JOIN turmas t ON t.id = td.turma_id
            WHERE p.ativo = 1  
            AND pd.ativo = 1 
            AND td.ativo = 1
            AND p.id = :id
        ";

        $bindings = [':id' => $teacherId];

        if ($academicYear) {
            $sql .= " AND td.ano_letivo = :academic_year";
            $bindings[':academic_year'] = $academicYear;
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }
}
