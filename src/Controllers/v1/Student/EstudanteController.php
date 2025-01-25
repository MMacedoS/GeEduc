<?php

namespace App\Controllers\v1\Student;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\UserToPerson;
use App\Repositories\Person\PessoaContatoRepository;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Plan\PlanoRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class EstudanteController extends Controller 
{
    use UserToPerson;

    protected $estudanteRepository;
    protected $pessoaFisicaRepository;
    protected $pessoaContatoRepository;
    protected $planosRepository;
    protected $estudanteTurmaRepository;

    public function __construct(){
        parent::__construct();
        $this->estudanteRepository = new EstudanteRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
        $this->pessoaContatoRepository = new PessoaContatoRepository();
        $this->planosRepository = new PlanoRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
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

        return $this->router->view('/student/index', 
            [
                'active' => 'pedagogico',  
                'data' => $data
            ]
        );
    }

    public function create(Request $request)
    {
        $planos = $this->planosRepository->allPlans();
        
        return $this->router->view('/student/create', ['active' => 'pedagogico', 'plans' => $planos]);
    }

    public function store(Request $request) 
    {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required',
            'monthly_day' => 'required',
            'plan_id' => 'required',
            'legal_responsible_id' => 'required'
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('student/create', [
                'active' => 'pedagogico', 
                'errors' => $validator->getErrors()
            ]);
        }

        $created = $this->estudanteRepository->saveAll($data);

        if(is_null($created)){
            return $this->router->view('student/create', ['active' => 'pedagogico',  'danger' => true]);
        }
        
        return $this->router->redirect('estudantes/');
    }

    public function edit(Request $request, $id) {
        $estudante = $this->estudanteRepository->findByUuid($id);

        if(is_null($estudante)){
            return $this->router->view('student/', ['active' => 'register', 'danger' => true]);
        }

        $planos = $this->planosRepository->allPlans();

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($estudante->pessoa_fisica_id);

        $pessoa_contato = $this->pessoaContatoRepository->findById($estudante->pessoa_contato_id);
        
        $pessoa_fisica_contato = $this->pessoaFisicaRepository->findById($pessoa_contato->pessoa_fisica_id);

        return $this->router->view('student/edit', 
        [
            'active' => 'register', 
            'estudante' => $estudante, 
            'pessoa_fisica' => $pessoa_fisica,
            'plans' => $planos,
            'pessoa_fisica_contato' => $pessoa_fisica_contato
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

    public function indexStudents(Request $request)
    {
        $pessoaAuth = $this->authUser();
        
        $estudante = $this->estudanteRepository->studentByPersonId($pessoaAuth->id);

        $turmas_estudante = $this->estudanteTurmaRepository->allClassStudents(['student_id' => $estudante->id]);

        return $this->router->view('/student/my-classrooms/index', 
            [
                'active' => 'students',  
                'turmas' => $turmas_estudante
            ]
        );
    }
}