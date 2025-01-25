<?php

namespace App\Controllers\v1\GradeReport;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Bimester\BimestreRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Discipline\DisciplinaRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\Scores\NotaRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;

class GradeReportController extends Controller 
{
    use GenericTrait;
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $estudanteRepository;
    protected $frequenciaRepository;
    protected $estudanteTurmaRepository;
    protected $bimestreRepository;
    protected $notaRepository;
    protected $disciplinaRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->frequenciaRepository = new FrequenciaRepository();
        $this->atividadeRepository = new AtividadeRepository();
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->bimestreRepository = new BimestreRepository();
        $this->estudanteRepository = new EstudanteRepository();
        $this->notaRepository = new NotaRepository();
        $this->disciplinaRepository = new DisciplinaRepository();
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

        $atividades = $this->atividadeRepository->allActivities(['class_discipline_id' => $turma_disciplina->id]);

        $notas = $this->notaRepository->allScores([
            'class_discipline_id' => $turma_disciplina->id, 
        ]);

        $bimestres = $this->bimestreRepository->allBimesters();

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'class_discipline_id' => $turma_disciplina->id,
                    'class_id' => $turma_disciplina->turma_id,
                ]
            );

        return $this->router->view(
            'teacher/reports/bimesterGrades/index', 
            [
                'turma_disciplina' => $turma_disciplina,
                'estudantes' => $estudantes,
                'atividades' => $atividades,
                'bimestres' => $bimestres,
                'notas' => $notas,
                'frequencias' => $frequencias
            ]
        ); 
    }

    public function indexStudents(Request $request, string $student_class_id)
    {
        $studentClass = $this->estudanteTurmaRepository->findByUuid($student_class_id);

        $allStudentClass = $this->estudanteTurmaRepository
        ->allClassStudents(
            ["student_id" => $studentClass->estudante_id]
            )[0];
      
        $bimestres = $this->bimestreRepository->allBimesters();

        $allDisciplines = $this->disciplinaRepository->findAllDisciplineByClassID($studentClass->turma_id);

        $notas = $this->notaRepository->allScores([
            'student_class_id' => $studentClass->id,
        ]);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $studentClass->id,
                    'class_id' => $studentClass->turma_id
                ]
            );  

        return $this->router->view(
            'student/reports/bimesterGrades/index', 
            [
                'allStudentClass' => $allStudentClass,
                'allDisciplines' => $allDisciplines,
                'bimestres' => $bimestres,
                'frequencias' => $frequencias,
                'notas' => $notas,
            ]
        ); 
    }

    private function generateArrayIDsForWhere(array $params = []): string {
        $ids = array_map(fn($param) => $param->id, $params);
        return implode(',', $ids);
    }
}


