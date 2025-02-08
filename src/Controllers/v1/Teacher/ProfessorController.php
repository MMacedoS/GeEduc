<?php

namespace App\Controllers\v1\Teacher;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\UserToPerson;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Interfaces\Teacher\IProfessorRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class ProfessorController extends Controller 
{
    use UserToPerson;

    protected $professorRepository;
    protected $professorDisciplinaRepository;
    protected $pessoaFisicaRepository;
    protected $turmaDisciplinaRepository;

    public function __construct(
        IProfessorRepository $professorRepository,
        IProfessorDisciplinaRepository $professorDisciplinaRepository,
        IPessoaFisicaRepository $pessoaFisicaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository
    ) {
        parent::__construct();   
        $this->professorRepository = $professorRepository; 
        $this->pessoaFisicaRepository = $pessoaFisicaRepository; 
        $this->professorDisciplinaRepository = $professorDisciplinaRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
    }

    public function index(Request $request) 
    {
        $params = $request->getQueryParams();

        if(!hasPermission('visualizar professores')) {
            return $this->router->redirect('professores?error=422');
        }

        $professores = $this->professorRepository->allTeachers($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($professores, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('teacher/index', [
            'active' => 'pedagogico', 
            'professores' => $paginatedBoards,
            'links' => $paginator->links(),
            'searchFilter' => $params['name_email'] ?? null,
            'situation' => $params['situation'] ?? null
        ]); 
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

    public function indexTeacher(Request $request)
    {
        $pessoaAuth = $this->authUser();
        
        $professor = $this->professorRepository->teacherByPersonId($pessoaAuth->id);

        $class_discipline = $this->turmaDisciplinaRepository
            ->classDisciplinesByTeacherId(
                $professor->id
            );

        $perPage = 10; 
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($class_discipline, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'disciplinas' => $class_discipline,
            'links' => $paginator->links(),
            'active' => 'teacher',
            'name_discipline' => $params['name_discipline'] ?? null,
            'situation' => $params['situation'] ?? null
        ];

        return $this->router->view('/teacher/my-disciplines/index', $data);
    }
}