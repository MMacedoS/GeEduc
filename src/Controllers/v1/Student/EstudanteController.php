<?php

namespace App\Controllers\v1\Student;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\UserToPerson;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Person\IPessoaContatoRepository;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Interfaces\Plan\IPlanoRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EstudanteController extends Controller 
{
    use UserToPerson;

    protected $estudanteRepository;
    protected $pessoaFisicaRepository;
    protected $pessoaContatoRepository;
    protected $planosRepository;
    protected $estudanteTurmaRepository;
    protected $turmaRepository;

    public function __construct(
        IEstudanteRepository $estudanteRepository,
        IPessoaFisicaRepository $pessoaFisicaRepository,
        IPessoaContatoRepository $pessoaContatoRepository,
        IPlanoRepository $planosRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        ITurmaRepository $turmaRepository
    ){
        parent::__construct();
        $this->estudanteRepository = $estudanteRepository;
        $this->pessoaFisicaRepository = $pessoaFisicaRepository;
        $this->pessoaContatoRepository = $pessoaContatoRepository;
        $this->planosRepository = $planosRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->turmaRepository = $turmaRepository;
    }

    public function index(Request $request){
        $params = $request->getQueryParams();

        $estudantes = $this->estudanteRepository->allStudents($params);
        $perPage = 10;
        $currentPage  =$request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($estudantes, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('/student/index', 
            [
                'active' => 'pedagogico',  
                'estudantes' => $paginatedBoards,
                'links' => $paginator->links(),
                'searchFilter' => $params['name_email'] ?? null,
                'situation'=> $params['situation'] ?? null
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

                $fileType = pathinfo($_FILES['arq-excel']['name'], PATHINFO_EXTENSION);
                
                if (!in_array($fileType, ['xlsx', 'xls'])) {
                    throw new Exception('Arquivo inválido. Por favor, envie um arquivo Excel (.xlsx ou .xls)');
                }

                $inputFile = $_FILES['arq-excel']['tmp_name'];
                $spreadsheet = IOFactory::load($inputFile);

                $this->organizationTableExcel($spreadsheet, function ($sheetTitle, $studentRow) {
                    $student = $this->createStudentTableExcel($studentRow);
                    if(!is_null($studentRow["Turma"]) && !empty($studentRow["Turma"])) {
                        $this->createStudentClassTableExcel((int)$studentRow["Turma"], (int)$student);
                    }
                });

                $created = true;
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                return $this->handleExcelError(
                    'Erro ao ler o arquivo Excel. Por favor, verifique o arquivo enviado.', 
                    $e->getMessage()
                );
            } catch (Exception $e) {
                return $this->handleExcelError(
                    'Erro ao processar o arquivo Excel.', 
                    $e->getMessage()
                );
            }
        }

        if (!$created) {
            return $this->router->view('student/create', [
                'active' => 'pedagogico',
                'danger' => true,
                'error' => 'Nenhum registro foi criado. Verifique o arquivo enviado.'
            ]);
        }

        return $this->router->redirect('estudantes/');
    }
    private function organizationTableExcel($spreadsheet, callable $callback)
    {
        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            $headers = [];
    
            foreach ($worksheet->getRowIterator(1, 1)->current()->getCellIterator() as $cell) {
                $headers[] = $cell->getValue();
            }
    
            foreach ($worksheet->getRowIterator(2) as $row) {
                $studentRow = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true); 
    
                $index = 0;
                foreach ($cellIterator as $cell) {
                    if ($cell->getValue() !== null && trim($cell->getValue()) !== '') {
                        $studentRow[$headers[$index]] = $cell->getValue();
                    }
                    $index++;
                }
    
                if (!empty($studentRow)) {
                    $callback($worksheet->getTitle(), $studentRow);
                }
            }
        }
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
        $studentData['active'] = 1;
        $studentData['procees_monthylees'] = 'Não';
        $estudante = $this->estudanteRepository->saveAll($studentData);

        if(is_null($estudante)) {
            return false;
        }
        return $estudante->id;
    }

    private function createStudentClassTableExcel(int $turma_id, int $id) {
        $estudanteTurma = [];
        $estudanteTurma['class_id'] = $turma_id;
        $estudanteTurma['student_id'] = $id;
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
            return $this->router->redirect("estudantes?sem-sucesso");
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

    private function handleExcelError(string $userMessage, string $logMessage)
    {
        return $this->router->view('student/create', [
            'active' => 'pedagogico',
            'danger' => true,
            'error' => $userMessage
        ]);
    }
}