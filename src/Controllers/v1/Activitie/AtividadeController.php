<?php

namespace App\Controllers\v1\Activitie;

use App\Controllers\Controller;
use App\Interfaces\Activitie\IAtividadeRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class AtividadeController extends Controller 
{
    const TEN = 10;
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $turmaRepository;

    public function __construct(
        ITurmaRepository $turmaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IAtividadeRepository $atividadeRepository
    ) {
        parent::__construct();   
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository; 
        $this->atividadeRepository = $atividadeRepository;
        $this->turmaRepository = $turmaRepository;
    }

    private function defineRoutes($class_id, $class_discipline_id) {
        switch($_SESSION["user"]->painel) {
            case 'coordenador':
                $this->redirect = "minha-coordenacao/turma/$class_id/disciplina/$class_discipline_id/atividades";
                $this->routeView = 'coordination/my-coordination/discipline/activitie';
                $this->active = "coordinator";
                break;
            case 'administrativo':
                    $this->redirect = "minha-coordenacao/turma/$class_id/disciplina/$class_discipline_id/atividades";
                    $this->routeView = 'coordination/my-coordination/discipline/activitie';
                    $this->active = "coordinator";
                    break;
            case 'professor':
                $this->redirect = "meus-componentes/$class_id/disciplina/$class_discipline_id/atividades";
                $this->routeView = 'teacher/my-disciplines/activitie';
                $this->active = "teacher";
                break;
        }
    }

    public function index(Request $request, string $class_id, string $class_discipline_id)
    {
        $this->defineRoutes($class_id, $class_discipline_id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );
        
        $activities = $this->atividadeRepository->allActivities(['class_discipline_id' => $class_disciplines[0]->id]);

        $totalValue = $this->sumValueActivities($activities);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($activities, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            "$this->routeView/index", 
            [
                'active' => $this->active, 
                'turma' => $classRooms, 
                'turmas_disciplinas' => $class_disciplines,
                'total_maximo' => $totalValue,
                'atividades' => $paginatedBoards, 
                'links' => $paginator->links()
            ]
        );
    }

    public function create(Request $request, string $class_id, string $class_discipline_id)
    {        
        $this->defineRoutes($class_id, $class_discipline_id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        $activities = $this->atividadeRepository->allActivities(['class_discipline_id' => $class_disciplines[0]->id, 'active' => 1]);

        $totalValue = $this->sumValueActivities($activities);

        
        if($totalValue >= self::TEN) {
            return $this->router->redirect($this->redirect);
        }
        
        return $this->router->view(
            "$this->routeView/create", 
            [
                'active' => $this->active, 
                'turma' => $classRooms, 
                'turmas_disciplinas' => $class_disciplines
            ]);  
        
    }

    public function store(Request $request, string $class_id, string $class_discipline_id)
    {
        $this->defineRoutes($class_id, $class_discipline_id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        $activities = $this->atividadeRepository->allActivities(['class_discipline_id' => $class_disciplines[0]->id, 'active' => 1]);

        $totalValue = $this->sumValueActivities($activities);

        if($totalValue >= self::TEN) {
            return $this->router->redirect($this->redirect);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [     
            'type' => 'required',
            'value' => 'required',
            'active' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                "$this->routeView/create", 
                [
                    'active' => $this->active, 
                    'danger' => true,  
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines
                ]
            );
        }

        $data['class_discipline_id'] = $class_disciplines[0]->id;

        $created = $this->atividadeRepository->create($data);

        if(is_null($created)) {            
            return $this->router->view(
               "$this->routeView/create", 
                [
                    'active' => $this->active, 
                    'danger' => true, 
                    'message' => 'não pode ser criado',
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines
                ]
            );
        }

        return $this->router->redirect($this->redirect);
    }

    public function edit(Request $request, string $class_id, string $class_discipline_id, string $id)
    {
        $this->defineRoutes($class_id, $class_discipline_id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        $activitie = $this->atividadeRepository->findByUuid($id);

        return $this->router->view(
            "$this->routeView/edit", 
            [
                'active' => $this->active, 
                'turma' => $classRooms, 
                'turmas_disciplinas' => $class_disciplines,
                'atividade' => $activitie
            ]
        );
    }

    public function update(Request $request, string $class_id, string $class_discipline_id, string $id)
    {
        $this->defineRoutes($class_id, $class_discipline_id);

        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [     
            'type' => 'required',
            'value' => 'required',
            'active' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                "$this->routeView/create", 
                [
                    'active' => $this->active, 
                    'danger' => true,  
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines
                ]
            );
        }
        
        $activitie = $this->atividadeRepository->findByUuid($id);

        $activities = $this->atividadeRepository->allActivities(['class_discipline_id' => $class_disciplines[0]->id, 'active' => 1]);
        $totalValue = $this->sumValueActivities($activities, $data['type']);

        if(($totalValue + $data['value']) >= self::TEN) {
            return $this->router->view(
                "$this->routeView/edit", 
                    [
                        'active' => $this->active, 
                        'danger' => true, 
                        'message' => 'Nota máxima excedida! Não pode ser atualizado',
                        'turma' => $classRooms, 
                        'turmas_disciplinas' => $class_disciplines,
                        'atividade' => $activitie
                    ]
                );
        }


        $data['class_discipline_id'] = $class_disciplines[0]->id;

        $created = $this->atividadeRepository->update($data, (int)$activitie->id);
        if(is_null($created)) {            
            return $this->router->view(
            "$this->routeView/edit", 
                [
                    'active' => $this->active, 
                    'danger' => true, 
                    'message' => 'não pode ser atualizado',
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines,
                    'atividade' => $activitie
                ]
            );
        }
     
        return $this->router->redirect($this->redirect);
    }

    public function destroy(Request $request, string $class_id, string $class_discipline_id, string $id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        $activitie = $this->atividadeRepository->findByUuid($id);

        if (is_null($activitie)) {
            echo json_encode(['status' => 422, 'message' => 'não pode ser deletado!']);
            exit();
        }

        $this->atividadeRepository->delete($activitie->id);

        echo json_encode(['status' => 422, 'message' => 'deletado!']);
        exit();
    }

    private function sumValueActivities($activities, $activitieUpdate = null) 
    {
        return array_reduce($activities, function ($sum, $item) use ($activitieUpdate) {
            if($item->ativo && $item->tipo != $activitieUpdate) {
                return $sum + floatval($item->valor);
            }
        }, 0);
    }
}