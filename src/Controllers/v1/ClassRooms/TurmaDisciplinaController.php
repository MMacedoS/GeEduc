<?php

namespace App\Controllers\v1\ClassRooms;

use App\Controllers\Controller;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Classrooms\TurmaRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Repositories\Work_Load\CargaHorariaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class TurmaDisciplinaController extends Controller 
{
    private $turmaRepository;
    protected $turmaDisciplinaRepository;
    protected $cargaHorariaRepository;
    protected $professorDisciplinaRepository;

    public function __construct()
    {
        parent::__construct();   
        $this->turmaRepository = new TurmaRepository(); 
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository(); 
        $this->cargaHorariaRepository = new CargaHorariaRepository(); 
        $this->professorDisciplinaRepository = new ProfessorDisciplinaRepository(); 
    }

    public function index(Request $request, $turma_id) 
    {
        $classRooms = $this->turmaRepository->findByUuid($turma_id);
        $class_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                [
                    'class_id' => $classRooms->id
                ]
            );

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($class_disciplines, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            'classRooms/discipline/index', 
            [
                'active' => 'pedagogico', 
                'turma' => $classRooms, 
                'turmas_disciplinas' => $paginatedBoards, 
                'links' => $paginator->links()
            ]
        ); 
    }

    public function create(Request $request, string $class_id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $disciplinas = $this->professorDisciplinaRepository->allTeacherDisciplines(['active' => 1]);
        $carga_horaria = $this->cargaHorariaRepository->allWorkLoad();

        return $this->router->view(
            'classRooms/discipline/create', 
            [
                'active' => 'register', 
                'disciplinas' => $disciplinas, 
                'carga_horaria' => $carga_horaria,
                'turma' => $classRooms
            ]
        );
    }

    public function store(Request $request, $class_id)
    {
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $disciplinas = $this->professorDisciplinaRepository->allTeacherDisciplines(['active' => 1]);
        $carga_horaria = $this->cargaHorariaRepository->allWorkLoad();

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [     
            'work_load_id' => 'required',
            'teacher_discipline_id' => 'required',
            'academic_year' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/discipline/create', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => "reveja os campos preenchidos",
                    'disciplinas' => $disciplinas, 
                    'carga_horaria' => $carga_horaria,
                    'turma' => $classRooms
                ]
            );
        } 

        $data['class_id'] = $classRooms->id;
        
        foreach ($data['teacher_discipline_id'] as $key => $value) {
            $data['teacher_discipline_id'] = $value;
            $created = $this->turmaDisciplinaRepository->create($data);
        }

        if(is_null($created)) {            
            return $this->router->view(
                'classRooms/discipline/create', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => 'não pode ser criado',
                    'disciplinas' => $disciplinas, 
                    'carga_horaria' => $carga_horaria,
                    'turma' => $classRooms
                ]
            );
        }

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/");
    }

    public function edit(Request $request, string $class_id, string $id)
    {
        $class_disciplines = $this->turmaDisciplinaRepository->findByUuid($id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $disciplinas = $this->professorDisciplinaRepository->allTeacherDisciplines(['active' => 1]);
        $carga_horaria = $this->cargaHorariaRepository->allWorkLoad();

        if (is_null($class_disciplines)) {
            return $this->router->view(
                'classRooms/discipline/index', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => 'item não encontrado',
                    'turma' => $classRooms    
                ]
            );
        }
        
        return $this->router->view(
            'classRooms/discipline/edit', 
            [
                'active' => 'pedagogico', 
                'turma' => $classRooms, 
                'turma_disciplina' => $class_disciplines,
                'disciplinas' => $disciplinas, 
                'carga_horaria' => $carga_horaria
            ]
        );
    }

    public function update(Request $request,string $class_id, string $id)
    {
        $class_disciplines = $this->turmaDisciplinaRepository->findByUuid($id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);
        $disciplinas = $this->professorDisciplinaRepository->allTeacherDisciplines(['active' => 1]);
        $carga_horaria = $this->cargaHorariaRepository->allWorkLoad();
        $data = $request->getBodyParams();

        if (is_null($class_disciplines)) {
            return $this->router->view(
                'classRooms/discipline/edit', 
                [
                    'active' => 'pedagogico', 
                    'turma' => $classRooms, 
                    'turma_disciplina' => $class_disciplines,
                    'disciplinas' => $disciplinas, 
                    'carga_horaria' => $carga_horaria
                ]
            );
        }

        $validator = new Validator($data);

        $rules = [     
            'work_load_id' => 'required',
            'teacher_discipline_id' => 'required',
            'academic_year' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/discipline/create', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => "reveja os campos preenchidos",
                    'disciplinas' => $disciplinas, 
                    'carga_horaria' => $carga_horaria,
                    'turma' => $classRooms
                ]
            );
        }
        $data['class_id'] = $classRooms->id;

        foreach ($data['teacher_discipline_id'] as $key => $value) {
            $data['teacher_discipline_id'] = $value;
            $updated = $this->turmaDisciplinaRepository->update($data, $class_disciplines->id);
        }

        if(is_null($updated)) {            
            return $this->router->view(
                'classRooms/discipline/create', 
                [
                    'active' => 'pedagogico', 
                    'danger' => true, 
                    'message' => 'não pode ser criado',
                    'disciplinas' => $disciplinas, 
                    'carga_horaria' => $carga_horaria,
                    'turma' => $classRooms
                ]
            );
        }

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/");
    }

    public function destroy(Request $request, string $class_id, string $id)
    {
        $class_disciplines = $this->turmaDisciplinaRepository->findByUuid($id);
        $classRooms = $this->turmaRepository->findByUuid($class_id);

        LoggerHelper::logInfo(json_encode($classRooms));

        if (is_null($class_disciplines)) {
            return $this->router->redirect("turmas/$classRooms->uuid/disciplinas?error=not_deleted");
        }

        $this->turmaDisciplinaRepository->delete($class_disciplines->id);

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/");
    }
}