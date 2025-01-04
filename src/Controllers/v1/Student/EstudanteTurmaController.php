<?php

namespace App\Controllers\v1\Student;

use App\Controllers\Controller;
use App\Repositories\Classrooms\TurmaRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Request\Request;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class EstudanteTurmaController extends Controller
{
    protected $estudanteRepository;
    protected $turmaRepository;
    protected $estudanteTurmaRepository;

    public function __construct(){
        parent::__construct();
        $this->estudanteRepository = new EstudanteRepository();
        $this->turmaRepository = new TurmaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
    }

    public function studentLinkClass(Request $request, $student_id) 
    {
        $student = $this->estudanteRepository
            ->studentWithPersonByUuid((string)$student_id);

        $classRooms = $this->turmaRepository
            ->allClassRooms(
                ['active' => 1]
            );

        $student_class = $this->estudanteTurmaRepository
            ->allClassStudents(
                ['student_id' => $student->id]
            );
        
        $classRooms = filterAvailableToursWithYear($classRooms, $student_class, Date('Y'));
        LoggerHelper::logInfo($student_class);

        $perPage = 10;
        $currentPage  =$request->getParam('page') ? (int)$request->getParam('page') : 1;

        $paginator = new Paginator(
            $student_class, 
            $perPage, 
            $currentPage
        );

        $paginatedBoards = $paginator->getPaginatedItems();

        $data = [
            'estudante' => $student,
            'turmas' => $classRooms,
            'estudante_turma' => $paginatedBoards,
            'links' => $paginator->links()
        ];

        return $this->router->view('/student-class/index', ['active' => 'register', 'data' => $data]);
    }

    public function store(Request $request, $student_id, $class_id) 
    {
        $student = $this->estudanteRepository
            ->findByUuid((string)$student_id);

        if(is_null($student)){
            echo json_encode(['status' => 422, 'message' => 'estudante não encontrado']);
            exit();
            return;
        }

        $classRoom = $this->turmaRepository
            ->findByUuid(
                $class_id
            );

        if(is_null($classRoom)){
            echo json_encode(['status' => 422, 'message' => 'turma não encontrado']);
            exit();
            return;
        }

        $data = [
            'class_id' => $classRoom->id,
            'student_id' => $student->id,
            'school_year' => Date('Y'),
            'active' => 1
        ]; 

        $created = $this->estudanteTurmaRepository->create($data);

        if(is_null($created)){
            echo json_encode(['status' => 422, 'message' => 'vinculo não realizado']);
            exit();
            return;
        }
        
        echo json_encode(['status' => 201, 'message' => 'vinculo realizado']);
        exit();
        return;
    }

    public function updateStatus(Request $request, $student_class_id) 
    {
        $student_class = $this->estudanteTurmaRepository
            ->findByUuid((string)$student_class_id);

        if(is_null($student_class)){
            echo json_encode(['status' => 422, 'message' => 'estudante turma não encontrado']);
            exit();
            return;
        }

        $data = [
            'active' => $student_class->ativo == 1 ? '0' : '1'
        ]; 

        $created = $this->estudanteTurmaRepository->update($data, $student_class->id);

        if(is_null($created)){
            echo json_encode(['status' => 422, 'message' => 'vinculo não atualizado']);
            exit();
            return;
        }
        
        echo json_encode(['status' => 201, 'message' => 'vinculo atualizado']);
        exit();
        return;
    }
}
