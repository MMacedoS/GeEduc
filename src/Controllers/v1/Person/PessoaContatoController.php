<?php

namespace App\Controllers\v1\Person;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\UserToPerson;
use App\Repositories\Person\PessoaContatoRepository;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class PessoaContatoController extends Controller 
{
    use UserToPerson;

    protected $pessoaContatoRepository;
    protected $pessoaFisicaRepository;
    protected $estudanteRepository;

    public function __construct(){
        parent::__construct();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
        $this->pessoaContatoRepository = new PessoaContatoRepository();
        $this->estudanteRepository = new EstudanteRepository();
    }

    public function index(Request $request){
        $params = $request->getQueryParams();
        $pessoas = $this->pessoaContatoRepository->allPersons([
            'name_email' => $params['name_email'] ?? null,
            'ativo' => $params['situation'] ?? null,
        ]);

        $perPage = 10;
        $currentPage  =$request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($pessoas, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();
        
        $data = [
            'active' => 'responsible_legal',  
            'pessoas' => $paginatedBoards,
            'links' => $paginator->links(),
            'name_email' => $params['name_email'] ?? null,
            'situation' => $params['situation'] ?? null,
        ];

        return $this->router->view('/person/index', $data);
    }

    public function create(Request $request)
    {        
        return $this->router->view('/person/create', ['active' => 'pedagogico']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required',
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('person/create', [
                'active' => 'pedagogico', 
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->pessoaContatoRepository->saveAll($data);

        if(is_null($created)){
            return $this->router->view('person/create', ['active' => 'pedagogico',  'danger' => true]);
        }
        
        return $this->router->redirect('pessoas/');
    }

    public function edit(Request $request, $id) {
        $pessoa_contato = $this->pessoaContatoRepository->findByUuid($id);

        if(is_null($pessoa_contato)){
            return $this->router->view('person/', ['active' => 'register', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($pessoa_contato->pessoa_fisica_id);

        return $this->router->view('person/edit', 
        [
            'active' => 'register', 
            'pessoa_contato' => $pessoa_contato, 
            'pessoa_fisica' => $pessoa_fisica
        ]);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->getBodyParams();

        $pessoa_contato = $this->pessoaContatoRepository->findByUuid($id);

        if(is_null($pessoa_contato)){
            return $this->router->view('person/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($pessoa_contato->pessoa_fisica_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('person/edit', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $data['usuario_id'] = (int)$pessoa_fisica->usuario_id;
        $data['person_id'] = (int)$pessoa_fisica->id;
        $data['id'] = (int)$pessoa_contato->id;
        $data['sector'] = 'responsavel_legal';

        $updated = $this->pessoaContatoRepository->updateAll($data);

        if(is_null($updated)){
            return $this->router->view('person/edit', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        return $this->router->redirect('pessoas/');
    }

    public function destroy(Request $request, $id) 
    {
        $pessoa_contato = $this->pessoaContatoRepository->findByUuid($id);

        if(is_null($pessoa_contato)){
            return $this->router->view('person/', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        $this->pessoaContatoRepository->deleteAll($pessoa_contato->id);

        echo json_encode("deletado com sucesso");
        exit();
    }

    public function indexWithoutPagination(Request $request) 
    {
        $data = $request->getQueryParams();
        $pessoas = $this->pessoaContatoRepository->allPersons($data);
        echo json_encode($pessoas);
        exit();
    }

    public function indexMyLittleGroup(Request $request)
    {
        $personAuth = $this->authUser();

        $pessoa_contato = $this->pessoaContatoRepository
        ->findByContactPersonId(
            [
                'person_id' => $personAuth->id
            ]
        );

        $estudantes = $this->estudanteRepository->allStudents(
            [
                'contact_person_id' => $pessoa_contato->id
            ]
        );

        $perPage = 10;
        $currentPage  =$request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($estudantes, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('/my-little-group/index', 
            [
                'active' => 'responsible_legal',  
                'estudantes' => $paginatedBoards,
                'links' => $paginator->links()
            ]
        );
    }
}