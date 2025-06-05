<?php

namespace App\Repositories\Classrooms;

use App\Config\Database;
use App\Interfaces\Classrooms\IAulaRepository;
use App\Models\Classrooms\Aula;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class AulaRepository implements IAulaRepository {
    const CLASS_NAME = Aula::class;
    const TABLE = 'aula';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Aula();
    }

    public function allClass(array $params = [])
    {
        $sql = "SELECT 
                a.id AS aula_id,
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
            LEFT JOIN professor_disciplina dp ON dp.id = a.professor_disciplina_id
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

        if (isset($params['teacher'])) {
            $conditions[] = "pf.nome LIKE :teacher ";
            $bindings[':teacher'] = '%' . $params['teacher'] . '%';
        } 

        if (isset($params['day']) && $params['day'] != '') {
            $conditions[] = "ds.dia = :day";
            $bindings[':day'] = $params['day'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY a.id ORDER BY a.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);   
    }

    public function create(array $data)
    {
        
    }

    public function update(array $data, int $id)
    {
        
    }

    public function delete(int $id)
    {
        
    }

    public function classByTeacherDisciplineId(int $teacherDisciplineId)
    {
        
    }

    public function classByTeacherId(int $teacherId)
    {
        
    }
}