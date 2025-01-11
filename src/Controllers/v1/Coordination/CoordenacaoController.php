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
        // $coordinators = $this->coordenacaoRepository->allCoordinator();
        
        return $this->router->view('/coordination/create', ['active' => 'pedagogico']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'graduacao' => 'required',
            'phone' => 'required',
            'doc' => 'required',
            'type_doc' => 'required',
            'address' => 'required',
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
        
        return $this->router->redirect('coordenadores/');
    }

    public function edit(Request $request, $id) {
        $coordenador = $this->coordenacaoRepository->findByUuid($id);

        if(is_null($coordenador)){
            return $this->router->view('coordination/', ['active' => 'register', 'danger' => true]);
        }


        $pessoa_fisica = $this->pessoaFisicaRepository->findById($coordenador->pessoa_fisica_id);

        return $this->router->view('coordination/edit', 
        [
            'active' => 'register', 
            'coordenador' => $coordenador, 
            'pessoa_fisica' => $pessoa_fisica,
        ]);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->getBodyParams();

        $coordenador = $this->coordenacaoRepository->findByUuid($id);

        if(is_null($coordenador)){
            return $this->router->view('coordination/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($coordenador->pessoa_fisica_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'graduacao' => 'required',
            'phone' => 'required',
            'doc' => 'required',
            'type_doc' => 'required',
            'address' => 'required',
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('coordination/edit', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $data['usuario_id'] = $pessoa_fisica->usuario_id;
        $data['pessoa_fisica_id'] = $pessoa_fisica->id;
        $data['id'] = $coordenador->id;
        $data['sector'] = 'coordenador';

        $updated = $this->coordenacaoRepository->updateAll($data);

        if(is_null($updated)){
            return $this->router->view('coordination/edit', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        return $this->router->redirect('coordenadores/');
    }

    public function destroy(Request $request, $id) {
        $coordenador = $this->coordenacaoRepository->findByUuid($id);
      
        if(is_null($coordenador)){
            return $this->router->view('coordination/', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        $this->coordenacaoRepository->deleteAll($coordenador);
    }
}