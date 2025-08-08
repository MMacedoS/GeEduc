<?php

namespace App\Controllers\v1\Recuperation;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Recuperation\IRecuperacaoRepository;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Request\Request;
use App\Utils\Paginator;

class RecuperacaoController extends Controller
{
    protected $turmaDisciplinaRepository;
    protected $professorDisciplinaRepository;
    protected $recuperacaoRepository;
    protected $turmaRepository;
    protected $periodoRepository;

    public function __construct(
        IRecuperacaoRepository $recuperacaoRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IProfessorDisciplinaRepository $professorDisciplinaRepository,
        IPeriodoRepository $periodoRepository,
        ITurmaRepository $turmaRepository
    ) {
        parent::__construct();
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->recuperacaoRepository = $recuperacaoRepository;
        $this->professorDisciplinaRepository = $professorDisciplinaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->periodoRepository = $periodoRepository;
    }

    public function index(Request $request, string $class_id, string $class_discipline_id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);

        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
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
                    'turma_disciplina_id' => $class_disciplines[0]->id
                ]
            );

        $semester_two = $this->recuperacaoRepository
            ->studentByScoreLow(
                [
                    'periodoOne' => 3,
                    'periodoTwo' => 4,
                    'type' => 'II Semestre',
                    'total' => 13.8
                ]
            );

        $final = $this->recuperacaoRepository
            ->studentByScoreLow(
                [
                    'periodoOne' => 1,
                    'periodoTwo' => 4,
                    'type' => 'Exames Finais',
                    'total' => 27.6
                ]
            );

        return $this->router->view(
            "teacher/my-disciplines/recuperation/index",
            [
                'active' => $this->active,
                'turmas_disciplinas' => $class_disciplines,
                'turma' => $classRooms,
                'semester_1' => $semester_one,
                'semester_2' => $semester_two,
                'final' => $final,
                'periodos' => $periodos
            ]
        );
    }

    public function indexByCoordenator(Request $request, string $class_id)
    {
        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        $classRooms = $this->turmaRepository->findByUuid($class_id);

        if (is_null($classRooms)) {
            return $this->router->redirect("minha-coordenacao/");
        }

        $semester_one = $this->recuperacaoRepository
            ->studentToFailed(
                [
                    'periodOne' => 1,
                    'periodTwo' => 2,
                    'class_room' => $classRooms->id,
                    'total' => 13.9
                ]
            );

        $semester_two = $this->recuperacaoRepository
            ->studentToFailed(
                [
                    'periodoOne' => 3,
                    'periodoTwo' => 4,
                    'class_room' => $classRooms->id,
                    'total' => 13.9
                ]
            );

        $final = $this->recuperacaoRepository
            ->studentToFailed(
                [
                    'periodoOne' => 1,
                    'periodoTwo' => 4,
                    'class_room' => $classRooms->id,
                    'total' => 27.6
                ]
            );

        return $this->router->view(
            "/coordination/my-coordination/classroom/recuperations",
            [
                'active' => $this->active,
                'turma' => $classRooms,
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
}
