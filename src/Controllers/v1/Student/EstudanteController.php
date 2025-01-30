<?php

namespace App\Controllers\v1\Student;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\UserToPerson;
use App\Repositories\Person\PessoaContatoRepository;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Plan\PlanoRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Classrooms\TurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;
use PHPExcel_IOFactory;
use Exception;

class EstudanteController extends Controller 
{
    use UserToPerson;

    protected $estudanteRepository;
    protected $pessoaFisicaRepository;
    protected $pessoaContatoRepository;
    protected $planosRepository;
    protected $estudanteTurmaRepository;
    protected $turmaRepository;

    public function __construct(){
        parent::__construct();
        $this->estudanteRepository = new EstudanteRepository();
        $this->pessoaFisicaRepository = new PessoaFisicaRepository();
        $this->pessoaContatoRepository = new PessoaContatoRepository();
        $this->planosRepository = new PlanoRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->turmaRepository = new TurmaRepository();
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

    public function createExcel(Request $request)
    {
        $planos = $this->planosRepository->allPlans();
        
        return $this->router->view('/student/createExcel', ['active' => 'pedagogico', 'plans' => $planos]);
    }

    public function storeExcel(Request $request) 
    {
        $created = false;
    
        if ($_FILES['arq-excel']['error'] === UPLOAD_ERR_OK) {
            try {
                // Verifica o tipo do arquivo
                $fileType = pathinfo($_FILES['arq-excel']['name'], PATHINFO_EXTENSION);
                if (!in_array($fileType, ['xlsx', 'xls'])) {
                    throw new Exception('Arquivo inválido. Por favor, envie um arquivo Excel (.xlsx ou .xls)');
                }
    
                // Carrega o arquivo Excel
                $inputFile = $_FILES['arq-excel']['tmp_name'];
                $excel = PHPExcel_IOFactory::load($inputFile);
               
                $studentsData = $this->organizationTableExcel($excel);

                // Processa os dados dos estudantes
                foreach ($studentsData as $student) {
                    $this->createReponsibleTableExcel($student);
                    
                    $this->createStudentTableExcel($student);

                    $this->createStudentClassTableExcel($student);

                    $created = true;
                }

            } catch (Exception $e) {
                dd($e->getMessage());
                error_log("Erro ao processar arquivo Excel: " . $e->getMessage());
                return $this->router->view('student/create', [
                    'active' => 'pedagogico',
                    'danger' => true,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            return $this->router->view('student/create', [
                'active' => 'pedagogico',
                'danger' => true,
                'error' => 'Erro no upload do arquivo'
            ]);
        }
        
        if (!$created) {
            return $this->router->view('student/create', [
                'active' => 'pedagogico',
                'danger' => true
            ]);
        }
        
        return $this->router->redirect('estudantes/');
    }

    private function organizationTableExcel($excel) {
        // Array para armazenar os dados
        $studentsData = [];

        // Seleciona a primeira planilha
        $worksheet = $excel->getActiveSheet();
            
        
        // Pega o cabeçalho (primeira linha)
        $headerRow = $worksheet->getRowIterator()->current();
        
        $cellIterator = $headerRow->getCellIterator();
        $headers = [];
        foreach ($cellIterator as $cell) {
            $headers[] = $cell->getValue();
        }
        
        // Itera sobre as linhas de dados (a partir da segunda linha)
        $rowIterator = $worksheet->getRowIterator();
        $rowIterator->next(); // Pula o cabeçalho
        
        while ($rowIterator->valid()) {
            $row = $rowIterator->current();
            $cellIterator = $row->getCellIterator();
            
            $studentRow = [];
            $index = 0;
            
            foreach ($cellIterator as $cell) {
                $studentRow[$headers[$index]] = $cell->getValue();
                $index++;
            }
            
            if (!empty(array_filter($studentRow))) { // Ignora linhas vazias
                $studentsData[] = $studentRow;
            }
            
            $rowIterator->next();
        }

        return $studentsData;
    }

    private function createReponsibleTableExcel(&$student) {
        $respData = [];
        $respData['name'] = $student['Responsável pelo Estudante'];
        $respData['type_doc'] = "CPF";
        $respData['doc'] = $student['CPF do Responsável'];
        $respData['email'] = $student['E-mail do Responsável'];
        $respData['phone'] = $student['Telefone do Responsável'];
        $respData['address'] = $student['Endereço do Estudante'];
        $respData['legal_responsive'] = 1;
        $respData['active'] = 1;

        $responsavel = $this->pessoaContatoRepository->saveAll($respData); 
        if($responsavel) {
            $student['responsavel_id'] = $responsavel->id;

            return true;
        }
        return false;
    }

    private function createStudentTableExcel(&$student) {
        $studentData = [];
        $studentData['name'] = $student['Nome do Estudante'];
        $studentData['type_doc'] = "CPF";
        $studentData['doc'] = $student['CPF do Estudante'];
        $studentData['email'] = $student['E-mail do Estudante'];
        $studentData['mother'] = $student['Mãe'];
        $studentData['father'] = $student['Pai'];
        $studentData['address'] = $student['Endereço do Estudante'];
        $studentData['discont'] = $student['Desconto'];
        $studentData['matricula'] = $student['Matricula'];
        $studentData['monthly_day'] = $student['Dia da mensalidade'];
        $studentData['plan_id'] = $student['Plano'];
        $studentData['procees_monthylees'] = strtolower($student['Gerar Mensalidades']);
        $studentData['phone'] = $student['Telefone do Responsável'];
        $studentData['active'] = 1;
        $studentData['legal_responsible_id'] = $student['responsavel_id'];
        $estudante = $this->estudanteRepository->saveAll($studentData);

        if($estudante) {
            $student['id'] = $estudante->id;

            return true;
        }
        return false;
    }

    private function createStudentClassTableExcel(&$student) {
        $estudanteTurma = [];
        $turma = $this->turmaRepository->findByName($student["Turma"]);
        $estudanteTurma['class_id'] = $turma["id"];
        $estudanteTurma['student_id'] = $student['id'];
        $estudanteTurma['school_year'] = date('Y');
        $newStudentClass = $this->estudanteTurmaRepository->create($estudanteTurma);

        if($newStudentClass) {
            return true;
        }
        return false;
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
            // 'legal_responsible_id' => 'required'
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