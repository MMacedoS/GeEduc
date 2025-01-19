<?php

namespace App\Controllers\v1\Scores;

use App\Controllers\Controller;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Bimester\BimestreRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\Scores\NotaRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Request\Request;

class NotaController extends Controller 
{
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $notaRepository;
    protected $estudanteTurmaRepository;
    protected $bimestreRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->atividadeRepository = new AtividadeRepository();
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->bimestreRepository = new BimestreRepository();
        $this->notaRepository = new NotaRepository();
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
            

        $atividades = $this->atividadeRepository->allActivities(['class_discipline_id' => $turma_disciplina->id]);
        
        $notas = $this->notaRepository->allScores([
            'class_discipline_id' => $turma_disciplina->id, 
            'bimester_id' => $paramsURL['bimester_id'] ?? null
        ]);
        
        $bimestres = $this->bimestreRepository->allBimesters();

        return $this->router->view(
            'teacher/my-disciplines/score', 
            [
                'active' => 'teacher',
                'turma_disciplina' => $turma_disciplina,
                'estudantes' => $estudantes,
                'notas' => $notas,
                'bimestres' => $bimestres,
                'atividades' => $atividades,
                'bimestreFilter' => $paramsURL['bimester_id'] ?? null,
            ]
        );
    }

    public function store(Request $request, string $class_discipline_id)
    {
        $data = $request->getBodyParams();
       
        if (isset($data['notas'])) {
            foreach ($data['notas'] as $scoreAndActivitieID => $score) {
                $arrayScoreAndActivitieID = explode(',', $scoreAndActivitieID, 2);
                $data['nota'] = $score;
                $data['estudante_turma_id'] = $arrayScoreAndActivitieID[0];
                $data['atividade_id'] = $arrayScoreAndActivitieID[1];
                
                $created = $this->notaRepository->create($data);
            }
        }

        if(is_null($created)){
            return $this->router->redirect("meus-componentes/$class_discipline_id/notas?error=422");
        }
       
        return $this->router->redirect("meus-componentes/$class_discipline_id/notas?bimester_id=$data[bimester_id]");
    }
}