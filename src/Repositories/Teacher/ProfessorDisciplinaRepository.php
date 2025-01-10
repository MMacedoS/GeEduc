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
        $sql = "SELECT 
           pd.*,
           (
            SELECT 
               JSON_OBJECT(
                   'id', d.id,
                   'nome', d.nome
                )
            FROM disciplinas d
            WHERE d.id = pd.disciplina_id and d.ativo = 1
        ) AS disciplina,
           (
            SELECT 
               JSON_OBJECT(
                   'id', p.id,
                   'pessoa_fisica_id', p.pessoa_fisica_id
                )
            FROM professores p
            WHERE p.id = pd.professor_id and p.ativo = 1
        ) AS professor
        FROM " . self::TABLE . " pd";
        
        $conditions = [];
        $bindings = [];

        ////////////////////ERRO AQUI
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
        /////////////////

        if(count($conditions) > 0){
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY pd.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);
    }

    public function create(array $data){
        $teacherDiscipline = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        disciplina_id = :disciplina_id,
                        professor_id = :professor_id,
                        ano_letivo = :ano_letivo
                "
            );

            $create = $stmt->execute([
                ':uuid' => $teacherDiscipline->uuid,
                ':disciplina_id' => $teacherDiscipline->disciplina_id,
                ':professor_id' => $teacherDiscipline->professor_id,
                ':ano_letivo' => $teacherDiscipline->ano_letivo,
            ]);

            if(!$create){
                return null;
            }

            return $this->findByUuid($teacherDiscipline->uuid);
        }catch(\Throwable $th){
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        }
    }

    public function update(array $data, int $id){
        $professor_disciplina = $this->findById($id);

        if(!$professor_disciplina){
            return null;
        }

        $professor_disciplina = $professor_disciplina->update($data, $professor_disciplina);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                    SET 
                        disciplina_id = :disciplina_id,
                        professor_id = :professor_id,
                        ano_letivo = :ano_letivo,
                        ativo = :active
                    WHERE id = :id
                "
            );

            $update = $stmt->execute([
                ':id' => $professor_disciplina->id,
                ':disciplina_id' => $professor_disciplina->disciplina_id,
                ':professor_id' => $professor_disciplina->professor_id,
                ':ano_letivo' => $professor_disciplina->ano_letivo,
                ':active' => $professor_disciplina->ativo
            ]);

            if(!$update){
                return null;
            }

            return $this->findById($id);
        }catch(\Throwable $th){
            return null;
        }
    }

    public function delete(int $id){
        $stmt = $this->conn->prepare(
            "UPDATE " . self::TABLE . "
                SET
                    ativo = 0
                WHERE id = :id
            "
        );

        $updated = $stmt->execute(['id' =>  $id]);

        return $updated;
    }
}