<?php

namespace App\Controllers\v1\ClassRooms;

use App\Controllers\Controller;
use App\Repositories\Classrooms\TurmaRepository;
use App\Repositories\Coordination\CoordenadorRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class TurmaController extends Controller 
{
    protected $turmaRepository;
    protected $coordenadorRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->turmaRepository = new TurmaRepository(); 
        $this->coordenadorRepository = new CoordenadorRepository();
    }

    public function index(Request $request) 
    {
        $classRooms = $this->turmaRepository->allClassRooms();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($classRooms, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'turmas' => $paginatedBoards,
            'links' => $paginator->links()
        ];
        return $this->router->view('classRooms/index', ['active' => 'pedagogico', 'data' => $data]); 
    }

    public function create(Request $request)
    {
        $coordenators = $this->coordenadorRepository->allCoordinators(['active' => 1]);
        return $this->router->view('classRooms/create', ['active' => 'register', 'coordenadores' => $coordenators]);
    }

    public function store(Request $request)
    {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'order' => 'required',
            'shift' => 'required',
            'active' => 'required',
            'coordinator_id' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/create', 
                [
                    'active' => 'pedagogico', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->turmaRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('classRooms/create', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('turmas/');
    }

    public function edit(Request $request, string $id)
    {
        $turma = $this->turmaRepository->findByUuid($id);
        $coordenators = $this->coordenadorRepository->allCoordinators(['active' => 1]);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'pedagogico', 'danger' => true]);
        }
        
        return $this->router->view('classRooms/edit', ['active' => 'pedagogico', 'turma' => $turma, 'coordenadores' => $coordenators]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->getBodyParams();

        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'order' => 'required',
            'shift' => 'required',
            'coordinator_id' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/edit', 
                [
                    'active' => 'pedagogico', 
                    'errors' => $validator->getErrors()
                ]
            );
        }
        
        $updated = $this->turmaRepository->update($data, $turma->id);

        if(is_null($updated)) {            
            return $this->router->view('classRooms/edit', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('turmas/');
    }

    public function destroy(Request $request, string $id)
    {
        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $this->turmaRepository->delete($turma->id);

        return $this->router->redirect('turmas/');
    }
}