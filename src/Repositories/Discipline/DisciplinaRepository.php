<?php

namespace App\Repositories\Discipline;

use App\Config\Database;
use App\Interfaces\Discipline\IDisciplinaRepository;
use App\Models\Discipline\Disciplina;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class DisciplinaRepository implements IDisciplinaRepository {
    const CLASS_NAME = Disciplina::class;
    const TABLE = 'disciplinas';

    use FindTrait;
    protected $conn;
    protected $model;

    public function __construct(){
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Disciplina();
    }

    public function allDisciplines(array $params = []){
        $sql = "SELECT 
            d.* 
            FROM " . self::TABLE . " d 
            LEFT JOIN professor_disciplina pd ON pd.disciplina_id = d.id
            ";

        $conditions = [];
        $bindings = [];

        if (isset($params['search'])) {
            $conditions[] = "d.nome LIKE :search ";
            $bindings[':search'] = '%' . $params['search'] . '%';
        }   

        if (isset($params['active']) && $params['active'] != '') {
            $conditions[] = "d.ativo = :ativo";
            $bindings[':ativo'] = $params['active'];
        }
        if (isset($params['teacher_id'])) {
            $conditions[] = "pd.professor_id = :professor_id";
            $bindings[':professor_id'] = $params['teacher_id'];
        }

        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::CLASS_NAME);  
    }

    public function create(array $data){
        $disciplina = $this->model->create(
            $data
        );

        try{
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . "
                    SET
                        uuid = :uuid,
                        nome = :name"
            );

            $create = $stmt->execute([
                ':uuid' => $disciplina->uuid,
                ':name' => $disciplina->nome
            ]);

            if(is_null($create)){
                return null;
            }

            return $this->findByUuid($disciplina->uuid);
        }catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id){
        $disciplina = $this->model->create($data);

        try{
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . "
                    SET
                        nome = :name,
                        ativo = :active
                    WHERE id = :id"
            );

            $updated = $stmt->execute([
                ':id' => $id,
                ':name' => $disciplina->nome,
                ':active' => $disciplina->ativo
            ]);

            if(!$updated){
                return null;
            }

            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function delete(int $id){
        $stmt = $this->conn
        ->prepare(
            "UPDATE " . self::TABLE . "
                SET 
                    ativo = 0
                WHERE id = :id"
        );

        $updated = $stmt->execute(['id' => $id]);

        return $updated;
    }

    public function findAllDisciplineByClassID($turma_id) {
        try {
            $sql = "SELECT t.*, JSON_OBJECT(
                'turma_id', t.id,
                'turma_nome', t.nome,
                'disciplinas', JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'disciplina_id', d.id,
                        'disciplina_nome', d.nome,
                        'professor_disciplina_id', td.professor_disciplina_id,
                        'turma_disciplina_id', td.id
                    )
                )
            ) AS resultado
            FROM turmas t
            LEFT JOIN turma_disciplina td ON td.turma_id = t.id
            LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id
            LEFT JOIN disciplinas d ON d.id = pd.disciplina_id
            WHERE t.id = $turma_id
            GROUP BY t.id;
            ";
            
            $stmt = $this->conn->prepare($sql);
    
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }
}