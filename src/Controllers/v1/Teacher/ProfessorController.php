<?php

namespace App\Controllers\v1\Teacher;

use App\Controllers\Controller;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Teacher\ProfessorRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class ProfessorController extends Controller 
{
    protected $professorRepository;
    protected $pessoaFisicaRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->professorRepository = new ProfessorRepository(); 
        $this->pessoaFisicaRepository = new PessoaFisicaRepository(); 
    }

    public function index(Request $request) 
    {
        if(!hasPermission('visualizar professores')) {
            return $this->router->redirect('professores?error=422');
        }

        $professores = $this->professorRepository->allTeachers();
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($professores, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'professores' => $paginatedBoards,
            'links' => $paginator->links()
        ];
        return $this->router->view('teacher/index', ['active' => 'pedagogico', 'data' => $data]); 
    }    

    public function create() 
    {
        if(!hasPermission('cadastrar professores')) {
            return $this->router->redirect('professores?error=422');
        }

        return $this->router->view('teacher/create', ['active' => 'register']);
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

        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'teacher/create', 
                [
                    'active' => 'pedagogico', 
                    'errors' => $validator->getErrors()
                ]
            );
        } 
        
        $created = $this->professorRepository->saveAll($data);

        if(is_null($created)) {            
        return $this->router->view('teacher/create', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('professores/');
    }

    public function edit(Request $request, $id) 
    {
        if(!hasPermission('editar professores')) {
            return $this->router->redirect('professores?error=422');
        }

        $professor = $this->professorRepository->findByUuid($id);

        if (is_null($professor)) {
            return $this->router->view('teacher/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($professor->pessoa_fisica_id);
        
        return $this->router->view('teacher/edit', ['active' => 'pedagogico', 'professor' => $professor, 'pessoa_fisica' => $pessoa_fisica]);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->getBodyParams();

        $professor = $this->professorRepository->findByUuid($id);

        if (is_null($professor)) {
            return $this->router->view('teacher/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($professor->pessoa_fisica_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',           
            'email' => 'required',
            'mother' => 'required',
            'doc' => 'required',

        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'teacher/edit', 
                [
                    'active' => 'pedagogico', 
                    'errors' => $validator->getErrors()
                ]
            );
        }
        
        $data['usuario_id'] = $pessoa_fisica->usuario_id;
        $data['pessoa_fisica_id'] = $pessoa_fisica->id;
        $data['id'] = $professor->id;
        $data['sector'] = 'professor';
        
        $updated = $this->professorRepository->updateAll($data);

        if(is_null($updated)) {            
            return $this->router->view('teacher/edit', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('professores/');
    }

    public function destroy(Request $request, $id) 
    {
        if(!hasPermission('deletar professores')) {
            return $this->router->redirect('professores?error=422');
        }

        $professor = $this->professorRepository->findByUuid($id);

        if (is_null($professor)) {
            return $this->router->view('teacher/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $this->professorRepository->deleteAll($professor);
    }
}