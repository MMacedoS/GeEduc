<?php

namespace App\Controllers\v1\Recuperation;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Recuperation\IRecuperacaoRepository;
use App\Interfaces\Scores\INotaFinalRepository;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaDisciplinaTransformer;
use App\Transformers\Classe\TurmaTransformer;
use App\Utils\Paginator;

class RecuperacaoController extends Controller
{
    protected $turmaDisciplinaRepository;
    protected $professorDisciplinaRepository;
    protected $recuperacaoRepository;
    protected $turmaRepository;
    protected $periodoRepository;
    protected $notaFinalRepository;
    protected $turmaDisciplinaTransformer;
    protected $turmaTransformer;

    public function __construct(
        IRecuperacaoRepository $recuperacaoRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IProfessorDisciplinaRepository $professorDisciplinaRepository,
        IPeriodoRepository $periodoRepository,
        ITurmaRepository $turmaRepository,
        INotaFinalRepository $notaFinalRepository,
        TurmaDisciplinaTransformer $turmaDisciplinaTransformer,
        TurmaTransformer $turmaTransformer
    ) {
        parent::__construct();
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->recuperacaoRepository = $recuperacaoRepository;
        $this->professorDisciplinaRepository = $professorDisciplinaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->periodoRepository = $periodoRepository;
        $this->notaFinalRepository = $notaFinalRepository;
        $this->turmaDisciplinaTransformer = $turmaDisciplinaTransformer;
        $this->turmaTransformer = $turmaTransformer;
    }

    public function index(Request $request, string $class_id, string $class_discipline_id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);

        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        $class_disciplines = $this->turmaDisciplinaRepository->findByUuid(
            $class_discipline_id
        );

        if (is_null($class_disciplines)) {
            return false;
        }

        $semester_one = $this->recuperacaoRepository
            ->studentsByTurmaDisciplinaAndScore(
                [
                    'periodoOne' => 1,
                    'periodoTwo' => 2,
                    'type' => 'I Semestre',
                    'total' => 13.8,
                    'turma_disciplina_id' => $class_disciplines->id
                ]
            );


        $semester_two = $this->recuperacaoRepository
            ->studentsByTurmaDisciplinaAndScore(
                [
                    'periodoOne' => 3,
                    'periodoTwo' => 4,
                    'type' => 'II Semestre',
                    'total' => 13.8,
                    'turma_disciplina_id' => $class_disciplines->id
                ]
            );

        $final = $this->recuperacaoRepository
            ->studentsByTurmaDisciplinaWithRecovery(
                [
                    'periodoOne' => 1,
                    'periodoTwo' => 4,
                    'type' => 'Exames Finais',
                    'total_minimo' => 27.6,
                    'total_aprovacao' => 27.9,
                    'turma_disciplina_id' => $class_disciplines->id
                ]
            );

        return $this->router->view(
            "teacher/my-disciplines/recuperation/index",
            [
                'active' => $this->active,
                'turmas_disciplinas' => $this->turmaDisciplinaTransformer->transform($class_disciplines),
                'turma' => (object)$this->turmaTransformer->transform($classRooms),
                'semester_1' => $semester_one,
                'semester_2' => $semester_two,
                'final' => $final,
                'periodos' => $periodos
            ]
        );
    }

    public function indexByCoordenator(Request $request, string $class_id)
    {
        $classRooms = $this->turmaRepository->findByUuid((string)$class_id);

        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        if (is_null($classRooms)) {
            return $this->router->redirect("minha-coordenacao/");
        }

        $semester_one = $this->recuperacaoRepository
            ->studentToFailed(
                [
                    'periodOne' => 1,
                    'periodTwo' => 2,
                    'class_room' => $classRooms->id,
                    'total' => 13.8
                ]
            );

        $semester_two = $this->recuperacaoRepository
            ->studentToFailed(
                [
                    'periodOne' => 3,
                    'periodTwo' => 4,
                    'class_room' => $classRooms->id,
                    'total' => 13.8
                ]
            );

        $final = $this->recuperacaoRepository
            ->studentToFailed(
                [
                    'periodOne' => 1,
                    'periodTwo' => 4,
                    'class_room' => $classRooms->id,
                    'total' => 27.6
                ]
            );

        return $this->router->view(
            "/coordination/my-coordination/classroom/recuperations",
            [
                'active' => $this->active,
                'turma' => (object)$this->turmaTransformer->transform($classRooms),
                'semester_1' => $semester_one,
                'semester_2' => $semester_two,
                'final' => $final,
                'periodos' => $periodos
            ]
        );
    }

    public function store(Request $request, string $class_id, string $class_discipline_id)
    {
        $data = $request->getBodyParams();

        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($class_discipline_id);

        if (is_null($turma_disciplina)) {
            return $this->router->redirect("$this->redirect?error=422");
        }

        $data['class_discipline_id'] = $turma_disciplina->id;

        $this->recuperacaoRepository->create($data);

        return $this->router->redirect("meus-componentes/turma/$class_id/disciplina/$class_discipline_id/recuperacoes");
    }

    public function storeFinalExam(Request $request, string $class_id, string $class_discipline_id)
    {
        $data = $request->getBodyParams();

        // Validação dos dados recebidos
        if (!isset($data['student_class_id']) || !isset($data['score'])) {
            return $this->router->redirect("meus-componentes/turma/$class_id/disciplina/$class_discipline_id/recuperacoes?danger=1");
        }

        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($class_discipline_id);

        if (is_null($turma_disciplina)) {
            return $this->router->redirect("meus-componentes/turma/$class_id/disciplina/$class_discipline_id/recuperacoes?danger=1");
        }

        // Validar nota (0 a 10)
        $nota = floatval($data['score']);
        if ($nota < 0 || $nota > 10) {
            return $this->router->redirect("meus-componentes/turma/$class_id/disciplina/$class_discipline_id/recuperacoes?danger=1");
        }

        // Buscar se já existe uma nota final para este estudante nesta disciplina
        $notaExistente = $this->notaFinalRepository->findByStudentAndDiscipline(
            $data['student_class_id'],
            $turma_disciplina->id
        );

        // Calcular a situação do estudante baseado na nota
        // Para calcular corretamente, precisamos da média total
        // Vamos assumir que se a nota for >= ao necessário, ele está aprovado
        $situacao = 'Reprovado';

        // Preparar dados para inserção/atualização
        $notaFinalData = [
            'turma_disciplina_id' => $turma_disciplina->id,
            'estudante_turma_id' => $data['student_class_id'],
            'nota' => $nota,
            'situacao' => $situacao, // Será atualizado com a lógica correta
            'ano_letivo' => date('Y')
        ];

        try {
            if ($notaExistente) {
                // Atualizar nota existente
                $this->notaFinalRepository->update($notaFinalData, $notaExistente->id);
            } else {
                // Criar nova nota final
                $this->notaFinalRepository->create($notaFinalData);
            }

            return $this->router->redirect("meus-componentes/turma/$class_id/disciplina/$class_discipline_id/recuperacoes?success=1");
        } catch (\Exception $e) {
            return $this->router->redirect("meus-componentes/turma/$class_id/disciplina/$class_discipline_id/recuperacoes?danger=1");
        }
    }
}
