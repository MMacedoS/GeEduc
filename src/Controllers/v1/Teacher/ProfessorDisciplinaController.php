<?php

namespace App\Controllers\v1\Teacher;

use App\Controllers\Controller;
use App\Repositories\Discipline\DisciplinaRepository;
use App\Repositories\Teacher\ProfessorRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class ProfessorDisciplinaController extends Controller{

    protected $professorRepository;
    protected $disciplinaRepository;
    protected $professorDisciplinaRepository;

    public function __construct(){
        parent::__construct();
        $this->professorRepository = new ProfessorRepository();
        $this->disciplinaRepository = new DisciplinaRepository();
        $this->professorDisciplinaRepository = new ProfessorDisciplinaRepository();
    }

    public function teacherLinkDiscipline(Request $request, $teacher_id){
        $teacher = $this->professorRepository
            ->teacherWithPersonByUuid((string)$teacher_id);

        $discipline = $this->disciplinaRepository
            ->allDisciplines();

        $teacher_discipline = $this->professorDisciplinaRepository
            ->allTeacherDisciplines(
                ['teacher_id' => $teacher_id]
            );

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;

        $paginator = new Paginator(
            $teacher_discipline,
            $perPage,
            $currentPage
        );

        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('/teacher/teacher-discipline/index',
            [
                'active' => 'register',
                'professor' => $teacher,
                'disciplinas' => $discipline,
                'professor_disciplinas' => $teacher_discipline,
                'links' => $paginator->links()
            ]
        );

    }

    public function store(Request $request, $teacher_id, $discipline_id){
        $teacher = $this->professorRepository
            ->findByUuid((string)$teacher_id);

        if(is_null($teacher)){
            echo json_encode(['status' => 422, 'message' => 'Professor não encontrado']);
            exit();
            return;
        }

        $discipline = $this->disciplinaRepository
            ->findByUuid($discipline_id);

        if(is_null($discipline)){
            echo json_encode(['status' => 422, 'message' => 'Disciplina não encontrada']);
            exit();
            return;
        }

        $data = [
            'teacher_id' => $teacher->id,
            'discipline_id' => $discipline->id,
            'school_year' => date('Y'),
            'active' => 1
        ];

        $created = $this->professorDisciplinaRepository->create($data);

        if(is_null($created)){
            echo json_encode(['status' => 422, 'message' => 'Vínculo não realizado']);
            exit();
            return;
        }

        echo json_encode(['status' => 201, 'message' => 'Vínculo realizado']);
        exit();
        return;
    }

    public function updateStatus(Request $request, $teacher_discipline_id){}
}