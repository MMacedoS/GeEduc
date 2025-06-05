<?php

namespace App\Controllers\v1\Scores;

use App\Controllers\Controller;
use App\Interfaces\Activitie\IAtividadeRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Scores\INotaRepository;
use App\Interfaces\Scores\IParalelaRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;

class NotaController extends Controller 
{
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $notaRepository;
    protected $estudanteTurmaRepository;
    protected $estudanteRepository;
    protected $periodoRepository;
    protected $paralelaRepository;
    protected $active;
    protected $routeView;
    protected $redirect;

    public function __construct(
        IAtividadeRepository $atividadeRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        IEstudanteRepository $estudanteRepository,
        IPeriodoRepository $periodoRepository,
        INotaRepository $notaRepository,
        IParalelaRepository $paralelaRepository                        
    ) {
        parent::__construct();   
        $this->atividadeRepository = $atividadeRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->estudanteRepository = $estudanteRepository;
        $this->periodoRepository = $periodoRepository;
        $this->notaRepository = $notaRepository;
        $this->paralelaRepository = $paralelaRepository;
    }

    private function defineRoutes($class_discipline_id) {
        switch($_SESSION["user"]->painel) {
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

    public function indexTeacher(Request $request, string $class_discipline_id)
    {
        $this->defineRoutes($class_discipline_id);
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
            'period_id' => $paramsURL['period_id'] ?? null
        ]);

        $paralela = $this->paralelaRepository->allScoresParallel([
            'class_discipline_id' => $turma_disciplina->id, 
            'period_id' => $paramsURL['period_id'] ?? null
        ]);
        
        $periodos = $this->periodoRepository->all(['active' => '1']);

        return $this->router->view(
            "$this->routeView/score", 
            [
                'active' => $this->active,
                'turma_disciplina' => $turma_disciplina,
                'estudantes' => $estudantes,
                'notas' => $notas,
                'periodos' => $periodos,
                'atividades' => $atividades,
                'periodFilter' => $paramsURL['period_id'] ?? null,
                'paralelas' => $paralela
            ]
        );
    }

    public function store(Request $request, string $class_discipline_id, string $class_id = null)
    {
        $this->defineRoutes($class_discipline_id);
        $data = $request->getBodyParams();
        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($class_discipline_id);

        if(is_null($turma_disciplina)) {
            return $this->router->redirect("$this->redirect/notas?error=422");
        }
       
        if (isset($data['notas'])) {
            foreach ($data['notas'] as $scoreAndActivitieID => $score) {
                $arrayScoreAndActivitieID = explode(',', $scoreAndActivitieID, 2);
                $data['nota'] = $score;
                $data['estudante_turma_id'] = $arrayScoreAndActivitieID[0];
                $data['atividade_id'] = $arrayScoreAndActivitieID[1];
                
                $created = $this->notaRepository->create($data);
            }
        }

        if (isset($data['parallel'])) {
            $data_parallel = [];
            foreach ($data['parallel'] as $studantsID => $score) {
                $data_parallel['nota'] = $score;
                $data_parallel['class_student_id'] = $studantsID;
                $data_parallel['class_discipline_id'] = $turma_disciplina->id;
                $data_parallel['period_id'] = $data['period_id'];
                
                $this->paralelaRepository->create($data_parallel);
            }
        }

        if(is_null($created)){
            return $this->router->redirect("$this->redirect/notas?error=422");
        }
    
        return $this->router->redirect("$this->redirect/notas?period_id=$data[period_id]");
    }

    public function indexResponsibleStudents(Request $request, string $student_id, string $studant_class_id) 
    {    
        $student = $this->estudanteRepository
            ->studentWithPersonByUuid((string)$student_id);

        $student_class = $this->estudanteTurmaRepository->findByUuid($studant_class_id);            
        
        $notas = $this->notaRepository->allScoresByStudents([
            'student_class_id' => $student_class->id, 
            'bimester_id' => $paramsURL['bimester_id'] ?? null
        ]);
        
        $periodos = $this->periodoRepository->all(['active' => '1']);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($notas, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            '/my-little-group/student-class/scores', 
            [
                'active' => 'teacher',
                'estudante' => $student,
                'notas' => $paginatedBoards,
                'links' => $paginator->links(),
                'periodos' => $periodos,
                'bimestreFilter' => $paramsURL['bimester_id'] ?? null,
            ]
        );
    }
}