<?php

namespace App\Controllers\v1\Activitie;

use App\Controllers\Controller;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Classrooms\TurmaRepository;
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

    public function __construct()
    {
        parent::__construct();   
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository(); 
        $this->atividadeRepository = new AtividadeRepository();
        $this->turmaRepository = new TurmaRepository();
    }

    public function index(Request $request, string $class_id, string $class_discipline_id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );
        
        $activities = $this->atividadeRepository->allActivities();

        $totalValue = $this->sumValueActivities($activities);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($activities, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            'classRooms/discipline/activitie/index', 
            [
                'active' => 'pedagogico', 
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
        $activities = $this->atividadeRepository->allActivities();

        $totalValue = $this->sumValueActivities($activities);

        if($totalValue > self::TEN) {
            return $this->router->redirect("turmas/$class_id/disciplinas/" . $class_discipline_id. "/atividades");
        }
        
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        return $this->router->view(
            'classRooms/discipline/activitie/create', 
            [
                'active' => 'pedagogico', 
                'turma' => $classRooms, 
                'turmas_disciplinas' => $class_disciplines
            ]
        );
    }

    public function store(Request $request, string $class_id, string $class_discipline_id)
    {
        $activities = $this->atividadeRepository->allActivities();

        $totalValue = $this->sumValueActivities($activities);

        if($totalValue > self::TEN) {
            return $this->router->redirect("turmas/$class_id/disciplinas/" . $class_discipline_id. "/atividades");
        }

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
                'classRooms/discipline/activitie/create', 
                [
                    'active' => 'pedagogico', 
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
               'classRooms/discipline/activitie/create', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => 'não pode ser criado',
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines
                ]
            );
        }

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/" . $class_disciplines[0]->uuid . "/atividades");
    }

    public function edit(Request $request, string $class_id, string $class_discipline_id, string $id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $class_disciplines = $this->turmaDisciplinaRepository->allClassDisciplines(
            [
                'class_id' => $classRooms->id,
                'uuid' => $class_discipline_id
            ]
        );

        $activitie = $this->atividadeRepository->findByUuid($id);

        return $this->router->view(
            'classRooms/discipline/activitie/edit', 
            [
                'active' => 'pedagogico', 
                'turma' => $classRooms, 
                'turmas_disciplinas' => $class_disciplines,
                'atividade' => $activitie
            ]
        );
    }

    public function update(Request $request, string $class_id, string $class_discipline_id, string $id)
    {
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
                'classRooms/discipline/activitie/create', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true,  
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines
                ]
            );
        }

        $activitie = $this->atividadeRepository->findByUuid($id);

        $data['class_discipline_id'] = $class_disciplines[0]->id;

        $created = $this->atividadeRepository->update($data, (int)$activitie->id);

        if(is_null($created)) {            
            return $this->router->view(
               'classRooms/discipline/activitie/edit', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => 'não pode ser atulizado',
                    'turma' => $classRooms, 
                    'turmas_disciplinas' => $class_disciplines,
                    'atividade' => $activitie
                ]
            );
        }

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/" . $class_disciplines[0]->uuid . "/atividades");
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
            return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/" . $class_disciplines[0]->uuid . "/atividades?error=not_deleted");
        }

        $this->turmaDisciplinaRepository->delete($activitie->id);

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/" . $class_disciplines[0]->uuid . "/atividades");
    }

    private function sumValueActivities($activities) 
    {
        return array_reduce($activities, function ($sum, $item) {
            return $sum + floatval($item->valor);
        }, 0);
    }
}