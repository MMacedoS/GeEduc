<?php

namespace App\Controllers\v1\Student;

use App\Controllers\Controller;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class EstudanteController extends Controller{

    protected $estudanteRepository;
    protected $pessoaFisicaRepository;

    public function __construct(){
        parent::__construct();
        $this->estudanteRepository = new EstudanteRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
    }

    public function index(Request $request){
        $estudantes = $this->estudanteRepository->allStudents();
        $perPage = 10;
        $currentPage  =$request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($estudantes, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();


        $data = [
            'estudantes' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('/student/index', ['active' => 'register', 'data' => $data]);
    }

    public function create(){
        return $this->router->view('/student/create', ['active' => 'register']);
    }

    public function store(Request $request){
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('student/create', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->estudanteRepository->saveAll($data);

        if(is_null($created)){
            return $this->router->view('student/create', ['active' => 'register', 'danger' => true]);
        }
        
        return $this->router->redirect('estudantes/');
    }

    public function edit(Request $request, $id){
        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('/student', ['active' => 'register', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($estudante->pessoa_fisica_id);

        return $this->router->view('student/edit', 
        [
            'active' => 'register', 
            'estudante' => $estudante, 
            'pessoa_fisica' => $pessoa_fisica
        ]);
    }

    public function update(Request $request, $id){
        $data = $request->getBodyParams();

        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('student/', ['active' => 'register', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($estudante->pessoa_fisica_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('student/edit', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $data['usuario_id'] = $pessoa_fisica->usuario->id;
        $data['pessoa_fisica_id'] = $pessoa_fisica->id;
        $data['id'] = $estudante->id;
        $data['sector'] = 'estudante';

        $updated = $this->estudanteRepository->updateAll($data);

        if(is_null($updated)){
            return $this->router->view('student/edit', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        return $this->router->redirect('estudantes/');
    }

    public function destroy(Request $request, $id){
        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('student/', [
                'active' => 'register',
                'danger' => true
            ]);
        }

        $this->estudanteRepository->deleteAll($estudante);
    }
}