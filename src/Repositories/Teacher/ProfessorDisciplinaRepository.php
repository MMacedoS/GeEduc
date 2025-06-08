<?php

namespace App\Repositories\Teacher;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Models\Teacher\ProfessorDisciplina;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ProfessorDisciplinaRepository extends SingletonInstance implements IProfessorDisciplinaRepository {

    const CLASS_NAME = ProfessorDisciplina::class;
    const TABLE = 'professor_disciplina';

    use FindTrait;



    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new ProfessorDisciplina();
    }

    public function allTeacherDisciplines(array $params = []){
        $sql = "SELECT 
                pd.*,
                JSON_OBJECT(
                    'nome', pf.nome,
                    'email', pf.email
                ) AS professor,
                JSON_OBJECT(
                    'id', d.id,
                    'uuid', d.uuid,
                    'nome', d.nome
                ) AS disciplina
            FROM " . self::TABLE . " pd 
            LEFT JOIN professores p ON p.id = pd.professor_id AND p.ativo = 1
            LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id 
            LEFT JOIN disciplinas d ON d.id = pd.disciplina_id
        ";
        
        $conditions = [];
        $bindings = [];

        ////////////////////ERRO AQUI
        if (isset($params['search'])) {
            $conditions[] = "(d.nome LIKE :search OR pf.nome LIKE :search)";
            $bindings[':search'] = '%' . $params['search'] . '%';
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

        $sql .= " ORDER BY d.nome DESC";

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
        } finally {          
            Database::getInstance()->closeConnection();
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
        } finally {          
            Database::getInstance()->closeConnection();
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