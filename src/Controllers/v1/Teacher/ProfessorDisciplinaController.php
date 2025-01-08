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

    public function store(Request $request, $teacher_id, $discipline_id){}

    public function updateStatus(Request $request, $teacher_discipline_id){}
}