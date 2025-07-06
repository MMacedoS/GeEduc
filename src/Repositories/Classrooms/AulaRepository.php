<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Classrooms\IAulaRepository;
use App\Models\Classrooms\Aula;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use Exception;

class AulaRepository extends SingletonInstance implements IAulaRepository {
    const CLASS_NAME = Aula::class;
    const TABLE = 'aula';

    use FindTrait;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Aula();
    }

    public function allClass(array $params = [])
    {
        $sql = "SELECT 
                a.id,
                a.uuid,
                a.turma_disciplina_id,                
                a.dia_id,
                JSON_OBJECT(
                    'nome', ds.dia,
                    'horario', ds.horario,
                    'turno', ds.turno
                ) as dia,
                JSON_OBJECT(
                    'disciplinas', 
                    COALESCE(
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'nome', d.nome,
                                'id', d.id
                            )
                        ),
                        JSON_ARRAY()
                    ),
                    'professores', 
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
            FROM aula a
            LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
            LEFT JOIN professor_disciplina dp ON dp.id = td.professor_disciplina_id
            LEFT JOIN disciplinas d ON d.id = dp.disciplina_id
            LEFT JOIN professores p ON p.id = dp.professor_id
            LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
            LEFT JOIN dias_da_semana ds on a.dia_id = ds.id ";

        $conditions = [];
        $bindings = [];

        if (isset($params['shift']) && $params['shift'] != '') {
            $conditions[] = "ds.turno = :shift";
            $bindings[':shift'] = $params['shift'];
        }

        if (isset($params['search'])) {
            $conditions[] = "pf.nome LIKE :search ";
            $bindings[':search'] = '%' . $params['search'] . '%';
        } 

        if (isset($params['day']) && $params['day'] != '') {
            $conditions[] = "ds.dia = :day";
            $bindings[':day'] = $params['day'];
        }

        if (isset($params['classroom_discipline_id']) && $params['classroom_discipline_id'] != '') {
            $conditions[] = "a.turma_disciplina_id = :turma_disciplina";
            $bindings[':turma_disciplina'] = $params['classroom_discipline_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY a.id ORDER BY ds.horario, ds.turno asc";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);   
    }

    public function create(array $data)
    {
        if(
            empty($data) || 
            (
                !isset($data['days_id']) && 
                !isset($data['classroom_discipline_id'])
            )
        ) {
            return null;
        }       

        if (!$this->removeClass($data['classroom_discipline_id'])) {
            return false;
        }

        try {            
            foreach ($data['days_id'] as $day) {
                $stmt = $this->conn->prepare(
                    "INSERT INTO aula (uuid, dia_id, turma_disciplina_id) 
                    VALUES (uuid(), :day_id, :class_disciplina_id)"
                );
                
                $success = $stmt->execute([
                    ':day_id' => (int)$day,
                    ':class_disciplina_id' => (int)$data['classroom_discipline_id']
                ]);

                if (!$success) {
                    continue;
                }
            }
            return true;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function update(array $data, int $id)
    {
        throw new Exception("Error Processing Request", 1);
        
    }

    public function delete(string $id)
    {
        $class = $this->findByUuid($id);

        if(is_null($class)) {
            return null;
        }

        $smtp = $this->conn->prepare("DELETE FROM aula WHERE id = :id");
        return $smtp->execute(
            [
                ':id' => (int)$class->id
            ]
        );
    }

    public function classByTeacherDisciplineId(int $teacherDisciplineId)
    {
        
    }

    public function classByTeacherId(int $teacherId)
    {
        
    }

    private function removeClass(int $turma_disciplina_id): bool 
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM aula WHERE turma_disciplina_id = :turma_disciplina_id"
        );
        $deleted = $stmt->execute([':turma_disciplina_id' => (int)$turma_disciplina_id]);

        return $deleted;
    }
}