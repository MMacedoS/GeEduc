<?php

namespace App\Controllers\v1\Frequencies;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\Period\PeriodoRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Repositories\Work_Load\CargaHorariaRepository;
use App\Request\Request;
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

    public function __construct()
    {
        parent::__construct();   
        $this->frequenciaRepository = new FrequenciaRepository();
        $this->atividadeRepository = new AtividadeRepository();
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->professorDisciplinaRepository = new ProfessorDisciplinaRepository();
        $this->periodoRepository = new PeriodoRepository();
        $this->cargaHorariaRepository = new CargaHorariaRepository();
        $this->estudanteRepository = new EstudanteRepository();
    }

    public function indexStudents(Request $request, string $studant_class_id)
    {
        $student_class = $this->estudanteTurmaRepository->findByUuid($studant_class_id);
        
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

    public function indexResponsibleStudents(Request $request, string $student_id, string $studant_class_id)
    {
        $student = $this->estudanteRepository
            ->studentWithPersonByUuid((string)$student_id);

        $student_class = $this->estudanteTurmaRepository->findByUuid($studant_class_id);
        
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
        $paramsURL = $request->getQueryParams();

        $turma_disciplina = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                ['uuid' => $class_discipline_id]
            )[0];

        $estudantes = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'class_id' => $turma_disciplina->turma_id, 
                    'school_year' => Date('Y')
                ]
            );
        
        $data_presence = Date('Y-m-d');

        if(isset($paramsURL['data']) && !empty($paramsURL['data'])) {
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

            
        $periodos = $this->periodoRepository->all();

        return $this->router->view(
            'teacher/my-disciplines/frequency', 
            [
                'active' => 'teacher',
                'turma_disciplina' => $turma_disciplina,
                'estudantes' => $estudantes,
                'frequencias' => $frequencias,
                'periodos' => $periodos,
                'dataFilter' => $data_presence,
                'bimestreFilter' => $paramsURL['period_id'] ?? null,
            ]
        ); 
    }

    public function store(Request $request, string $class_discipline_id)
    {
        $data = $request->getBodyParams();
     
        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($class_discipline_id);

        $data['class_discipline_id'] = $turma_disciplina->id;

       
        if (isset($data['class_students_id'])) {
            foreach ($data['class_students_id'] as $classStudentID => $attendance) {
                $data['faltas'] = $attendance;
                $data['class_student_id'] = $classStudentID;
                $created = $this->frequenciaRepository->create($data);
            }
        }
      
        if(is_null($created)){
            return $this->router->redirect("meus-componentes/$class_discipline_id/frequencia?error=422");
        }
       
        return $this->router->redirect("meus-componentes/$class_discipline_id/frequencia?data=$data[data]&period_id=$data[period_id]");
    }
}