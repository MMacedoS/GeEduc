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

    public function studentsByTurmaDisciplinaAndScore(array $params = [])
    {
        $sql = "SELECT
                    et.id as estudante_turma_id,
                    e.id as estudante_id,
                    e.uuid as estudante_uuid,
                    pf.nome as estudante_nome,
                    SUM(COALESCE(n.nota, 0)) AS media,
                    COUNT(n.id) AS total_atividades,
                    JSON_OBJECT(
                        'id', e.id,
                        'uuid', e.uuid,
                        'nome', pf.nome
                    ) AS estudante,
                    r.id AS recuperacao_id,
                    r.uuid AS recuperacao_uuid,
                    r.ano_letivo,
                    r.nota AS nota,
                    r.periodo AS recuperacao_periodo,
                    r.obs
                FROM estudante_turma et
                LEFT JOIN estudantes e ON e.id = et.estudante_id
                LEFT JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id
                LEFT JOIN notas n ON n.estudante_turma_id = et.id 
                    AND n.periodo_id BETWEEN :periodoOne AND :periodoTwo
                LEFT JOIN atividade a ON a.id = n.atividade_id
                LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
                LEFT JOIN recuperacao r ON r.estudante_turma_id = et.id 
                    AND r.periodo = :type 
                    AND r.turma_disciplina_id = :turma_disciplina_id
                WHERE td.id = :turma_disciplina_id
                GROUP BY et.id, e.id, e.uuid, pf.nome, r.id, r.uuid, r.ano_letivo, r.nota, r.periodo, r.obs
                HAVING SUM(COALESCE(n.nota, 0)) < :total
                ORDER BY media ASC, pf.nome ASC";

        try {
            if (!isset($params['turma_disciplina_id'])) {
                throw new \InvalidArgumentException("Parâmetro 'turma_disciplina_id' é obrigatório");
            }

            if (!isset($params['type'])) {
                throw new \InvalidArgumentException("Parâmetro 'type' é obrigatório");
            }

            if (!isset($params['total'])) {
                throw new \InvalidArgumentException("Parâmetro 'total' é obrigatório");
            }

            $bindings = [
                ':periodoOne' => $params['periodoOne'] ?? 1,
                ':periodoTwo' => $params['periodoTwo'] ?? 2,
                ':type' => $params['type'],
                ':total' => $params['total'],
                ':turma_disciplina_id' => $params['turma_disciplina_id']
            ];

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            LoggerHelper::logInfo(sprintf(
                "studentsByTurmaDisciplinaAndScore: %d estudantes encontrados (turma_disciplina: %s, nota < %.2f)",
                count($result),
                $bindings[':turma_disciplina_id'],
                $bindings[':total']
            ));

            return $result;
        } catch (\InvalidArgumentException $e) {
            LoggerHelper::logInfo("Erro de validação em studentsByTurmaDisciplinaAndScore: " . $e->getMessage());
            throw $e;
        } catch (\PDOException $e) {
            $errorMsg = sprintf(
                "Erro SQL em studentsByTurmaDisciplinaAndScore - Turma/Disciplina: %s, Erro: %s",
                $params['turma_disciplina_id'] ?? 'N/A',
                $e->getMessage()
            );
            LoggerHelper::logInfo($errorMsg);
            throw new \Exception("Erro ao buscar estudantes por turma/disciplina: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            LoggerHelper::logInfo("Erro inesperado em studentsByTurmaDisciplinaAndScore: " . $e->getMessage());
            throw $e;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    public function studentToFailed(array $params = [])
    {
        $sql = "SELECT 
                    e.id AS estudante_id,
                    e.uuid AS estudante_uuid,
                    et.id AS estudante_turma_id,
                    pf.nome AS estudante_nome,
                    pf.email AS estudante_email,
                    COUNT(DISTINCT d.id) AS quantidade_disciplinas_reprovadas,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            d.nome, 
                            ' (Média: ', ROUND(soma_por_disciplina.soma_notas, 2), 
                            CASE 
                                WHEN soma_por_disciplina.recup_nota IS NOT NULL 
                                THEN CONCAT(' + Recup: ', ROUND(soma_por_disciplina.recup_nota, 2))
                                ELSE ''
                            END,
                            ' = ', ROUND(soma_por_disciplina.total_com_recup, 2), ')'
                        )
                        ORDER BY d.nome 
                        SEPARATOR ', '
                    ) AS disciplinas_reprovadas_detalhes,
                    GROUP_CONCAT(DISTINCT d.nome ORDER BY d.nome SEPARATOR ', ') AS disciplinas_reprovadas,
                    AVG(soma_por_disciplina.soma_notas) AS media_geral_reprovacoes,
                    AVG(soma_por_disciplina.total_com_recup) AS media_geral_com_recuperacao,
                    SUM(CASE WHEN soma_por_disciplina.recup_nota IS NOT NULL THEN 1 ELSE 0 END) AS disciplinas_com_recuperacao
                FROM (
                    SELECT
                        et.id as et_id,
                        e.id as e_id,
                        d.id as d_id,
                        td.id as td_id,
                        SUM(COALESCE(n.nota, 0)) AS soma_notas,
                        r.nota AS recup_nota,
                        (SUM(COALESCE(n.nota, 0)) + COALESCE(r.nota, 0)) AS total_com_recup
                    FROM notas n
                    INNER JOIN estudante_turma et ON et.id = n.estudante_turma_id
                    INNER JOIN estudantes e ON e.id = et.estudante_id
                    INNER JOIN atividade a ON a.id = n.atividade_id
                    INNER JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
                    INNER JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id
                    INNER JOIN disciplinas d ON d.id = pd.disciplina_id
                    LEFT JOIN recuperacao r ON r.estudante_turma_id = et.id 
                        AND r.turma_disciplina_id = td.id
                        AND (
                            (:periodOne = 1 AND :periodTwo = 2 AND r.periodo = 'I Semestre')
                            OR (:periodOne = 3 AND :periodTwo = 4 AND r.periodo = 'II Semestre')
                            OR (:periodOne = 1 AND :periodTwo = 4 AND r.periodo IN ('I Semestre', 'II Semestre', 'Exames Finais'))
                        )
                    WHERE n.periodo_id BETWEEN :periodOne AND :periodTwo
                        AND td.turma_id = :class
                    GROUP BY et.id, e.id, d.id, td.id, r.nota
                    HAVING total_com_recup < :total
                ) AS soma_por_disciplina
                INNER JOIN estudante_turma et ON et.id = soma_por_disciplina.et_id
                INNER JOIN estudantes e ON e.id = soma_por_disciplina.e_id
                INNER JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id
                INNER JOIN disciplinas d ON d.id = soma_por_disciplina.d_id
                GROUP BY e.id, e.uuid, et.id, pf.nome, pf.email
                ORDER BY quantidade_disciplinas_reprovadas DESC, pf.nome ASC";

        try {
            $stmt = $this->conn->prepare($sql);

            $bindings = [
                ':periodOne' => $params['periodOne'] ?? 1,
                ':periodTwo' => $params['periodTwo'] ?? 2,
                ':class' => $params['class_room'],
                ':total' => $params['total'] ?? 13.9
            ];

            $stmt->execute($bindings);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            return $result;
        } catch (\PDOException $e) {
            $errorMsg = sprintf(
                "Erro ao buscar estudantes reprovados - Turma: %s, Erro: %s",
                $params['class_room'] ?? 'N/A',
                $e->getMessage()
            );
            LoggerHelper::logInfo($errorMsg);
            throw new \Exception("Erro ao consultar estudantes reprovados: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            LoggerHelper::logInfo("Erro inesperado em studentToFailed: " . $e->getMessage());
            throw $e;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    public function studentsByTurmaDisciplinaWithRecovery(array $params = [])
    {
        $sql = "SELECT
                    et.id as estudante_turma_id,
                    e.id as estudante_id,
                    e.uuid as estudante_uuid,
                    pf.nome as estudante_nome,
                    SUM(COALESCE(n.nota, 0)) AS media_notas,
                    COUNT(n.id) AS total_atividades,
                    JSON_OBJECT(
                        'id', e.id,
                        'uuid', e.uuid,
                        'nome', pf.nome
                    ) AS estudante,
                    
                    rs1.id AS recup_sem1_id,
                    rs1.nota AS recup_sem1_nota,
                    rs1.obs AS recup_sem1_obs,
                    
                    rs2.id AS recup_sem2_id,
                    rs2.nota AS recup_sem2_nota,
                    rs2.obs AS recup_sem2_obs,
                    
                    (COALESCE(rs1.nota, 0) + COALESCE(rs2.nota, 0)) AS recuperacoes_semestrais,
                    
                    nf.id AS nota_final_id,
                    nf.uuid AS nota_final_uuid,
                    nf.ano_letivo AS nota_final_ano_letivo,
                    nf.nota AS nota_final,
                    nf.situacao AS nota_final_situacao,
                    CAST(NULL AS CHAR) AS nota_final_obs,
                    
                    (SUM(COALESCE(n.nota, 0)) + COALESCE(rs1.nota, 0) + COALESCE(rs2.nota, 0)) AS media_total,
                    
                    CASE 
                        WHEN nf.nota >= 7.0
                        THEN 'Aprovado com Exame Final'
                        WHEN nf.nota IS NOT NULL 
                        THEN 'Reprovado no Exame Final'
                        WHEN (SUM(COALESCE(n.nota, 0)) + COALESCE(rs1.nota, 0) + COALESCE(rs2.nota, 0)) >= :total_aprovacao 
                        THEN 'Aprovado com Recuperação'
                        WHEN (rs1.nota IS NOT NULL OR rs2.nota IS NOT NULL)
                        THEN 'Aguardando Exame Final'
                        ELSE 'Reprovado sem Recuperação'
                    END AS situacao
                FROM estudante_turma et
                LEFT JOIN estudantes e ON e.id = et.estudante_id
                LEFT JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id
                LEFT JOIN notas n ON n.estudante_turma_id = et.id 
                    AND n.periodo_id BETWEEN :periodoOne AND :periodoTwo
                LEFT JOIN atividade a ON a.id = n.atividade_id
                LEFT JOIN turma_disciplina td ON td.id = a.turma_disciplina_id
                LEFT JOIN recuperacao rs1 ON rs1.estudante_turma_id = et.id 
                    AND rs1.turma_disciplina_id = :turma_disciplina_id
                    AND rs1.periodo = 'I Semestre'
                LEFT JOIN recuperacao rs2 ON rs2.estudante_turma_id = et.id 
                    AND rs2.turma_disciplina_id = :turma_disciplina_id
                    AND rs2.periodo = 'II Semestre'
                LEFT JOIN nota_final nf ON nf.estudante_turma_id = et.id 
                    AND nf.turma_disciplina_id = :turma_disciplina_id
                WHERE td.id = :turma_disciplina_id
                GROUP BY et.id, e.id, e.uuid, pf.nome, 
                         rs1.id, rs1.nota, rs1.obs,
                         rs2.id, rs2.nota, rs2.obs,
                         nf.id, nf.uuid, nf.ano_letivo, nf.nota, nf.situacao
                HAVING SUM(COALESCE(n.nota, 0)) < :total_minimo
                    AND (SUM(COALESCE(n.nota, 0)) + COALESCE(rs1.nota, 0) + COALESCE(rs2.nota, 0)) < :total_aprovacao
                ORDER BY media_total DESC, pf.nome ASC";

        try {
            if (!isset($params['turma_disciplina_id'])) {
                throw new \InvalidArgumentException("Parâmetro 'turma_disciplina_id' é obrigatório");
            }

            if (!isset($params['type'])) {
                throw new \InvalidArgumentException("Parâmetro 'type' é obrigatório");
            }

            if (!isset($params['total_minimo'])) {
                throw new \InvalidArgumentException("Parâmetro 'total_minimo' é obrigatório");
            }

            $bindings = [
                ':periodoOne' => $params['periodoOne'] ?? 1,
                ':periodoTwo' => $params['periodoTwo'] ?? 2,
                ':type' => $params['type'],
                ':total_minimo' => $params['total_minimo'],
                ':total_aprovacao' => $params['total_aprovacao'] ?? 27.9,
                ':turma_disciplina_id' => $params['turma_disciplina_id']
            ];

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($bindings);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            LoggerHelper::logInfo(sprintf(
                "studentsByTurmaDisciplinaWithRecovery: %d estudantes encontrados (turma_disciplina: %s, nota mínima: %.2f, aprovação: %.2f)",
                count($result),
                $bindings[':turma_disciplina_id'],
                $bindings[':total_minimo'],
                $bindings[':total_aprovacao']
            ));

            return $result;
        } catch (\InvalidArgumentException $e) {
            LoggerHelper::logInfo("Erro de validação em studentsByTurmaDisciplinaWithRecovery: " . $e->getMessage());
            throw $e;
        } catch (\PDOException $e) {
            $errorMsg = sprintf(
                "Erro SQL em studentsByTurmaDisciplinaWithRecovery - Turma/Disciplina: %s, Erro: %s",
                $params['turma_disciplina_id'] ?? 'N/A',
                $e->getMessage()
            );
            LoggerHelper::logInfo($errorMsg);
            throw new \Exception("Erro ao buscar estudantes com recuperação: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            LoggerHelper::logInfo("Erro inesperado em studentsByTurmaDisciplinaWithRecovery: " . $e->getMessage());
            throw $e;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    public function studentToFailedDetailed(array $params = [])
    {
        $sql = "SELECT 
                et.id AS estudante_turma_id,
                e.id AS estudante_id,
                e.uuid AS estudante_uuid,
                pf.nome AS estudante_nome,
                pf.email AS estudante_email,
                d.id AS disciplina_id,
                d.nome AS disciplina_nome,
                td.id AS turma_disciplina_id,
                td.turma_id,
                SUM(COALESCE(n.nota, 0)) AS soma_notas,
                COUNT(n.id) AS quantidade_atividades,
                MIN(n.nota) AS nota_minima,
                MAX(n.nota) AS nota_maxima,
                AVG(n.nota) AS media_atividades
            FROM estudante_turma et 
            INNER JOIN estudantes e ON e.id = et.estudante_id 
            INNER JOIN pessoa_fisica pf ON pf.id = e.pessoa_fisica_id 
            LEFT JOIN notas n ON n.estudante_turma_id = et.id 
                AND n.periodo_id BETWEEN :periodOne AND :periodTwo 
            INNER JOIN atividade a ON a.id = n.atividade_id 
            INNER JOIN turma_disciplina td ON td.id = a.turma_disciplina_id 
            INNER JOIN professor_disciplina pd ON pd.id = td.professor_disciplina_id 
            INNER JOIN disciplinas d ON d.id = pd.disciplina_id 
            WHERE td.turma_id = :class 
            GROUP BY et.id, e.id, e.uuid, pf.nome, pf.email, d.id, d.nome, td.id, td.turma_id 
            HAVING soma_notas < :total 
            ORDER BY pf.nome ASC, d.nome ASC";

        try {
            $stmt = $this->conn->prepare($sql);

            $bindings = [
                ':periodOne' => $params['periodOne'] ?? 1,
                ':periodTwo' => $params['periodTwo'] ?? 2,
                ':class' => $params['class_room'],
                ':total' => $params['total'] ?? 13.9
            ];

            $stmt->execute($bindings);
            $result = $stmt->fetchAll(PDO::FETCH_CLASS);

            LoggerHelper::logInfo(sprintf(
                "studentToFailedDetailed: Encontradas %d reprovações detalhadas (turma: %s, períodos: %d-%d, nota mínima: %.2f)",
                count($result),
                $params['class_room'] ?? 'N/A',
                $bindings[':periodOne'],
                $bindings[':periodTwo'],
                $bindings[':total']
            ));

            return $result;
        } catch (\PDOException $e) {
            $errorMsg = sprintf(
                "Erro ao buscar detalhes de reprovações - Turma: %s, Erro: %s",
                $params['class_room'] ?? 'N/A',
                $e->getMessage()
            );
            LoggerHelper::logInfo($errorMsg);
            throw new \Exception("Erro ao consultar detalhes de estudantes reprovados: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            LoggerHelper::logInfo("Erro inesperado em studentToFailedDetailed: " . $e->getMessage());
            throw $e;
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
            if ($idScore) {
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

    private function checkIfExistsScore($recuperacao): ?String
    {
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
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação select: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }

    private function removeScore($id): ?bool
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
            if ($delete) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            LoggerHelper::logInfo("Erro na transação delete: {$th->getMessage()}");
            LoggerHelper::logInfo("Trace: " . $th->getTraceAsString());
            return null;
        } finally {
            Database::getInstance()->closeConnection();
        }
    }
}
