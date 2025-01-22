<?php

namespace App\Controllers\v1\GradeReport;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Bimester\BimestreRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\Scores\NotaRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Request\Request;

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
}