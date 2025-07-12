<?php

namespace App\Repositories\Recuperation;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Interfaces\Recuperation\IRecuperacaoRepository;
use App\Models\recuperation\Recuperacao;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;
use PDO;

class RecuperacaoRepository extends SingletonInstance implements IRecuperacaoRepository
{
    const CLASS_NAME = Recuperacao::class;
    const TABLE = 'recuperacao';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Recuperacao();
    }

    public function all(array $params = [])
    {
        $sql = "SELECT
        r.*, 
        JSON_OBJECT(
            'id', td.id,
            'uuid', td.uuid
        ) AS turmas,
        JSON_OBJECT(
            'id', e.id,
            'uuid', e.uuid,
            'nome', pf.nome
        ) AS estudantes
        FROM recuperacao r
        LEFT JOIN turma_disciplina td ON td.id = r.turma_disciplina_id
        LEFT JOIN estudante_turma et ON et.id = r.estudante_turma_id 
        LEFT JOIN estudantes e ON e.id = et.estudante_id
        LEFT JOIN pessoa_fisica pf on pf.id = e.pessoa_fisica_id
        ";        
        
        $conditions = [];
        $bindings = [];
        
        if (isset($params['class_discipline_id'])) {
            $conditions[] = 'r.turma_disciplina_id = :class_discipline_id';
            $bindings[':class_discipline_id'] = $params['class_discipline_id'];
        }

        if (isset($params['students_class_id'])) {
            $conditions[] = 'e.estudante_class_id = :students_class_id';
            $bindings[':students_class_id'] = $params['students_class_id'];
        }

        if (isset($params['name_email'])) {
            $conditions[] = "(pf.nome LIKE :name_email OR pf.email LIKE :name_email)";
            $bindings[':name_email'] = "%" . $params['name_email'] . "%";
        }

        if (isset($params['period'])) {
            $conditions[] = 'r.periodo_id = :period';
            $bindings[':period'] = $params['period'];
        }
        
        if (count($conditions) > 0) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY pf.nome ASC";

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

    public function studentByScoreLow(array $params = [])
    {
        $sql = "SELECT
                et.id as estudante_turma_id, 
                sum(n.nota) AS media,
                JSON_OBJECT(
                    'id', e.id,
                    'uuid', e.uuid,
                    'nome', pf.nome
                ) AS estudantes,  
                r.id, 
                r.uuid,
                r.ano_letivo, 
                r.nota,
                r.turma_disciplina_id,
                r.periodo,
                r.obs
            FROM estudante_turma et
            LEFT JOIN estudantes e ON e.id = et.estudante_id
            LEFT JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id
            LEFT JOIN notas n ON n.estudante_turma_id = et.id AND (n.periodo_id >= :periodoOne AND n.periodo_id <= :periodoTwo)
            LEFT JOIN atividade a ON a.id = n.atividade_id
            LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
            LEFT JOIN recuperacao r ON r.estudante_turma_id = et.id AND r.periodo = :tipo
            GROUP BY et.id, r.id, r.uuid,r.ano_letivo, r.nota,r.turma_disciplina_id,r.periodo,r.estudante_turma_id,r.obs
            HAVING SUM(COALESCE(n.nota, 0)) < :total;";  
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':periodoOne' => $params['periodoOne'] ?? 1,
                ':periodoTwo' => $params['periodoTwo'] ?? 2,
                ':tipo' => $params['type'],
                ':total' => $params['total']
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_CLASS);    
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function studentToFailed(array $params = []) 
    {
        $sql = "SELECT 
                    estudante_nome,
                    GROUP_CONCAT(DISTINCT disciplina ORDER BY disciplina SEPARATOR ', ') AS disciplinas_reprovadas
                FROM (
                    SELECT
                        pf.nome AS estudante_nome,
                        d.nome AS disciplina,
                        SUM(COALESCE(n.nota, 0)) AS soma_notas

                    FROM notas n
                    LEFT JOIN estudante_turma et ON et.id = n.estudante_turma_id
                    LEFT JOIN estudantes e ON e.id = et.estudante_id
                    LEFT JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id
                    LEFT JOIN atividade a ON a.id = n.atividade_id
                    LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
                    LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id
                    LEFT JOIN disciplinas d ON d.id = pd.disciplina_id

                    WHERE n.periodo_id BETWEEN :periodOne AND :periodTwo
                    AND td.turma_id = :class

                    GROUP BY et.id, d.id, pf.nome, d.nome
                    HAVING soma_notas < :total
                ) AS reprovacoes
                GROUP BY estudante_nome
                ORDER BY estudante_nome;
";

        // $sql = "SELECT 
        //         et.id AS estudante_turma_id, 
        //         td.turma_id, 
        //         pf.nome as estudante, 
        //         d.nome AS disciplina, 
        //         SUM(COALESCE(n.nota, 0)) AS soma 
        //     FROM estudante_turma et 
        //     LEFT JOIN estudantes e ON e.id = et.estudante_id 
        //     LEFT JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id 
        //     LEFT JOIN notas n ON n.estudante_turma_id = et.id AND n.periodo_id BETWEEN :periodOne AND :periodTwo 
        //     LEFT JOIN atividade a ON a.id = n.atividade_id 
        //     LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id 
        //     LEFT JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id 
        //     LEFT JOIN disciplinas d ON d.id = pd.disciplina_id 
        //     WHERE td.turma_id = :class 
        //     GROUP BY et.id, e.id, e.uuid, pf.nome, td.turma_id, d.id, d.nome 
        //     HAVING soma < :total 
        //     ORDER BY pf.nome, d.nome";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':periodOne' => $params['periodOne'] ?? 1,
                ':periodTwo' => $params['periodTwo'] ?? 2,
                ':class' => $params['class_room'],
                ':total' => $params['total'] ?? 13.9
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_CLASS);    
        } catch (\PDOException $e) {
            throw new \Exception("Database query error: " . $e->getMessage());
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    public function create(array $params)
    {
        $recuperacao = $this->model->create($params);
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO " . self::TABLE . " 
                SET 
                    uuid = :uuid,
                    ano_letivo = :ano_letivo,
                    periodo = :periodo,
                    estudante_turma_id = :estudante_turma_id,
                    turma_disciplina_id = :turma_disciplina_id,
                    obs = :obs,
                    nota = :nota"
            );

            $idScore = $this->checkIfExistsScore($recuperacao);
            if($idScore) {
                $this->removeScore($idScore);
            }

            $create = $stmt->execute([
                ':uuid' => $recuperacao->uuid,
                ':turma_disciplina_id' => $recuperacao->turma_disciplina_id,
                ':periodo' => $recuperacao->periodo,
                ':estudante_turma_id' => $recuperacao->estudante_turma_id,
                ':ano_letivo' => $recuperacao->ano_letivo,
                ':obs' => $recuperacao->obs,
                ':nota' => $recuperacao->nota
            ]);
  
            if (!$create) {
                return null;
            }
    
            return true;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação create: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {          
            Database::getInstance()->closeConnection();
        }
    }

    private function checkIfExistsScore($recuperacao) :?String {
        try {
            $stmt = $this->conn
                ->prepare(
                    "SELECT id 
                    FROM " . self::TABLE . " 
                    WHERE estudante_turma_id = :estudante_turma_id 
                    AND periodo = :periodo 
                    AND turma_disciplina_id = :turma_disciplina_id"
                );

            $stmt->execute([
                ':estudante_turma_id' => $recuperacao->estudante_turma_id,
                ':periodo' => $recuperacao->periodo,
                ':turma_disciplina_id' => $recuperacao->turma_disciplina_id
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
}