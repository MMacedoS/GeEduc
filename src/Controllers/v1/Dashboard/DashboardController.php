<?php

namespace App\Controllers\v1\Dashboard;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Controllers\v1\Traits\UserToPerson;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Coordination\ICoordenadorRepository;
use App\Interfaces\Coordination\ICoordenadorTurmaRepository;
use App\Interfaces\Discipline\IDisciplinaRepository;
use App\Interfaces\Frequencies\IFrequenciaRepository;
use App\Interfaces\MonthlyFees\IMensalidadeRepository;
use App\Interfaces\Recuperation\IRecuperacaoRepository;
use App\Interfaces\Scores\IBoletimRepository;
use App\Interfaces\Scores\ILowScoresRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Interfaces\Teacher\IProfessorRepository;
use App\Interfaces\Work_Load\ICargaHorariaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;

class DashboardController extends Controller
{
    use UserToPerson;
    use GenericTrait;

    protected $estudanteTurmaRepository;
    protected $estudanteRepository;
    protected $turmaDisciplinaRepository;
    protected $frequenciaRepository;
    protected $cargaHorariaRepository;
    protected $mensalidadeRepository;
    protected $turmaRepository;
    protected $professorRepository;
    protected $disciplinaRepository;
    protected $boletimRepository;
    protected $lowScoresRepository;
    protected $coordenadorRepository;
    protected $coordenadorTurmaRepository;
    protected $recuperacaoRepository;

    public function __construct(
        ICargaHorariaRepository $cargaHorariaRepository,
        IFrequenciaRepository $frequenciaRepository,
        ITurmaRepository $turmaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IMensalidadeRepository $mensalidadeRepository,
        IEstudanteRepository $estudanteRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        IProfessorRepository $professorRepository,
        IDisciplinaRepository $disciplinaRepository,
        IBoletimRepository $boletimRepository,
        ILowScoresRepository $lowScoresRepository,
        ICoordenadorRepository $coordenadorRepository,
        ICoordenadorTurmaRepository $coordenadorTurmaRepository,
        IRecuperacaoRepository $recuperacaoRepository
    ) {
        parent::__construct();

        $this->cargaHorariaRepository = $cargaHorariaRepository;
        $this->frequenciaRepository = $frequenciaRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->estudanteRepository = $estudanteRepository;
        $this->mensalidadeRepository = $mensalidadeRepository;
        $this->turmaRepository = $turmaRepository;
        $this->professorRepository = $professorRepository;
        $this->disciplinaRepository = $disciplinaRepository;
        $this->boletimRepository = $boletimRepository;
        $this->lowScoresRepository = $lowScoresRepository;
        $this->coordenadorRepository = $coordenadorRepository;
        $this->coordenadorTurmaRepository = $coordenadorTurmaRepository;
        $this->recuperacaoRepository = $recuperacaoRepository;
    }

    public function index(Request $request)
    {
        return $this->prepareIndex($_SESSION['user']->painel);
    }

    public function indexFacility(Request $request)
    {
        return $this->router->view('dashboard/facility', ['active' => 'dashboard']);
    }

    private function prepareIndex(string $painel)
    {
        if ($painel == 'estudante') {
            return $this->indexStudents();
        }

        if ($painel === 'professor') {
            return $this->indexTeacher();
        }

        if ($painel == 'administrativo') {
            return $this->indexAdministrators();
        }

        if ($painel == 'coordenador') {
            return $this->indexCoordenators();
        }

        return $this->router->view('dashboard/index', ['active' => 'dashboard']);
    }

