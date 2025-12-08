<?php

namespace App\Controllers\v1\Frequencies;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Interfaces\Activitie\IAtividadeRepository;
use App\Interfaces\Calendar\IDiaLetivoRepository;
use App\Interfaces\Classrooms\IAulaRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Frequencies\IFrequenciaRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Interfaces\Work_Load\ICargaHorariaRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaDisciplinaTransformer;
use App\Utils\Paginator;

class FrequenciaController extends Controller
{
    use GenericTrait;

    const TEN = 10;
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $estudanteRepository;
    protected $frequenciaRepository;
    protected $estudanteTurmaRepository;
    protected $professorDisciplinaRepository;
    protected $periodoRepository;
    protected $cargaHorariaRepository;
    protected $aulaRepository;
    private $diaLetivoRepository;
    private $turmaDisciplinaTransformer;

    public function __construct(
        IAtividadeRepository $atividadeRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IEstudanteRepository $estudanteRepository,
        IFrequenciaRepository $frequenciaRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        IProfessorDisciplinaRepository $professorDisciplinaRepository,
        ICargaHorariaRepository $cargaHorariaRepository,
        IPeriodoRepository $periodoRepository,
        IAulaRepository $aulaRepository,
        IDiaLetivoRepository $diaLetivoRepository,
        TurmaDisciplinaTransformer $turmaDisciplinaTransformer
    ) {
        parent::__construct();
        $this->frequenciaRepository = $frequenciaRepository;
        $this->atividadeRepository = $atividadeRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->professorDisciplinaRepository = $professorDisciplinaRepository;
        $this->periodoRepository = $periodoRepository;
        $this->cargaHorariaRepository = $cargaHorariaRepository;
        $this->estudanteRepository = $estudanteRepository;
        $this->aulaRepository = $aulaRepository;
        $this->diaLetivoRepository = $diaLetivoRepository;
        $this->turmaDisciplinaTransformer = $turmaDisciplinaTransformer;
    }

    private function defineRoutes($class_discipline_id)
    {
        switch ($_SESSION["user"]->painel) {
            case 'coordenador':
                $this->routeView = 'coordination/my-coordination/discipline';
                $this->redirect = "minha-coordenacao/turma/$class_discipline_id";
                $this->active = 'coordinator';
                break;
            case 'administrativo':
                $this->routeView = 'coordination/my-coordination/discipline';
                $this->redirect = "minha-coordenacao/turma/$class_discipline_id";
                $this->active = 'coordinator';
                break;
            case 'professor':
                $this->routeView = 'teacher/my-disciplines';
                $this->redirect = "meus-componentes/$class_discipline_id";
                $this->active = 'teacher';
                break;
        }
    }

    public function indexStudents(Request $request, string $student_class_id)
    {
        $student_class = $this->estudanteTurmaRepository->findByUuid($student_class_id);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $student_class->id,
                    'class_id' => $student_class->turma_id
                ]
            );

        $class_discipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);

        $carga_horaria = $this->cargaHorariaRepository->findById($class_discipline->id);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($frequencias, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $total_faltas = $this->sumAbsences($frequencias);
        $carga = $carga_horaria->carga ?? 80;

        $presenca = $carga - $total_faltas;
        $percentual_faltas = round(($total_faltas / $carga) * 100, 2);
        $percentual_presenca = round(($presenca / $carga) * 100, 2);

        return $this->router->view(
            'student/my-classrooms/frequencies',
            [
                'active' => 'students',
                'estudante_turma' => $student_class,
                'frequencias' => $paginatedBoards,
                'percentual_faltas' => $percentual_faltas,
                'percentual_presenca' => $percentual_presenca,
                'total_faltas' => $total_faltas,
                'presenca' => $presenca,
                'links' => $paginator->links()
            ]
        );
    }

    public function indexResponsibleStudents(Request $request, string $student_id, string $student_class_id)
    {
        $student = $this->estudanteRepository
            ->studentWithPersonByUuid((string)$student_id);

        $student_class = $this->estudanteTurmaRepository->findByUuid($student_class_id);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $student_class->id,
                    'class_id' => $student_class->turma_id
                ]
            );

        $class_discipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);

        $carga_horaria = $this->cargaHorariaRepository->findById($class_discipline->id);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($frequencias, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $total_faltas = $this->sumAbsences($frequencias);
        $carga = $carga_horaria->carga ?? 80;

        $presenca = $carga - $total_faltas;
        $percentual_faltas = round(($total_faltas / $carga) * 100, 2);
        $percentual_presenca = round(($presenca / $carga) * 100, 2);

        return $this->router->view(
            '/my-little-group/student-class/frequencies',
            [
                'active' => 'students',
                'estudante' => $student,
                'estudante_turma' => $student_class,
                'frequencias' => $paginatedBoards,
                'percentual_faltas' => $percentual_faltas,
                'percentual_presenca' => $percentual_presenca,
                'total_faltas' => $total_faltas,
                'presenca' => $presenca,
                'links' => $paginator->links()
            ]
        );
    }

    public function indexTeacher(Request $request, string $class_discipline_id)
    {
        $this->defineRoutes($class_discipline_id);
        $paramsURL = $request->getQueryParams();

        $turma_disciplina = $this->turmaDisciplinaRepository
            ->findByUuid($class_discipline_id);

        $aulas = $this->aulaRepository->allClass(['classroom_discipline_id' => $turma_disciplina->id]);

        $dia = $this->diaLetivoRepository->firstDay(['year' => Date('Y')]);

        $estudantes = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'class_id' => $turma_disciplina->turma_id,
                    'school_year' => Date('Y')
                ]
            );

        $data_presence = Date('Y-m-d');

        if (isset($paramsURL['data']) && !empty($paramsURL['data'])) {
            $data_presence = $paramsURL['data'];
        }

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'class_discipline_id' => $turma_disciplina->id,
                    'class_id' => $turma_disciplina->turma_id,
                    'data_presence' => $data_presence,
                    'period_id' => $paramsURL['period_id'] ?? null
                ]
            );

        $periodos = $this->periodoRepository->all(['active' => '1']);

        return $this->router->view(
            "$this->routeView/frequency",
            [
                'active' => $this->active,
                'turma_disciplina' => $this->turmaDisciplinaTransformer->transform($turma_disciplina),
                'estudantes' => $estudantes,
                'frequencias' => $frequencias,
                'periodos' => $periodos,
                'aulas' => $this->extractWeekDays($aulas),
                'dia' => $dia,
                'dataFilter' => $data_presence,
                'bimestreFilter' => $paramsURL['period_id'] ?? null,
            ]
        );
    }

    public function store(Request $request, string $class_discipline_id)
    {
        $this->defineRoutes($class_discipline_id);
        $data = $request->getBodyParams();

        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($class_discipline_id);

        $data['class_discipline_id'] = $turma_disciplina->id;

        if (isset($data['class_students_id'])) {
            foreach ($data['class_students_id'] as $classStudentID => $attendance) {
                $data['justify'] = $data['students_justify'][$classStudentID];
                $data['faltas'] = $this->checkSelect($attendance);
                $data['class_student_id'] = $classStudentID;
                $created = $this->frequenciaRepository->create($data);
            }
        }

        if (is_null($created)) {
            return $this->router->redirect("$this->redirect/frequencia?error=422");
        }

        return $this->router->redirect("$this->redirect/frequencia?data=$data[data]&period_id=$data[period_id]");
    }
}
