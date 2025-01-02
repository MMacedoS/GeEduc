<?php

namespace App\Controllers\v1\Discipline;

use App\Controllers\Controller;
use App\Repositories\Teacher\DisciplinaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class DisciplinaController extends Controller{
    protected $disciplinaRepository;

    public function __construct(){
        parent::__construct();
        $this->disciplinaRepository = new DisciplinaRepository();
    }

    public function index(Request $request){
        if(hasPermission('visualizar disciplinas')){
            return $this->router->redirect('disciplinas?error=442');
        }

        $disciplinas = $this->disciplinaRepository->allDisciplines();
        $perPage = 10; 
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($disciplinas, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'disciplinas' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('discipline/index', ['active' => 'register', 'data' => $data]);
    }

    public function create(){
        if(hasPermission('cadastrar disciplinas')){
            return $this->router->redirect('disciplinas?error=422');
        }

        return $this->router->view('discipline/create', ['active' => 'register']);
    }

    public function store(Request $request, $id){
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'discipline/create',
                [
                    'active' => 'register',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $created = $this->disciplinaRepository->create($data);

        if(is_null($created)){
            return $this->router->view('discipline/create', ['active' => 'register', 'danger' => true]);
        }

        return $this->router->redirect('disciplinas/');
    }

    public function edit(Request $resquest, $id){

    }

    public function update(Request $resquest, $id){
        
    }

    public function destroy(Request $resquest, $id){
        
    }
}