    private function indexStudents()
    {
        $data = [
            'active' => 'dashboard',
        ];
        $pessoaAuth = $this->authUser();

        $estudante = $this->estudanteRepository
            ->studentByPersonId($pessoaAuth->id);

        if (is_null($estudante)) {
            return $this->router->view('dashboard/index', ['active' => 'dashboard']);
        }

        $estudante_turma = $this->estudanteTurmaRepository
            ->studentClassByStudentId($estudante->id);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $estudante_turma->id,
                    'class_id' => $estudante_turma->turma_id
                ]
            );

        if (isset($frequencias) && !empty($frequencias)) {
            $class_discipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);

            $carga_horaria = $this->cargaHorariaRepository->findById($class_discipline->id);

            $total_faltas = $this->sumAbsences($frequencias);
            $carga = $carga_horaria->carga ?? 80;

            $presenca = $carga - $total_faltas;
            $percentual_faltas = round(($total_faltas / $carga) * 100, 2);
            $percentual_presenca = round(($presenca / $carga) * 100, 2);

            $data = array_merge($data, [
                'percentual_faltas' => $percentual_faltas,
                'percentual_presenca' => $percentual_presenca,
                'total_faltas' => $total_faltas,
                'presenca' => $presenca
            ]);
        }

        $notas = $this->boletimRepository->totalScoreByStudentsAndDisciplines(
            [
                'student_class_id' => $estudante_turma->id,
            ]
        );

        $data['notas'] = $notas;

        return $this->router->view(
            'dashboard/index',
            $data
        );
    }

    private function indexAdministrators()
    {
        // Buscar estudantes únicos ativos
        $estudantes = $this->estudanteRepository
            ->allStudents(
                [
                    'active' => 1
                ]
            );

        $discipline = $this->disciplinaRepository
            ->allDisciplines(
                [
                    'active' => 1
                ]
            );

        $turmas = $this->turmaRepository
            ->allClassRooms(
                [
                    'active' => 1
                ]
            );

        $professor = $this->professorRepository
            ->allTeachers(
                [
                    'active' => 1
                ]
            );

        $monthlyfees = $this->mensalidadeRepository->allMonthlyfeesGraph();

        $total_monthly = $this->sumMonthlyFees($monthlyfees);

        $late_monthly = $this->sumMonthlyFees($monthlyfees, 'atrasado');

        $canceled_monthly = $this->sumMonthlyFees($monthlyfees, 'cancelado');

        $paid_monthly = $this->sumMonthlyFees($monthlyfees, 'pago');

        $pending_monthly = $this->sumMonthlyFees($monthlyfees, 'pendente');

        $percentual_pending = $this->calculatePercentage($pending_monthly, $total_monthly);
        $percentual_late = $this->calculatePercentage($late_monthly, $total_monthly);
        $percentual_paid = $this->calculatePercentage($paid_monthly, $total_monthly);
        $percentual_canceled = $this->calculatePercentage($canceled_monthly, $total_monthly);

        $total_monthly -= $canceled_monthly;

        return $this->router->view(
            'dashboard/index',
            [
                'active' => 'dashboard',
                'percentual_pending' => $percentual_pending,
                'pending_monthly' => $pending_monthly,
                'percentual_late' => $percentual_late,
                'late_monthly' => $late_monthly,
                'percentual_paid' => $percentual_paid,
                'paid_monthly' => $paid_monthly,
                'percentual_canceled' => $percentual_canceled,
                'canceled_monthly' => $canceled_monthly,
                'total_monthly' => $total_monthly,
                'estudantes' => $estudantes,
                'discipline' => $discipline,
                'teachers' => $professor,
                'turmas' => $turmas,
            ]
        );
    }

    private function indexCoordenators()
    {
        // Buscar estudantes únicos ativos
        $estudantes = $this->estudanteRepository
            ->allStudents(
                [
                    'active' => 1
                ]
            );

        $discipline = $this->disciplinaRepository
            ->allDisciplines(
                [
                    'active' => 1
                ]
            );

        $turmas = $this->turmaRepository
            ->allClassRooms(
                [
                    'active' => 1
                ]
            );

        $professor = $this->professorRepository
            ->allTeachers(
                [
                    'active' => 1
                ]
            );

        return $this->router->view(
            'dashboard/index',
            [
                'active' => 'dashboard',
                'estudantes' => $estudantes,
                'discipline' => $discipline,
                'teachers' => $professor,
                'turmas' => $turmas,
            ]
        );
    }

    private function indexTeacher()
    {
        $pessoaAuth = $this->authUser();
        $professor = $this->professorRepository
            ->teacherWithPersonByID($pessoaAuth->id);

        $discipline = $this->disciplinaRepository
            ->allDisciplines(
                [
                    'active' => 1,
                    'teacher_id' => $professor["id"]
                ]
            );

        $turmas = $this->turmaRepository
            ->allClassroomsByTeacherID($professor["id"]);

        return $this->router->view(
            'dashboard/index',
            [
                'active' => 'dashboard',
                'discipline' => $discipline,
                'turmas' => $turmas,
            ]
        );
    }

    /**
     * Retorna dados do gráfico de alunos reprovados por disciplina
     * IMPORTANTE: Usa studentToFailed() para considerar recuperação
     */
    public function getFailedStudentsByDiscipline(Request $request)
    {
        header('Content-Type: application/json');
        ob_clean();

        try {
            $turmaId = $request->getParam('turma_id');
            $anoLetivo = $request->getParam('ano_letivo') ?? date('Y');

            if (!$turmaId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID da turma é obrigatório'
                ]);
                exit;
            }

            // Usar studentToFailed para obter alunos reprovados com recuperação
            $alunosReprovados = $this->recuperacaoRepository->studentToFailed([
                'periodOne' => 1,
                'periodTwo' => 4,
                'class_room' => $turmaId,
                'total' => 27.6
            ]);

            // Agrupar por disciplina
            $disciplinasMap = [];

            if (!empty($alunosReprovados) && is_array($alunosReprovados)) {
                foreach ($alunosReprovados as $aluno) {
                    $disciplinasReprovadas = is_object($aluno)
                        ? ($aluno->disciplinas_reprovadas ?? '')
                        : ($aluno['disciplinas_reprovadas'] ?? '');

                    $estudanteTurmaId = is_object($aluno)
                        ? ($aluno->estudante_turma_id ?? null)
                        : ($aluno['estudante_turma_id'] ?? null);

                    if (empty($disciplinasReprovadas) || !$estudanteTurmaId) {
                        continue;
                    }

                    // Separar disciplinas (GROUP_CONCAT retorna string separada por vírgula)
                    $disciplinas = array_map('trim', explode(',', $disciplinasReprovadas));

                    foreach ($disciplinas as $disciplinaNome) {
                        if (empty($disciplinaNome)) {
                            continue;
                        }

                        if (!isset($disciplinasMap[$disciplinaNome])) {
                            $disciplinasMap[$disciplinaNome] = [
                                'disciplina_nome' => $disciplinaNome,
                                'alunos_unicos' => []
                            ];
                        }

                        // Contar apenas se o aluno não foi contado ainda nesta disciplina
                        if (!in_array($estudanteTurmaId, $disciplinasMap[$disciplinaNome]['alunos_unicos'])) {
                            $disciplinasMap[$disciplinaNome]['alunos_unicos'][] = $estudanteTurmaId;
                        }
                    }
                }
            }

            // Formatar resultado
            $data = [];
            $chartData = [
                'categories' => [],
                'series' => []
            ];

            foreach ($disciplinasMap as $disciplina) {
                $totalReprovados = count($disciplina['alunos_unicos']);
                $data[] = [
                    'disciplina_nome' => $disciplina['disciplina_nome'],
                    'total_reprovados' => $totalReprovados
                ];
                $chartData['categories'][] = $disciplina['disciplina_nome'];
                $chartData['series'][] = $totalReprovados;
            }

            // Ordenar por quantidade de reprovados
            usort($data, function ($a, $b) {
                return $b['total_reprovados'] - $a['total_reprovados'];
            });

            if (empty($data)) {
                $chartData['categories'] = ['Nenhuma reprovação'];
                $chartData['series'] = [0];
            }

            echo json_encode([
                'success' => true,
                'data' => $data,
                'chart_data' => $chartData,
                'title' => 'Alunos em Risco por Disciplina (com Recuperação)'
            ]);
        } catch (\Exception $e) {
            error_log('Erro em getFailedStudentsByDiscipline: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar dados: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Retorna as turmas disponíveis para o coordenador
     */
    public function getCoordinatorClasses(Request $request)
    {
        try {
            $result = $this->lowScoresRepository->getClassesForCoordinator();

            if (!$result['success']) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $result['message'] ?? 'Erro desconhecido'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $result['data']
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ]);
        }
    }

    /**
     * Retorna estatísticas para o dashboard do professor
     */
    public function getTeacherStats(Request $request)
    {
        header('Content-Type: application/json');
        ob_clean();

        try {
            $pessoaAuth = $this->authUser();

            if (!$pessoaAuth) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ]);
                return;
            }

            $professor = $this->professorRepository->teacherWithPersonByID($pessoaAuth->id);

            if (!$professor || (is_array($professor) && empty($professor))) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Professor não encontrado'
                ]);
                return;
            }

            // Buscar turmas e disciplinas do professor
            $professorId = is_array($professor) ? ($professor['id'] ?? null) : $professor->id;

            if (!$professorId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID do professor não encontrado'
                ]);
                exit;
            }

            $turmasDisciplinas = $this->turmaDisciplinaRepository->classDisciplinesByTeacherId($professorId);

            // Filtrar por ano letivo atual
            if (is_array($turmasDisciplinas)) {
                $turmasDisciplinas = array_filter($turmasDisciplinas, function ($td) {
                    $anoLetivo = is_object($td) ? ($td->ano_letivo ?? null) : ($td['ano_letivo'] ?? null);
                    return $anoLetivo == date('Y');
                });
            }

            $totalDisciplinas = is_array($turmasDisciplinas) ? count($turmasDisciplinas) : 0;

            // Calcular total de alunos únicos
            $alunosUnicos = [];
            $totalFrequencias = 0;
            $totalPresencas = 0;

            if (is_array($turmasDisciplinas)) {
                foreach ($turmasDisciplinas as $td) {
                    $turmaId = is_object($td) ? $td->turma_id : ($td['turma_id'] ?? null);

                    if (!$turmaId) continue;

                    $estudantesTurma = $this->estudanteTurmaRepository->allClassStudents([
                        'classroom_id' => $turmaId,
                        'active' => 1,
                        'school_year' => date('Y')
                    ]);

                    if (is_array($estudantesTurma)) {
                        foreach ($estudantesTurma as $et) {
                            $estudanteId = is_object($et) ? $et->estudante_id : ($et['estudante_id'] ?? null);
                            $etId = is_object($et) ? $et->id : ($et['id'] ?? null);

                            if ($estudanteId) {
                                $alunosUnicos[$estudanteId] = true;
                            }

                            // Buscar frequências do aluno nesta disciplina
                            $tdId = is_object($td) ? $td->id : ($td['id'] ?? null);

                            if ($tdId && $etId) {
                                $frequencias = $this->frequenciaRepository->allFrequencies([
                                    'class_discipline_id' => $tdId,
                                    'student_id' => $etId
                                ]);

                                if (is_array($frequencias)) {
                                    foreach ($frequencias as $freq) {
                                        $totalFrequencias++;
                                        $faltas = is_object($freq) ? ($freq->faltas ?? 1) : ($freq['faltas'] ?? 1);
                                        if ($faltas == 0) {
                                            $totalPresencas++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $totalAlunos = count($alunosUnicos);

            // Calcular frequência média
            $frequenciaMedia = 0;
            if ($totalFrequencias > 0) {
                $frequenciaMedia = round(($totalPresencas / $totalFrequencias) * 100, 1);
            }

            // Buscar aulas do mês atual (contando registros de frequência)
            $aulasMinistradas = 0;
            if (is_array($turmasDisciplinas)) {
                foreach ($turmasDisciplinas as $td) {
                    $tdId = is_object($td) ? $td->id : ($td['id'] ?? null);

                    if (!$tdId) continue;

                    $freqMes = $this->frequenciaRepository->allFrequencies([
                        'class_discipline_id' => $tdId
                    ]);

                    if (is_array($freqMes)) {
                        $datasUnicas = [];
                        foreach ($freqMes as $f) {
                            $data = is_object($f) ? ($f->data ?? null) : ($f['data'] ?? null);

                            if (
                                $data && date('m', strtotime($data)) == date('m') &&
                                date('Y', strtotime($data)) == date('Y')
                            ) {
                                $datasUnicas[$data] = true;
                            }
                        }
                        $aulasMinistradas += count($datasUnicas);
                    }
                }
            }

            $turmasNomes = [];
            if (is_array($turmasDisciplinas)) {
                foreach ($turmasDisciplinas as $td) {
                    $turma = is_object($td) ? ($td->turma ?? null) : ($td['turma'] ?? null);
                    if ($turma) {
                        $turmaObj = getJsonToObject($turma);
                        if ($turmaObj && isset($turmaObj->nome) && !empty($turmaObj->nome)) {
                            $turmasNomes[] = $turmaObj->nome;
                        }
                    }
                }
            }
            $turmasNomes = array_values(array_unique($turmasNomes));

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_disciplinas' => $totalDisciplinas,
                    'total_alunos' => $totalAlunos,
                    'aulas_ministradas_mes' => $aulasMinistradas,
                    'frequencia_media' => $frequenciaMedia,
                    'turmas' => $turmasNomes
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Retorna estatísticas para o dashboard do estudante
     */
    public function getStudentStats(Request $request)
    {
        header('Content-Type: application/json');
        ob_clean();

        try {
            $pessoaAuth = $this->authUser();

            if (!$pessoaAuth) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ]);
                exit;
            }

            $estudante = $this->estudanteRepository->studentByPersonId($pessoaAuth->id);

            if (!$estudante) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Estudante não encontrado'
                ]);
                exit;
            }

            $estudanteTurma = $this->estudanteTurmaRepository->studentClassByStudentId($estudante->id);

            if (!$estudanteTurma) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'media_geral' => 0,
                        'frequencia_percentual' => 0,
                        'situacao' => 'Sem turma',
                        'total_disciplinas' => 0,
                        'disciplinas_aprovadas' => 0,
                        'disciplinas_recuperacao' => 0,
                        'total_faltas' => 0
                    ]
                ]);
                exit;
            }

            // Buscar todas as notas do estudante
            $notas = $this->boletimRepository->totalScoreByStudentsAndDisciplines([
                'student_class_id' => $estudanteTurma->id,
            ]);

            $mediaGeral = 0;
            $disciplinasAprovadas = 0;
            $disciplinasRecuperacao = 0;
            $totalDisciplinas = count($notas);

            if ($totalDisciplinas > 0) {
                $somaMedias = 0;
                foreach ($notas as $nota) {
                    $total = $nota->total ?? 0;
                    $somaMedias += $total;

                    if ($total >= 27.6) {
                        $disciplinasAprovadas++;
                    } elseif ($total >= 15 && $total < 27.6) {
                        $disciplinasRecuperacao++;
                    }
                }
                $mediaGeral = round($somaMedias / $totalDisciplinas, 1);
            }

            // Calcular frequência
            $frequencias = $this->frequenciaRepository->allFrequencies([
                'student_id' => $estudanteTurma->id,
                'class_id' => $estudanteTurma->turma_id
            ]);

            $totalFaltas = $this->sumAbsences($frequencias ?? []);
            $cargaHoraria = 80; // Valor padrão

            if (!empty($frequencias)) {
                $classDiscipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);
                $cargaHorariaObj = $this->cargaHorariaRepository->findById($classDiscipline->id);
                $cargaHoraria = $cargaHorariaObj->carga ?? 80;
            }

            $presencaPercentual = round((($cargaHoraria - $totalFaltas) / $cargaHoraria) * 100, 1);

            $situacao = 'Regular';
            if ($mediaGeral >= 27.6 && $presencaPercentual >= 75) {
                $situacao = 'Aprovado';
            } elseif ($mediaGeral < 15 || $presencaPercentual < 75) {
                $situacao = 'Reprovado';
            } elseif ($mediaGeral >= 15 && $mediaGeral < 27.6) {
                $situacao = 'Recuperação';
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'media_geral' => $mediaGeral,
                    'frequencia_percentual' => $presencaPercentual,
                    'situacao' => $situacao,
                    'total_disciplinas' => $totalDisciplinas,
                    'disciplinas_aprovadas' => $disciplinasAprovadas,
                    'disciplinas_recuperacao' => $disciplinasRecuperacao,
                    'total_faltas' => $totalFaltas
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ]);
        }
        exit;
    }



    public function getCoordinatorStatsByUser(Request $request)
    {
        header('Content-Type: application/json');

        // Limpar qualquer output anterior
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        try {
            $coordenadorId = $this->getAuthenticatedCoordinatorId();

            if (!$coordenadorId) {
                ob_end_clean();
                $this->sendJsonResponse(false, 'Coordenador não encontrado');
                exit;
            }

            $turmasIds = $this->getCoordinatorClassIds($coordenadorId);

            if (empty($turmasIds)) {
                ob_end_clean();
                $this->sendEmptyCoordinatorStats($coordenadorId);
                exit;
            }

            // Métodos especializados seguindo critérios do RecuperacaoController
            $totalAlunos = $this->getTotalStudents($turmasIds);
            $alunosEmRisco = $this->getStudentsAtRisk($turmasIds);
            $alunosAprovados = $this->getApprovedStudents($totalAlunos, $alunosEmRisco);
            $indiceAprovacao = $this->calculateApprovalIndex($alunosAprovados, $totalAlunos);
            $componentesRisco = $this->getComponentsWithStudentsAtRisk($turmasIds);

            ob_end_clean();

            $this->sendJsonResponse(true, null, [
                'total_turmas' => count($turmasIds),
                'total_alunos' => $totalAlunos,
                'indice_aprovacao' => $indiceAprovacao,
                'alunos_aprovados' => $alunosAprovados,
                'alunos_risco' => $alunosEmRisco,
                'total_componentes_com_reprovados' => $componentesRisco['total_componentes'],
                'disciplinas_risco' => $componentesRisco['lista'],
                'disciplinas_risco_count' => count($componentesRisco['lista'])
            ]);
        } catch (\Exception $e) {
            ob_end_clean();
            error_log('Erro em getCoordinatorStatsByUser: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            $this->sendJsonResponse(false, 'Erro ao carregar estatísticas: ' . $e->getMessage());
        }
        exit;
    }

    /**
     * Obtém o ID do coordenador autenticado
     */
    private function getAuthenticatedCoordinatorId(): ?int
    {
        $pessoaAuth = $this->authUser();

        if (!$pessoaAuth) {
            return null;
        }

        $coordenadores = $this->coordenadorRepository->allCoordinators([
            'pessoa_fisica_id' => $pessoaAuth->id,
            'situation' => 1
        ]);

        if (empty($coordenadores)) {
            return null;
        }

        $coordenador = is_array($coordenadores) ? $coordenadores[0] : $coordenadores;
        return is_object($coordenador) ? $coordenador->id : ($coordenador['id'] ?? null);
    }

    /**
     * Obtém os IDs das turmas do coordenador
     */
    private function getCoordinatorClassIds(int $coordenadorId): array
    {
        $coordenadorTurmas = $this->coordenadorTurmaRepository->allCoordinatorClass([
            'coordenador_id' => $coordenadorId
        ]);

        if (empty($coordenadorTurmas) || !is_array($coordenadorTurmas)) {
            return [];
        }

        $turmasIds = [];
        foreach ($coordenadorTurmas as $ct) {
            $turmaId = is_object($ct) ? $ct->turma_id : ($ct['turma_id'] ?? null);
            if ($turmaId) {
                $turmasIds[] = $turmaId;
            }
        }

        return array_unique($turmasIds);
    }

    /**
     * Busca estudantes ativos das turmas especificadas
     * Retorna array com ID único do estudante
     */
    private function getStudentsFromClasses(array $turmasIds): array
    {
        $estudantesUnicos = [];

        foreach ($turmasIds as $turmaId) {
            $estudantesTurma = $this->estudanteTurmaRepository->allClassStudents([
                'class_id' => $turmaId,
                'active' => 1,
                'school_year' => date('Y')
            ]);

            if (!is_array($estudantesTurma)) {
                continue;
            }

            foreach ($estudantesTurma as $et) {
                $estudanteId = is_object($et) ? $et->estudante_id : ($et['estudante_id'] ?? null);

                if ($estudanteId && !isset($estudantesUnicos[$estudanteId])) {
                    $estudantesUnicos[$estudanteId] = true;
                }
            }
        }

        return array_keys($estudantesUnicos);
    }

    /**
     * Retorna o total de estudantes únicos nas turmas
     */
    private function getTotalStudents(array $turmasIds): int
    {
        return count($this->getStudentsFromClasses($turmasIds));
    }

    /**
     * Retorna quantidade de estudantes em risco de reprovação
     * Critérios: periodOne=1, periodTwo=4, total=27.6 (mesmos do RecuperacaoController)
     * Estudante em risco = reprovado em pelo menos uma disciplina
     */
    private function getStudentsAtRisk(array $turmasIds): int
    {
        $alunosEmRiscoUnicos = [];

        foreach ($turmasIds as $turmaId) {
            $reprovados = $this->recuperacaoRepository->studentToFailed([
                'periodOne' => 1,
                'periodTwo' => 4,
                'class_room' => $turmaId,
                'total' => 27.6
            ]);

            if (!empty($reprovados) && is_array($reprovados)) {
                foreach ($reprovados as $reprovado) {
                    $estudanteId = is_object($reprovado)
                        ? ($reprovado->estudante_id ?? null)
                        : ($reprovado['estudante_id'] ?? null);

                    if ($estudanteId) {
                        $alunosEmRiscoUnicos[$estudanteId] = true;
                    }
                }
            }
        }

        return count($alunosEmRiscoUnicos);
    }

    /**
     * Retorna quantidade de estudantes aprovados
     */
    private function getApprovedStudents(int $totalAlunos, int $alunosEmRisco): int
    {
        return max(0, $totalAlunos - $alunosEmRisco);
    }

    /**
     * Calcula índice de aprovação em percentual
     */
    private function calculateApprovalIndex(int $alunosAprovados, int $totalAlunos): float
    {
        if ($totalAlunos <= 0) {
            return 0.0;
        }

        return round(($alunosAprovados / $totalAlunos) * 100, 1);
    }

    /**
     * Busca componentes curriculares com estudantes em risco de reprovação
     * Critérios: periodOne=1, periodTwo=4, total=27.6 (mesmos do RecuperacaoController)
     * 
     * Retorna lista das top 5 disciplinas com maior número de estudantes reprovados
     * e o total de componentes com pelo menos um estudante reprovado
     */
    private function getComponentsWithStudentsAtRisk(array $turmasIds): array
    {
        $estudantesEmRiscoGlobal = [];
        $disciplinasMap = [];

        LoggerHelper::logInfo("=== INÍCIO DEBUG COMPONENTES EM RISCO ===");
        LoggerHelper::logInfo("Total de turmas: " . count($turmasIds));

        foreach ($turmasIds as $turmaId) {
            $reprovados = $this->recuperacaoRepository->studentToFailed([
                'periodOne' => 1,
                'periodTwo' => 4,
                'class_room' => $turmaId,
                'total' => 27.6
            ]);

            LoggerHelper::logInfo("Turma {$turmaId}: " . count($reprovados) . " registros retornados");

            if (!empty($reprovados) && is_array($reprovados)) {
                foreach ($reprovados as $idx => $reprovado) {
                    $estudanteId = is_object($reprovado)
                        ? ($reprovado->estudante_id ?? null)
                        : ($reprovado['estudante_id'] ?? null);

                    $disciplinasReprovadas = is_object($reprovado)
                        ? ($reprovado->disciplinas_reprovadas ?? '')
                        : ($reprovado['disciplinas_reprovadas'] ?? '');

                    if ($idx < 3) { // Log dos primeiros 3 registros de cada turma
                        LoggerHelper::logInfo("  [{$idx}] Estudante ID: {$estudanteId}, Disciplinas: {$disciplinasReprovadas}");
                    }

                    if (!$estudanteId || empty($disciplinasReprovadas)) {
                        continue;
                    }

                    $estudantesEmRiscoGlobal[$estudanteId] = true;

                    $disciplinas = array_map('trim', explode(',', $disciplinasReprovadas));

                    foreach ($disciplinas as $disciplinaNome) {
                        if (empty($disciplinaNome)) {
                            continue;
                        }

                        // Remover texto entre parênteses se existir
                        $disciplinaNome = preg_replace('/\s*\([^)]*\)/', '', $disciplinaNome);
                        $disciplinaNome = trim($disciplinaNome);

                        if (!isset($disciplinasMap[$disciplinaNome])) {
                            $disciplinasMap[$disciplinaNome] = [];
                        }

                        $disciplinasMap[$disciplinaNome][$estudanteId] = true;
                    }
                }
            }
        }

        LoggerHelper::logInfo("Total de estudantes únicos em risco: " . count($estudantesEmRiscoGlobal));
        LoggerHelper::logInfo("Total de disciplinas diferentes: " . count($disciplinasMap));

        $disciplinasRisco = [];
        foreach ($disciplinasMap as $disciplina => $estudantes) {
            $qtd = count($estudantes);
            LoggerHelper::logInfo("  {$disciplina}: {$qtd} estudantes");

            $disciplinasRisco[] = [
                'disciplina' => $disciplina,
                'turma' => '',
                'quantidade_reprovados' => $qtd
            ];
        }

        LoggerHelper::logInfo("=== FIM DEBUG COMPONENTES EM RISCO ===");

        usort($disciplinasRisco, function ($a, $b) {
            return $b['quantidade_reprovados'] - $a['quantidade_reprovados'];
        });

        return [
            'lista' => array_slice($disciplinasRisco, 0, 5),
            'total_componentes' => count($disciplinasRisco)
        ];
    }

    /**
     * Formata e ordena as disciplinas com reprovação
     * Retorna array com 'lista' (top 5) e 'total_componentes'
     */
    /**
     * Envia resposta JSON padronizada
     */
    private function sendJsonResponse(bool $success, ?string $message = null, ?array $data = null): void
    {
        $response = ['success' => $success];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
    }

    /**
     * Envia resposta vazia para coordenador sem turmas
     */
    private function sendEmptyCoordinatorStats(int $coordenadorId): void
    {
        $this->sendJsonResponse(true, null, [
            'total_turmas' => 0,
            'total_alunos' => 0,
            'indice_aprovacao' => 0,
            'alunos_aprovados' => 0,
            'alunos_risco' => 0,
            'total_componentes_com_reprovados' => 0,
            'disciplinas_risco' => []
        ]);
    }

    /**
     * Retorna estatísticas do professor baseado em suas disciplinas específicas
     */
    public function getTeacherStatsByUser(Request $request)
    {
        header('Content-Type: application/json');
        ob_clean();

        try {
            $pessoaAuth = $this->authUser();

            if (!$pessoaAuth) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ]);
                exit;
            }

            $professor = $this->professorRepository->teacherWithPersonByID($pessoaAuth->id);

            if (!$professor || (is_array($professor) && empty($professor))) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Professor não encontrado'
                ]);
                exit;
            }

            $professorId = is_array($professor) ? ($professor['id'] ?? null) : $professor->id;

            if (!$professorId) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID do professor não encontrado'
                ]);
                exit;
            }

            // Buscar apenas as turmas/disciplinas do professor logado
            $turmasDisciplinas = $this->turmaDisciplinaRepository->classDisciplinesByTeacherId($professorId);

            if (is_array($turmasDisciplinas)) {
                $turmasDisciplinas = array_filter($turmasDisciplinas, function ($td) {
                    $anoLetivo = is_object($td) ? ($td->ano_letivo ?? null) : ($td['ano_letivo'] ?? null);
                    return $anoLetivo == date('Y');
                });
            }

            $totalDisciplinas = is_array($turmasDisciplinas) ? count($turmasDisciplinas) : 0;

            // Calcular alunos únicos apenas das disciplinas do professor
            $alunosUnicos = [];
            $totalFrequencias = 0;
            $totalPresencas = 0;

            if (is_array($turmasDisciplinas)) {
                foreach ($turmasDisciplinas as $td) {
                    $turmaId = is_object($td) ? $td->turma_id : ($td['turma_id'] ?? null);

                    if (!$turmaId) continue;

                    $estudantesTurma = $this->estudanteTurmaRepository->allClassStudents([
                        'classroom_id' => $turmaId,
                        'active' => 1,
                        'school_year' => date('Y')
                    ]);

                    if (is_array($estudantesTurma)) {
                        foreach ($estudantesTurma as $et) {
                            $estudanteId = is_object($et) ? $et->estudante_id : ($et['estudante_id'] ?? null);
                            $etId = is_object($et) ? $et->id : ($et['id'] ?? null);

                            if ($estudanteId) {
                                $alunosUnicos[$estudanteId] = true;
                            }

                            $tdId = is_object($td) ? $td->id : ($td['id'] ?? null);

                            if ($tdId && $etId) {
                                $frequencias = $this->frequenciaRepository->allFrequencies([
                                    'class_discipline_id' => $tdId,
                                    'student_id' => $etId
                                ]);

                                if (is_array($frequencias)) {
                                    foreach ($frequencias as $freq) {
                                        $totalFrequencias++;
                                        $faltas = is_object($freq) ? ($freq->faltas ?? 1) : ($freq['faltas'] ?? 1);
                                        if ($faltas == 0) {
                                            $totalPresencas++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $totalAlunos = count($alunosUnicos);
            $frequenciaMedia = $totalFrequencias > 0 ? round(($totalPresencas / $totalFrequencias) * 100, 1) : 0;

            // Calcular aulas ministradas do mês atual
            $aulasMinistradas = 0;
            if (is_array($turmasDisciplinas)) {
                foreach ($turmasDisciplinas as $td) {
                    $tdId = is_object($td) ? $td->id : ($td['id'] ?? null);

                    if (!$tdId) continue;

                    $freqMes = $this->frequenciaRepository->allFrequencies([
                        'class_discipline_id' => $tdId
                    ]);

                    if (is_array($freqMes)) {
                        $datasUnicas = [];
                        foreach ($freqMes as $f) {
                            $data = is_object($f) ? ($f->data ?? null) : ($f['data'] ?? null);

                            if (
                                $data && date('m', strtotime($data)) == date('m') &&
                                date('Y', strtotime($data)) == date('Y')
                            ) {
                                $datasUnicas[$data] = true;
                            }
                        }
                        $aulasMinistradas += count($datasUnicas);
                    }
                }
            }

            // Montar lista de turmas com detalhes
            $turmas = [];
            if (is_array($turmasDisciplinas)) {
                foreach ($turmasDisciplinas as $td) {
                    $turmaId = is_object($td) ? $td->turma_id : ($td['turma_id'] ?? null);

                    if (!$turmaId) continue;

                    $turma = $this->turmaRepository->findById($turmaId);

                    if ($turma) {
                        $estudantesTurma = $this->estudanteTurmaRepository->allClassStudents([
                            'classroom_id' => $turmaId,
                            'active' => 1,
                            'school_year' => date('Y')
                        ]);

                        $turmas[] = [
                            'id' => is_object($turma) ? $turma->id : ($turma['id'] ?? null),
                            'nome' => is_object($turma) ? $turma->nome : ($turma['nome'] ?? ''),
                            'turno' => is_object($turma) ? $turma->turno : ($turma['turno'] ?? ''),
                            'total_estudantes' => count($estudantesTurma)
                        ];
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'total_disciplinas' => $totalDisciplinas,
                    'total_alunos' => $totalAlunos,
                    'aulas_ministradas_mes' => $aulasMinistradas,
                    'frequencia_media' => $frequenciaMedia,
                    'turmas' => $turmas
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Retorna estatísticas do estudante baseado em suas matrículas
     */
    public function getStudentStatsByUser(Request $request)
    {
        header('Content-Type: application/json');
        ob_clean();

        try {
            $pessoaAuth = $this->authUser();

            if (!$pessoaAuth) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ]);
                exit;
            }

            $estudante = $this->estudanteRepository->studentByPersonId($pessoaAuth->id);

            if (!$estudante) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Estudante não encontrado'
                ]);
                exit;
            }

            // Buscar turma ativa do estudante no ano atual
            $estudanteTurma = $this->estudanteTurmaRepository->studentClassByStudentId($estudante->id);

            if (!$estudanteTurma) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'media_geral' => 0,
                        'frequencia_percentual' => 0,
                        'situacao' => 'Sem turma',
                        'total_disciplinas' => 0,
                        'disciplinas_aprovadas' => 0,
                        'disciplinas_recuperacao' => 0,
                        'total_faltas' => 0
                    ]
                ]);
                exit;
            }

            // Buscar notas do estudante
            $notas = $this->boletimRepository->totalScoreByStudentsAndDisciplines([
                'student_class_id' => $estudanteTurma->id,
            ]);

            $mediaGeral = 0;
            $disciplinasAprovadas = 0;
            $disciplinasRecuperacao = 0;
            $totalDisciplinas = count($notas);

            if ($totalDisciplinas > 0) {
                $somaMedias = 0;
                foreach ($notas as $nota) {
                    $total = is_object($nota) ? ($nota->total ?? 0) : ($nota['total'] ?? 0);
                    $somaMedias += $total;

                    if ($total >= 27.6) {
                        $disciplinasAprovadas++;
                    } elseif ($total >= 15 && $total < 27.6) {
                        $disciplinasRecuperacao++;
                    }
                }
                $mediaGeral = round($somaMedias / $totalDisciplinas, 1);
            }

            // Calcular frequência
            $frequencias = $this->frequenciaRepository->allFrequencies([
                'student_id' => $estudanteTurma->id,
                'class_id' => $estudanteTurma->turma_id
            ]);

            $totalFaltas = $this->sumAbsences($frequencias ?? []);
            $cargaHoraria = 80;

            if (!empty($frequencias)) {
                $classDiscipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);
                if ($classDiscipline) {
                    $cargaHorariaObj = $this->cargaHorariaRepository->findById($classDiscipline->id);
                    $cargaHoraria = is_object($cargaHorariaObj) ? ($cargaHorariaObj->carga ?? 80) : 80;
                }
            }

            $presencaPercentual = round((($cargaHoraria - $totalFaltas) / $cargaHoraria) * 100, 1);

            // Determinar situação
            $situacao = 'Regular';
            if ($mediaGeral >= 27.6 && $presencaPercentual >= 75) {
                $situacao = 'Aprovado';
            } elseif ($mediaGeral < 15 || $presencaPercentual < 75) {
                $situacao = 'Reprovado';
            } elseif ($mediaGeral >= 15 && $mediaGeral < 27.6) {
                $situacao = 'Recuperação';
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'media_geral' => $mediaGeral,
                    'frequencia_percentual' => $presencaPercentual,
                    'situacao' => $situacao,
                    'total_disciplinas' => $totalDisciplinas,
                    'disciplinas_aprovadas' => $disciplinasAprovadas,
                    'disciplinas_recuperacao' => $disciplinasRecuperacao,
                    'total_faltas' => $totalFaltas
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
