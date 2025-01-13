<?php

namespace App\Controllers\v1\Frequencies;

use App\Controllers\Controller;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Bimester\BimestreRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class FrequenciaController extends Controller 
{
    const TEN = 10;
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $frequenciaRepository;
    protected $estudanteTurmaRepository;
    protected $professorDisciplinaRepository;
    protected $bimestreRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->frequenciaRepository = new FrequenciaRepository();
        $this->atividadeRepository = new AtividadeRepository();
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->professorDisciplinaRepository = new ProfessorDisciplinaRepository();
        $this->bimestreRepository = new BimestreRepository();
    }

    public function indexStudents(Request $request, string $class_discipline_id)
    {
        $student_class = $this->estudanteTurmaRepository->findByUuid($class_discipline_id);
        
        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $student_class->id,
                    'class_id' => $student_class->turma_id
                ]
            );

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($frequencias, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            'student/my-classrooms/frequencies', 
            [
                'active' => 'students',
                'estudante_turma' => $student_class,
                'frequencias' => $paginatedBoards, 
                'links' => $paginator->links()
            ]
        ); 
    }

    public function indexTeacher(Request $request, string $class_discipline_id)
    {
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

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'class_discipline_id' => $turma_disciplina->id,
                    'class_id' => $turma_disciplina->turma_id
                ]
            );

        $bimestres = $this->bimestreRepository->allBimesters();

        return $this->router->view(
            'teacher/my-disciplines/frequency', 
            [
                'active' => 'teacher',
                'turma_disciplina' => $turma_disciplina,
                'estudantes' => $estudantes,
                'frequencias' => $frequencias,
                'bimestres' => $bimestres
            ]
        ); 
    }

    public function store(Request $request, string $class_discipline_id)
    {
        $data = $request->getBodyParams();

        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($class_discipline_id);

        $data['class_discipline_id'] = $turma_disciplina->id;

        foreach ($data['class_student_id'] as $value) {
            $data['class_student_id'] = $value;
            
            $created = $this->frequenciaRepository->create($data);
        }
        
        if(is_null($created)){
            return $this->router->redirect("meus-componentes/$class_discipline_id/frequencia?error=422");
        }
        
        return $this->router->redirect("meus-componentes/$class_discipline_id/frequencia");
    }

    // $dataForChart = array_map(function ($frequencia) {
    //     return [
    //         'y' => date('Y-m-d', strtotime($frequencia->data)), // Formatar a data
    //         'frequencia' => intval($frequencia->frequencia), // Frequência como valor numérico
    //     ];
    // }, $frequencias);
    
    // // Exemplo de saída JSON para usar no gráfico
    // echo json_encode($dataForChart, JSON_PRETTY_PRINT);
}