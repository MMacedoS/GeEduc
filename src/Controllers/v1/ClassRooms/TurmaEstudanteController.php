<?php

namespace App\Controllers\v1\ClassRooms;

use App\Controllers\Controller;
use App\Repositories\Classrooms\TurmaEstudanteRepository;
use App\Repositories\Classrooms\TurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class TurmaEstudanteController extends Controller 
{
    protected $turmaEstudanteRepository;
    protected $turmaRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->turmaRepository = new TurmaRepository(); 
        $this->turmaEstudanteRepository = new TurmaEstudanteRepository();
    }

    public function index(Request $request) 
    {
        $classRooms = $this->turmaEstudanteRepository->allClassStudents();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($classRooms, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'turmas' => $paginatedBoards,
            'links' => $paginator->links()
        ];
        return $this->router->view('class-student/index', ['active' => 'register', 'data' => $data]); 
    }

    public function create(Request $request)
    {
        return $this->router->view('class-student/create', ['active' => 'register']);
    }

    public function store(Request $request)
    {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'order' => 'required',
            'shift' => 'required',
            'active' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/create', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->turmaRepository->create($data);

        if(is_null($created)) {            
        return $this->router->view('classRooms/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('turmas/');
    }

    public function edit(Request $request, string $id)
    {
        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'register', 'danger' => true]);
        }
        
        return $this->router->view('classRooms/edit', ['active' => 'register', 'turma' => $turma]);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->getBodyParams();

        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'register', 'danger' => true]);
        }

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'order' => 'required',
            'shift' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/edit', 
                [
                    'active' => 'register', 
                    'errors' => $validator->getErrors()
                ]
            );
        }
        
        $updated = $this->turmaRepository->update($data, $turma->id);

        if(is_null($updated)) {            
            return $this->router->view('classRooms/edit', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('turmas/');
    }

    public function destroy(Request $request, string $id)
    {
        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'register', 'danger' => true]);
        }

        $this->turmaRepository->delete($turma->id);

        return $this->router->redirect('turmas/');
    }
}