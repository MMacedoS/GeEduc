<?php

namespace App\Repositories\Scores;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Scores\INotaRepository;
use App\Models\Scores\Nota;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class NotaRepository extends SingletonInstance implements INotaRepository {
    const CLASS_NAME = Nota::class;
    const TABLE = 'notas';

    use FindTrait;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Nota();
    }

    public function allScores(array $params = [])
    {
        $sql = "SELECT
            n.*, 
            JSON_OBJECT(
                'id', td.id,
                'uuid', td.uuid,
                'atividades', JSON_OBJECT(
                    'id', a.id,
                    'uuid', a.uuid,
                    'tipo', a.tipo
                ) 
            ) AS turmas_details
            FROM notas n
            LEFT JOIN atividade a ON n.atividade_id = a.id
            LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
            LEFT JOIN estudante_turma et ON et.id = n.estudante_turma_id";
        
        
        $conditions = [];
        $bindings = [];
        
        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'a.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }
        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }

        if (isset($params['period_id'])) {
            $conditions[] = 'n.periodo_id = :period_id';
            $bindings[':period_id'] = $params['period_id'];
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY n.created_at DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            
            return $stmt->fetchAll(PDO::FETCH_CLASS);    
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function getNotasByEstudante(int $estudanteTurmaId): array
    {
        $stmt = $this->conn->prepare("SELECT nota FROM notas WHERE estudante_turma_id = :id");
        $stmt->bindParam(':id', $estudanteTurmaId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $params)
    {
        $class = $this->model->create($params);
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    atividade_id = :atividade_id,
                    periodo_id = :periodo_id,
                    estudante_turma_id = :estudante_turma_id,
                    nota = :nota"
            );

            $idScore = $this->checkIfExistsScore($class);
            if($idScore) {
                $this->removeScore($idScore);
            }

            $create = $stmt->execute([
                ':uuid' => $class->uuid,
                ':atividade_id' => $class->atividade_id,
                ':periodo_id' => $class->periodo_id,
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':nota' => $class->nota
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

    private function checkIfExistsScore($class) :?String {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM notas WHERE estudante_turma_id = :estudante_turma_id AND periodo_id = :periodo_id AND atividade_id = :atividade_id");
            $stmt->execute([
                ':estudante_turma_id' => $class->estudante_turma_id,
                ':periodo_id' => $class->periodo_id,
                ':atividade_id' => $class->atividade_id
            ]);

            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $id['id'] ?? null;
        } catch(\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação select: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    private function removeScore($id) :?bool 
    {
        $scores = $this->findById((int)$id);

        if (is_null($scores)) {
            return null;
        }
        
        try {
            $stmt = $this->conn->prepare("DELETE FROM notas WHERE id = :id");
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

    public function allScoresByStudents(array $params = [])
    {
        $sql = "SELECT 
                    d.nome AS disciplina,
                    GROUP_CONCAT(
                        CASE WHEN b.id = 1 THEN CONCAT('1º Bim: ', a.tipo, ': ', n.nota) END
                        SEPARATOR '</br>'
                    ) AS notas_bimestre1,

                    GROUP_CONCAT(
                        CASE WHEN b.id = 2 THEN CONCAT('2º Bim: ', a.tipo, ': ', n.nota) END
                        SEPARATOR '</br>'
                    ) AS notas_bimestre2,

                    GROUP_CONCAT(
                        CASE WHEN b.id = 3 THEN CONCAT('3º Bim: ', a.tipo, ': ', n.nota) END
                        SEPARATOR '</br>'
                    ) AS notas_bimestre3,

                    GROUP_CONCAT(
                        CASE WHEN b.id = 4 THEN CONCAT('4º Bim: ', a.tipo, ': ', n.nota) END
                        SEPARATOR '</br>'
                    ) AS notas_bimestre4,
                    SUM(n.nota) as media,
                    pf.nome as professor
                FROM 
                    notas n
                LEFT JOIN 
                    estudante_turma et ON n.estudante_turma_id = et.id
                LEFT JOIN 
                    atividade a ON n.atividade_id = a.id
                LEFT JOIN 
                    turma_disciplina td ON a.turma_disciplina_id = td.id
                LEFT JOIN 
                    professor_disciplina pd ON td.professor_disciplina_id = pd.id
                LEFT JOIN 
                    professores p ON pd.professor_id = p.id
                LEFT JOIN 
                    pessoa_fisica pf ON p.pessoa_fisica_id = pf.id
                LEFT JOIN 
                    disciplinas d ON pd.disciplina_id = d.id
                LEFT JOIN 
                    periodo b ON n.periodo_id = b.id                
            ";
        
        
        $conditions = [];
        $bindings = [];
        
        if (isset($params['student_class_id'])) {
            $conditions[] = 'n.estudante_turma_id = :student_class_id';
            $bindings[':student_class_id'] = $params['student_class_id'];
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY 
                    pf.nome, et.id, d.id
                ORDER BY 
                 d.nome;";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            
            return $stmt->fetchAll(PDO::FETCH_CLASS);    
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }
}