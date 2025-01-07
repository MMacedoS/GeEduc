<?php

namespace App\Controllers\v1\Bimestre;

use App\Controllers\Controller;
use App\Repositories\Bimester\BimestreRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator; 

class BimestreController extends Controller{
    protected $bimestreRepository;

    public function __contruct(){
        parent::__construct();
        $this->bimestreRepository = new BimestreRepository();    
    }

    public function index(Request $request){
        if(!hasPermission('visyalizar disciplinas')){
            return $this->router->redirect('bimestres?error=442');
        }

        $bimestres = $this->bimestreRepository->allBimesters();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($bimestres, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'bimestres' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('bimester/index', ['active' => 'pedagogico', 'data' => $data]);
    }

    public function create(){
        if(hasPermission('cadastrar disciplinas')){
            return $this->router->redirect('bimestres?error=442')
        }

        return $this->router->view('bimester/create', ['active' => 'register']);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'bimester' => 'required|min:1|max:20'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'bimester/create',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors().
                ]
            )
        }

        $created = $this->bimestreRepository->create($data);

        if(is_null($created)){
            return $this->router->view('bimester/create', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('bimestres/');
    }

    public function edit(Request $request, int $id){
        if(!hasPermission('editar disciplinas')){
            return $this->router->redirect('bimestres?error=422');
        }

        $bimestre = $this->bimestreRepository->findByUuid($id);
        
        if(is_null($bimestre)){
            return $thisrouter->view('bimester/', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->view('bimester/edit', ['active' => 'pedagogico', 'bimestre' => $bimestre]);
    }

    public function update(Request $request, $id){
        $bimestre = $this->bimestreRepository->findByUuid($id);

        if(is_null($bimestre)){
            return $this->router->view('bimester/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'bimester' => 'required|min:1max:20'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view(
                'bimester/edit',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $updated = $this->bimestreRepository->update($data, $bimestre->id);
        
        if(is_null($updated)){
            return $this->router->view('bimester/edit', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('bimestres/');
    }

    public function destroy(Request $request, $id){
        if(hasPermission('deletar disciplinas')){
            return $this->router->redirect('bimestres?error=422');
        }

        $bimestre = $this->bimestreRepository->findByUuid($id);

        if(is_null($bimestre)){
            return $this->router->view('bimester/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $bimestre = $this->bimestreRepository->delete($bimestre->id);

        return $this->router->redirect('bimestres/');
    }

}