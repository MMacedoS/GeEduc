<?php

namespace App\Repositories\Teacher;

use App\Config\Database;
use App\Models\Teacher\ProfessorDisciplina;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ProfessorDisciplinaRepository {

    const CLASS_NAME = ProfessorDisciplina::class;
    const TABLE = 'disciplina_professor';

    use FindTrait;

    protected $conn;
    protected $model;

    public function __construct(){
        $conn = new Database();
        $this->conn = $conn->getConnection();
        $this->model = new ProfessorDisciplina();
    }

    public function allTeacherDisciplines(array $params = []){
        $sql = "SELECT pd.*,
                (
                    SELECT JSON_OBJECT(
                        'id', d.id,
                        'nome', d.nome
                    )
                    FROM disciplinas d
                    WHERE d.id = pd.disciplina_id and d.ativo = 1
                ) AS disciplina,
                (
                    SELECT JSON_OBJECT(
                        'id', p.id,
                        'pessoa_fisica_id', p.pessoa_fisica_id
                    )
                    FROM professores p
                    WHERE p.id = pd.professor_id and p.ativo = 1
                ) AS professor
                FROM " . self::TABLE . " pd";
        
        $conditions = [];
        $bindings = [];

        if(isset($params['search'])){
            $conditions[] = "d.nome LIKE :nome";
            $bindings[':nome'] = '%' . $params['search'] . '%';
        }

        if(isset($params['teacher_id'])){
            $conditions[] = 'pd.professor_id = :professor_id';
            $bindings[':professor_id'] = $params['teacher_id'];
        }

        if(isset($params['discipline_id'])){
            $conditions[] = 'pd.disciplina_id = :disciplina_id';
            $bindings[':disciplina_id'] = $params['discipline_id'];
        }

        if(isset($params['active'])){
            $conditions[] = 'pd.ativo = :ativo';
            $bindings[':ativo'] = $params['active'];
        }

        if(count($conditions) > 0){
            $slq .= "WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY pd.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }
}