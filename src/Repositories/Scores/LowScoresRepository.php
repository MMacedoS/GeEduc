<?php

namespace App\Repositories\Scores;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Controllers\v1\Traits\UserToPerson;
use App\Interfaces\Scores\ILowScoresRepository;
use App\Utils\LoggerHelper;
use PDO;

class LowScoresRepository extends SingletonInstance implements ILowScoresRepository
{
    use UserToPerson;

    protected $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Busca o ID do coordenador baseado na pessoa física logada
     * @return int|null
     */
    private function getCoordenadorId()
    {
        try {
            // Garantir que a sessão está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Verificar se existe usuário logado
            if (!isset($_SESSION['user']) || !$_SESSION['user']) {
                return null;
            }

            $pessoaFisica = $this->authUser();

            if (!$pessoaFisica) {
                return null;
            }

            $sql = "SELECT c.id FROM coordenadores c WHERE c.pessoa_fisica_id = :pessoa_fisica_id AND c.ativo = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pessoa_fisica_id', $pessoaFisica->id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao buscar coordenador: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retorna cláusula WHERE para filtrar apenas turmas do coordenador logado
     * @return string
     */
    private function getCoordenadorTurmasFilter()
    {
        try {
            // Garantir que a sessão está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Verificar se existe usuário logado
            if (!isset($_SESSION['user']) || !$_SESSION['user']) {
                return " AND 1 = 0 ";
            }

            // Verificar se é admin ou administrativo (acesso total)
            $userPainel = $_SESSION['user']->painel ?? null;
            if (in_array($userPainel, ['administrativo', 'secretaria'])) {
                return " "; // Sem filtro para admin
            }

            $coordenadorId = $this->getCoordenadorId();

            if (!$coordenadorId) {
                // Se não for coordenador ou não encontrar, retorna filtro que não retorna nada
                return " AND 1 = 0 ";
            }

            return " AND t.id IN (SELECT cat.turma_id FROM coordenador_as_turma cat WHERE cat.coordenador_id = {$coordenadorId}) ";
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao aplicar filtro de coordenador: ' . $e->getMessage());
            return " AND 1 = 0 ";
        }
    }

    public function getLowScoresByClass(array $params = [])
    {
        try {
            $anoLetivo = $params['ano_letivo'] ?? date('Y');
            $limiteNota = $params['limite_nota'] ?? 6.0; // Nota mínima para não precisar de recuperação

            // Filtro para coordenador
            $coordenadorFilter = $this->getCoordenadorTurmasFilter();

            $sql = "SELECT 
                        t.id as turma_id,
                        t.nome as turma_nome,
                        t.turno,
                        COUNT(DISTINCT et.estudante_id) as total_estudantes_com_notas_baixas
                    FROM turmas t
                    INNER JOIN turma_disciplina td ON t.id = td.turma_id
                    INNER JOIN atividade a ON td.id = a.turma_disciplina_id
                    INNER JOIN notas n ON a.id = n.atividade_id
                    INNER JOIN estudante_turma et ON n.estudante_turma_id = et.id
                    WHERE et.ano_letivo = :ano_letivo
                        AND td.ano_letivo = :ano_letivo_td
                        AND t.ativo = 1
                        AND et.ativo = 1
                        AND a.ativo = 1
                        AND td.ativo = 1
                        {$coordenadorFilter}
                    GROUP BY t.id, t.nome, t.turno, et.estudante_id, td.id
                    HAVING 
                        CASE 
                            WHEN SUM(a.valor) > 0 THEN 
                                SUM(n.nota * a.valor) / SUM(a.valor)  -- Média ponderada
                            ELSE 
                                AVG(n.nota)  -- Média aritmética se não houver pesos
                        END < :limite_nota";

            // Agora vamos agrupar novamente para contar estudantes por turma
            $finalSql = "SELECT 
                            turma_id,
                            turma_nome,
                            turno,
                            COUNT(*) as total_estudantes_com_notas_baixas
                        FROM ($sql) as subquery
                        GROUP BY turma_id, turma_nome, turno
                        ORDER BY turma_nome";

            $stmt = $this->conn->prepare($finalSql);
            $stmt->bindParam(':ano_letivo', $anoLetivo);
            $stmt->bindParam(':ano_letivo_td', $anoLetivo);
            $stmt->bindParam(':limite_nota', $limiteNota);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Formatar dados para o ApexCharts
            $chartData = [
                'categories' => [],
                'series' => []
            ];

            if (empty($result)) {
                // Se não há dados, retorna dados de exemplo
                $chartData['categories'] = ['Nenhum estudante'];
                $chartData['series'] = [0];
            } else {
                foreach ($result as $row) {
                    $chartData['categories'][] = $row['turma_nome'] . ' (' . ucfirst($row['turno']) . ')';
                    $chartData['series'][] = (int) $row['total_estudantes_com_notas_baixas'];
                }
            }

            return [
                'success' => true,
                'data' => $result,
                'chart_data' => $chartData
            ];
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao buscar estudantes com pontuações baixas por turma: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                'data' => [],
                'chart_data' => ['categories' => [], 'series' => []]
            ];
        }
    }

    public function getStudentsWithLowScores(array $params = [])
    {
        try {
            $anoLetivo = $params['ano_letivo'] ?? date('Y');
            $limiteNota = $params['limite_nota'] ?? 6.0;
            $turmaId = $params['turma_id'] ?? null;

            // Filtro para coordenador
            $coordenadorFilter = $this->getCoordenadorTurmasFilter();

            $sql = "SELECT 
                        e.id as estudante_id,
                        pf.nome as estudante_nome,
                        t.nome as turma_nome,
                        d.nome as disciplina_nome,
                        CASE 
                            WHEN SUM(a.valor) > 0 THEN 
                                ROUND(SUM(n.nota * a.valor) / SUM(a.valor), 2)  -- Média ponderada
                            ELSE 
                                ROUND(AVG(n.nota), 2)  -- Média aritmética se não houver pesos
                        END as media_nota,
                        COUNT(n.id) as total_atividades,
                        SUM(a.valor) as soma_pesos,
                        CASE 
                            WHEN (
                                CASE 
                                    WHEN SUM(a.valor) > 0 THEN 
                                        SUM(n.nota * a.valor) / SUM(a.valor)
                                    ELSE 
                                        AVG(n.nota)
                                END
                            ) < :limite_nota THEN 'Recuperação'
                            ELSE 'Aprovado'
                        END as status_recuperacao
                    FROM estudantes e
                    INNER JOIN pessoa_fisica pf ON e.pessoa_fisica_id = pf.id
                    INNER JOIN estudante_turma et ON e.id = et.estudante_id
                    INNER JOIN turmas t ON et.turma_id = t.id
                    INNER JOIN notas n ON et.id = n.estudante_turma_id
                    INNER JOIN atividade a ON n.atividade_id = a.id
                    INNER JOIN turma_disciplina td ON a.turma_disciplina_id = td.id
                    INNER JOIN professor_disciplina pd ON td.professor_disciplina_id = pd.id
                    INNER JOIN disciplinas d ON pd.disciplina_id = d.id
                    WHERE et.ano_letivo = :ano_letivo
                        AND td.ano_letivo = :ano_letivo_td
                        AND t.ativo = 1
                        AND et.ativo = 1
                        AND a.ativo = 1
                        AND td.ativo = 1
                        {$coordenadorFilter}";

            $bindings = [
                ':ano_letivo' => $anoLetivo,
                ':ano_letivo_td' => $anoLetivo,
                ':limite_nota' => $limiteNota
            ];

            if ($turmaId) {
                $sql .= " AND t.id = :turma_id";
                $bindings[':turma_id'] = $turmaId;
            }

            $sql .= " GROUP BY e.id, pf.nome, t.nome, d.nome, t.id, d.id
                     HAVING 
                        CASE 
                            WHEN SUM(a.valor) > 0 THEN 
                                SUM(n.nota * a.valor) / SUM(a.valor)  -- Média ponderada
                            ELSE 
                                AVG(n.nota)  -- Média aritmética se não houver pesos
                        END < :limite_nota_filter
                     ORDER BY t.nome, pf.nome, d.nome";

            $bindings[':limite_nota_filter'] = $limiteNota;

            $stmt = $this->conn->prepare($sql);
            foreach ($bindings as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Formatar as notas para 2 casas decimais
            foreach ($result as &$row) {
                $row['media_nota'] = number_format((float)$row['media_nota'], 2, '.', '');
            }

            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao buscar detalhes dos estudantes com pontuações baixas: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function getLowScoresStatistics(array $params = [])
    {
        try {
            $anoLetivo = $params['ano_letivo'] ?? date('Y');
            $limiteNota = $params['limite_nota'] ?? 6.0;

            // Filtro para coordenador
            $coordenadorFilter = $this->getCoordenadorTurmasFilter();

            $sql = "SELECT 
                        COUNT(DISTINCT et.estudante_id) as total_estudantes_recuperacao,
                        COUNT(DISTINCT t.id) as total_turmas_afetadas,
                        COUNT(DISTINCT d.id) as total_disciplinas_afetadas,
                        AVG(media_notas.media_estudante) as media_geral_notas_baixas,
                        MIN(media_notas.media_estudante) as menor_media,
                        MAX(media_notas.media_estudante) as maior_media_recuperacao,
                        COUNT(DISTINCT a.id) as total_atividades_realizadas
                    FROM (
                        SELECT 
                            et.estudante_id,
                            et.id as estudante_turma_id,
                            CASE 
                                WHEN SUM(a.valor) > 0 THEN 
                                    SUM(n.nota * a.valor) / SUM(a.valor)  -- Média ponderada
                                ELSE 
                                    AVG(n.nota)  -- Média aritmética se não houver pesos
                            END as media_estudante
                        FROM estudante_turma et
                        INNER JOIN turmas t ON et.turma_id = t.id
                        INNER JOIN notas n ON et.id = n.estudante_turma_id
                        INNER JOIN atividade a ON n.atividade_id = a.id
                        INNER JOIN turma_disciplina td ON a.turma_disciplina_id = td.id
                        WHERE et.ano_letivo = :ano_letivo
                            AND td.ano_letivo = :ano_letivo_td
                            AND et.ativo = 1
                            AND a.ativo = 1
                            AND td.ativo = 1
                            AND t.ativo = 1
                            {$coordenadorFilter}
                        GROUP BY et.estudante_id, et.id, td.id
                        HAVING 
                            CASE 
                                WHEN SUM(a.valor) > 0 THEN 
                                    SUM(n.nota * a.valor) / SUM(a.valor)
                                ELSE 
                                    AVG(n.nota)
                            END < :limite_nota
                    ) as media_notas
                    INNER JOIN estudante_turma et ON media_notas.estudante_turma_id = et.id
                    INNER JOIN turmas t ON et.turma_id = t.id
                    INNER JOIN notas n ON et.id = n.estudante_turma_id
                    INNER JOIN atividade a ON n.atividade_id = a.id
                    INNER JOIN turma_disciplina td ON a.turma_disciplina_id = td.id
                    INNER JOIN professor_disciplina pd ON td.professor_disciplina_id = pd.id
                    INNER JOIN disciplinas d ON pd.disciplina_id = d.id
                    WHERE t.ativo = 1
                        {$coordenadorFilter}";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ano_letivo', $anoLetivo);
            $stmt->bindParam(':ano_letivo_td', $anoLetivo);
            $stmt->bindParam(':limite_nota', $limiteNota);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Formatar números para melhor apresentação
            if ($result) {
                $result['media_geral_notas_baixas'] = number_format((float)$result['media_geral_notas_baixas'], 2, '.', '');
                $result['menor_media'] = number_format((float)$result['menor_media'], 2, '.', '');
                $result['maior_media_recuperacao'] = number_format((float)$result['maior_media_recuperacao'], 2, '.', '');
            }

            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao buscar estatísticas de pontuações baixas: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Busca alunos reprovados por disciplina em uma turma específica
     * Baseado na média dos 4 bimestres
     */
    public function getFailedStudentsByDisciplineAndClass(array $params = [])
    {
        try {
            $anoLetivo = $params['ano_letivo'] ?? date('Y');
            $turmaId = $params['turma_id'] ?? null;

            // Filtro para coordenador
            $coordenadorFilter = $this->getCoordenadorTurmasFilter();

            $sql = "SELECT 
                        d.id as disciplina_id,
                        d.nome as disciplina_nome,
                        COUNT(DISTINCT et.estudante_id) as total_reprovados
                    FROM disciplinas d
                    INNER JOIN professor_disciplina pd ON d.id = pd.disciplina_id
                    INNER JOIN turma_disciplina td ON pd.id = td.professor_disciplina_id
                    INNER JOIN turmas t ON td.turma_id = t.id
                    INNER JOIN estudante_turma et ON t.id = et.turma_id
                    INNER JOIN (
                        SELECT 
                            n.estudante_turma_id,
                            a.turma_disciplina_id,
                            SUM(n.nota) as soma_anual
                        FROM notas n
                        INNER JOIN atividade a ON n.atividade_id = a.id
                        INNER JOIN periodo p ON n.periodo_id = p.id
                        WHERE p.periodo IN (1, 2, 3, 4)  -- 4 bimestres
                        GROUP BY n.estudante_turma_id, a.turma_disciplina_id
                        HAVING COUNT(DISTINCT p.periodo) = 4  -- Só alunos com notas nos 4 bimestres
                            AND SUM(n.nota) < 28  -- Soma menor que 27.6 = reprovado
                    ) as somas_baixas ON et.id = somas_baixas.estudante_turma_id 
                        AND td.id = somas_baixas.turma_disciplina_id
                    WHERE et.ano_letivo = :ano_letivo
                        AND td.ano_letivo = :ano_letivo_td
                        AND t.ativo = 1
                        AND et.ativo = 1
                        AND td.ativo = 1
                        AND d.ativo = 1
                        {$coordenadorFilter}";

            $bindings = [
                ':ano_letivo' => $anoLetivo,
                ':ano_letivo_td' => $anoLetivo
            ];

            if ($turmaId) {
                $sql .= " AND t.id = :turma_id";
                $bindings[':turma_id'] = $turmaId;
            }

            $sql .= " GROUP BY d.id, d.nome
                     ORDER BY d.nome";

            $stmt = $this->conn->prepare($sql);
            foreach ($bindings as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Formatar dados para o ApexCharts
            $chartData = [
                'categories' => [],
                'series' => []
            ];

            if (empty($result)) {
                $chartData['categories'] = ['Nenhuma reprovação'];
                $chartData['series'] = [0];
            } else {
                foreach ($result as $row) {
                    $chartData['categories'][] = $row['disciplina_nome'];
                    $chartData['series'][] = (int) $row['total_reprovados'];
                }
            }

            return [
                'success' => true,
                'data' => $result,
                'chart_data' => $chartData
            ];
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao buscar alunos reprovados por disciplina: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                'data' => [],
                'chart_data' => ['categories' => [], 'series' => []]
            ];
        }
    }

    /**
     * Busca as turmas disponíveis para o coordenador logado
     */
    public function getClassesForCoordinator()
    {
        try {
            // Filtro para coordenador
            $coordenadorFilter = $this->getCoordenadorTurmasFilter();

            $sql = "SELECT 
                        t.id,
                        t.nome,
                        t.turno,
                        COUNT(DISTINCT et.estudante_id) as total_estudantes
                    FROM turmas t
                    LEFT JOIN estudante_turma et ON t.id = et.turma_id 
                        AND et.ativo = 1 
                        AND et.ano_letivo = :ano_letivo
                    WHERE t.ativo = 1
                        {$coordenadorFilter}
                    GROUP BY t.id, t.nome, t.turno
                    ORDER BY t.ordem, t.nome";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':ano_letivo', date('Y'));
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $result
            ];
        } catch (\Exception $e) {
            LoggerHelper::logInfo('Erro ao buscar turmas do coordenador: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
}
