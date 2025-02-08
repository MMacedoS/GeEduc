<?php

namespace App\Controllers\v1\Discipline;

use App\Controllers\Controller;
use App\Interfaces\Discipline\IDisciplinaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class DisciplinaController extends Controller{
    protected $disciplinaRepository;

    public function __construct(
        IDisciplinaRepository $disciplinaRepository
    ){
        parent::__construct();
        $this->disciplinaRepository = $disciplinaRepository;
    }

    public function index(Request $request){
        $params = $request->getQueryParams();
        
        if(!hasPermission('visualizar disciplinas')){
            return $this->router->redirect('disciplinas?error=442');
        }

        $disciplinas = $this->disciplinaRepository->allDisciplines([
            'search' => $params['name_discipline'] ?? null,
            'active' => $params['situation'] ?? null
        ]);
        
        $perPage = 10; 
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($disciplinas, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'disciplinas' => $paginatedBoards,
            'links' => $paginator->links(),
            'active' => 'pedagogico',
            'name_discipline' => $params['name_discipline'] ?? null,
            'situation' => $params['situation'] ?? null
        ];

        return $this->router->view('discipline/index', $data);
    }

    public function create(){
        if(hasPermission('cadastrar disciplinas')){
            return $this->router->redirect('disciplinas?error=422');
        }

        return $this->router->view('discipline/create', ['active' => 'register']);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'discipline/create',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $created = $this->disciplinaRepository->create($data);

        if(is_null($created)){
            return $this->router->view('discipline/create', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('disciplinas/');
    }

    public function edit(Request $resquest, $id){
        if(!hasPermission('editar disciplinas')){
            return $this->router->redirect('disciplinas?error=422');
        }

        $disciplina = $this->disciplinaRepository->findByUuid($id);

        if(is_null($disciplina)){
            return $this->router->view('discipline/', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->view('discipline/edit', ['active' => 'pedagogico', 'disciplina' => $disciplina]);
    }

    public function update(Request $request, $id){
        $disciplina = $this->disciplinaRepository->findByUuid($id);

        if(is_null($disciplina)){
            return $this->router->view('discipline/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'discipline/edit',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $updated = $this->disciplinaRepository->update($data, $disciplina->id);

        if(is_null($updated)){
            return $this->router->view('discipline/edit', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('disciplinas/');
    }

    public function destroy(Request $resquest, $id){
        if(hasPermission('deletar disciplinas')){
            return $this->router->redirect('disciplinas?error=422');
        }

        $disciplina = $this->disciplinaRepository->findByUuid($id);

        if(is_null($disciplina)){
            return $this->router->view('discipline/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $disciplina = $this->disciplinaRepository->delete($disciplina->id);

        return $this->router->redirect('disciplinas/');
    }
}