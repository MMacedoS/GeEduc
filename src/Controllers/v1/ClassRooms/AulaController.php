<?php

namespace App\Controllers\v1\ClassRooms;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Interfaces\Classrooms\IAulaRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Weekday\IDiaSemanaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;
use Exception;

class AulaController extends Controller 
{
    use GenericTrait;
    private $aulaRepository;
    private $diaSemanaRepository;
    private $turmaRepository;
    protected $turmaDisciplinaRepository;

    public function __construct(
        IAulaRepository $aulaRepository,
        IDiaSemanaRepository $diaSemanaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        ITurmaRepository $turmaRepository
    ) {
        parent::__construct();   
        $this->aulaRepository = $aulaRepository;
        $this->diaSemanaRepository = $diaSemanaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository; 
    }

    public function index(Request $request, ?string $turma_id, ?string $turma_disciplina_id ) 
    {
        $params = $request->getQueryParams();

        $days = $this->diaSemanaRepository->allWeekDay();
        $classRoom = $this->turmaRepository->findByUuid($turma_id);

        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRoom->id,
                'uuid' => $turma_disciplina_id
            ]
        );

        $params['classroom_discipline_id'] = $class_disciplines[0]->id;
        
        $class = $this->aulaRepository->allClass($params);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($class, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('classRooms/discipline/class/index', [
            'active' => 'pedagogico',
            'aulas' => $paginatedBoards,
            'dias' => $days,
            'links' => $paginator->links(),
            'searchFilter' => $params['search'] ?? null,
            'shift' => $params['shift'] ?? null,
            'day' => $params['day'] ?? null,
            'turma' => $classRoom,
            'turmas_disciplinas' => $class_disciplines
        ]); 
    }

    public function create(Request $request, ?string $turma_id, ?string $turma_disciplina_id)
    {        
        $days = $this->diaSemanaRepository->allWeekDay();
        $classRoom = $this->turmaRepository->findByUuid($turma_id);

        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRoom->id,
                'uuid' => $turma_disciplina_id
            ]
        );
        
        $class = $this->aulaRepository->allClass(['classroom_discipline_id' => $class_disciplines[0]->id]);

        return $this->router->view('classRooms/discipline/class/create', [
            'active' => 'pedagogico',
            'aulas' => $this->extractItemOfObject($class, 'dia_id'),
            'dias' => $days,
            'turma' => $classRoom,
            'turmas_disciplinas' => $class_disciplines
        ]); 

    }

    public function store(Request $request, ?string $turma_id, ?string $turma_disciplina_id)
    {
        $data = $request->getBodyParams();
        
        $validator = new Validator($data);

        $rules = [
            'days_id' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->redirect("turmas/$turma_id/disciplinas/$turma_disciplina_id/aulas");
        } 

        $turma_disciplina = $this->turmaDisciplinaRepository->findByUuid($turma_disciplina_id);

        if(is_null($turma_disciplina)) {
            return $this->router->redirect("turmas/$turma_id/disciplinas/$turma_disciplina_id/aulas");
        }

        $data['classroom_discipline_id'] = $turma_disciplina->id;
        
        $created = $this->aulaRepository->create($data);

        if(is_null($created)) {            
            return $this->router->redirect("turmas/$turma_id/disciplinas/$turma_disciplina_id/aulas");
        }

        return $this->router->redirect("turmas/$turma_id/disciplinas/$turma_disciplina_id/aulas");
    }

    public function edit(Request $request, string $id)
    {
        throw new Exception("Error Processing Request", 1);
    }

    public function update(Request $request, string $id)
    {
        throw new Exception("Error Processing Request", 1);
    }

    public function destroy(
        Request $request, 
        string $turma_id, 
        string $turma_disciplina_id, 
        string $aula_id
    ) {
        if(is_null($aula_id)) {
            echo $this->responseJson(422, "aula não encontrada");
            exit();
        }
        
        $class = $this->aulaRepository->delete($aula_id);

        if (is_null($class)) {
            echo $this->responseJson(422, "aula não pode ser deletada!");
            exit();
        }

        echo $this->responseJson(202, "aula excluida!");
        exit();
    }
}