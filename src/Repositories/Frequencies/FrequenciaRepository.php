<?php

namespace App\Repositories\Frequencies;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Frequencies\IFrequenciaRepository;
use App\Models\Frequencies\Frequencia;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class FrequenciaRepository extends SingletonInstance implements IFrequenciaRepository {
    const CLASS_NAME = Frequencia::class;
    const TABLE = 'frequencias';

    use FindTrait;


    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Frequencia();
    }

    public function allFrequencies(array $params = [])
    {
        $sql = "SELECT 
            f.*,
            JSON_OBJECT(
                'id', td.id,
                'uuid', td.uuid,
                'professor_disciplina', JSON_OBJECT(
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
                ),
                'turma', JSON_OBJECT(
                    'id', t.id,
                    'uuid', t.uuid
                ),
                'carga_horaria', JSON_OBJECT(
                    'id', ch.id,
                    'carga_horaria', ch.carga
                ),
                'periodo', JSON_OBJECT(
                    'id', b.id,
                    'periodo', b.periodo
                ),
                'total_faltas_aluno', (
                    SELECT SUM(f2.faltas) 
                    FROM frequencias f2 
                    WHERE f2.estudante_turma_id = f.estudante_turma_id 
                    AND f2.turma_disciplina_id = td.id
                )
            ) AS turma_disciplina_details
        FROM frequencias f
        LEFT JOIN turma_disciplina td ON td.id = f.turma_disciplina_id
        LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id AND pd.ativo = 1
        LEFT JOIN professores p ON p.id = pd.professor_id AND p.ativo = 1
        LEFT JOIN pessoa_fisica pf ON pf.id = p.pessoa_fisica_id
        LEFT JOIN disciplinas d ON d.id = pd.disciplina_id
        LEFT JOIN turmas t ON t.id = td.turma_id
        LEFT JOIN carga_horaria ch ON ch.id = td.carga_horaria_id         
        LEFT JOIN periodo b ON b.id = f.periodo_id ";

        
        $conditions = [];
        $bindings = [];
        
        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'f.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['bimester_id'])) {
            $conditions[] = 'f.periodo_id = :bimester_id';
            $bindings[':bimester_id'] = $params['bimester_id'];
        }
        
        if (isset($params['student_id'])) {
            $conditions[] = 'f.estudante_turma_id = :student_id';
            $bindings[':student_id'] = $params['student_id'];
        }

        if (isset($params['class_id'])) {
            $conditions[] = 't.id = :class_id';
            $bindings[':class_id'] = $params['class_id'];
        }
        
        if (isset($params['data_presence'])) {
            $conditions[] = 'f.data = :data_presence';
            $bindings[':data_presence'] = $params['data_presence'];
        }
        
        if (isset($params['period_id'])) {
            $conditions[] = 'f.periodo_id = :period_id';
            $bindings[':period_id'] = $params['period_id'];
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY f.data DESC, b.periodo DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            
            return $stmt->fetchAll(\PDO::FETCH_CLASS);    
        } catch (\PDOException $e) {
            
            throw new \Exception("Database query error: " . $e->getMessage());
        }
    }

    public function create(array $params)
    {
        $class = $this->model->create($params);
    
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    turma_disciplina_id = :turma_disciplina_id,
                    periodo_id = :periodo_id,
                    estudante_turma_id = :estudante_turma_id,
                    data = :data,
                    faltas = :faltas"
                    
            );
            if($this->checkIfExistsFrequency($class)) {
                $this->removeFrequency($class);
            }
      
            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':turma_disciplina_id' => $class->turma_disciplina_id,
                ':periodo_id' => $class->periodo_id,
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':data' => $class->data ?? null,
                ':faltas' => $class->faltas,
            ]);
  
            if (!$create) {
                return null;
            }
    
            return $this->findByUuid($class->uuid);
        } catch (\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }    

    private function checkIfExistsFrequency($class) :?bool {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM frequencias WHERE data = :data AND estudante_turma_id = :estudante_turma_id AND periodo_id = :periodo_id");
            $select = $stmt->execute([
                ':data' => $class->data,
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':periodo_id' => $class->periodo_id
            ]);
            if($select && $stmt->fetch()) {
                return true;
            }
            
            return false;
        } catch(\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
    private function removeFrequency($class) :?bool {
        try {
            $stmt = $this->conn->prepare("DELETE FROM frequencias WHERE data = :data AND estudante_turma_id = :estudante_turma_id AND periodo_id = :periodo_id");
            $delete = $stmt->execute([
                ':data' => $class->data,
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':periodo_id' => $class->periodo_id
            ]);
            if($delete) {
                return true;
            }
            return false;
        } catch(\Throwable $th) {
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function update(array $data, int $id)
    {
        $frequencia = $this->findById($id);

        $class = $this->model->update($data, $frequencia);

        try {
            $stmt = $this->conn->prepare(
                "UPDATE " . self::TABLE . " 
                SET 
                    turma_disciplina_id = :turma_disciplina_id,
                    periodo_id = :periodo_id,
                    estudante_turma_id = :estudante_turma_id,
                    faltas = :faltas,
                    data = :data,
                    ativo = :ativo
                WHERE id = :id"
            );

            $update = $stmt->execute([
                ':turma_disciplina_id' => $class->turma_disciplina_id,
                ':periodo_id' => $class->periodo_id,
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':faltas' => $class->faltas ?? null,
                ':data' => $class->data ?? null,
                ':id' => $id
            ]);

            if (!$update) {
                return null;
            }

            return $class;
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

    public function selectFrequencies(int $turmaDisciplinaId, int $turmaEstudanteId)
    {
        $sql = "SELECT 
                    f.id,
                    f.uuid,
                    f.turma_disciplina_id,
                    f.periodo_id,
                    f.estudante_turma_id,
                    f.faltas,
                    f.data,
                    f.created_at,
                    f.updated_at
                FROM " . self::TABLE . " f
                WHERE f.turma_disciplina_id = :turma_disciplina_id
                AND f.estudante_turma_id = :estudante_turma_id";

        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':turma_disciplina_id' => $turmaDisciplinaId,
                ':estudante_turma_id' => $turmaEstudanteId
            ]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return [];
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

}