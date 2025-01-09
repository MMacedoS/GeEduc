<?php

namespace App\Controllers\v1\Coordination;

use App\Controllers\Controller;
use App\Repositories\Coordination\CoordenacaoRepository;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class CoordenacaoController extends Controller{

    protected $coordenacaoRepository;
    protected $pessoaFisicaRepository;

    public function __construct(){
        parent::__construct();
        $this->coordenacaoRepository = new CoordenacaoRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
    }

    public function index(Request $request){
        $coordenadores = $this->coordenacaoRepository->allCoordinators();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($coordenadores, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'coordenadores' => $paginatedBoards,
            'links' => $paginator->links()
        ];
        
        return $this->router->view('/coordination/index', 
            [
                'active' => 'pedagogico',  
                'data' => $data
            ]
        );
    }

    public function create(Request $request)
    {
        $coordinators = $this->coordenacaoRepository->allCoordinator();
        
        return $this->router->view('/coordination/create', ['active' => 'pedagogico', 'coordinators' => $coordinators]);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required',
            'monthly_day' => 'required',
            'plan_id' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('coordination/create', [
                'active' => 'pedagogico', 
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->coordenacaoRepository->saveAll($data);

        if(is_null($created)){
            return $this->router->view('coordination/create', ['active' => 'pedagogico',  'danger' => true]);
        }
        
        return $this->router->redirect('coordenacao/');
    }

    public function edit(Request $request, $id) {
        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('student/', ['active' => 'register', 'danger' => true]);
        }

        $planos = $this->planosRepository->allPlans();

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($estudante->pessoa_fisica_id);

        return $this->router->view('student/edit', 
        [
            'active' => 'register', 
            'estudante' => $estudante, 
            'pessoa_fisica' => $pessoa_fisica,
            'plans' => $planos
        ]);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->getBodyParams();

        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('student/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($estudante->pessoa_fisica_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required',
            'monthly_day' => 'required',
            'plan_id' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('student/edit', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $data['usuario_id'] = $pessoa_fisica->usuario_id;
        $data['pessoa_fisica_id'] = $pessoa_fisica->id;
        $data['id'] = $estudante->id;
        $data['sector'] = 'estudante';

        $updated = $this->estudanteRepository->updateAll($data);

        if(is_null($updated)){
            return $this->router->view('student/edit', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        return $this->router->redirect('estudantes/');
    }

    public function destroy(Request $request, $id) {
        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('student/', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        $this->estudanteRepository->deleteAll($estudante);
    }
}