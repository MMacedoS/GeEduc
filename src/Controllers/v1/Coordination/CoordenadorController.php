<?php

namespace App\Controllers\v1\Coordination;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Coordination\ICoordenadorRepository;
use App\Interfaces\Coordination\ICoordenadorTurmaRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Interfaces\Profile\IUsuarioRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class CoordenadorController extends Controller{

    protected $coordenadorRepository;
    protected $pessoaFisicaRepository;
    protected $usuarioRepository;
    protected $coordenadorTurmaRepository;
    protected $turmaRepository;
    protected $periodoRepository;
    protected $turmaDisciplinaRepository;
    protected $estudanteTurmaRepository;
    

    public function __construct(
        ICoordenadorRepository $coordenadorRepository,
        IPessoaFisicaRepository $pessoaFisicaRepository,
        IUsuarioRepository $usuarioRepository,
        ICoordenadorTurmaRepository $coordenadorTurmaRepository,
        IPeriodoRepository $periodoRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        ITurmaRepository $turmaRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository
    ){
        parent::__construct();
        $this->coordenadorRepository = $coordenadorRepository;
        $this->pessoaFisicaRepository = $pessoaFisicaRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->coordenadorTurmaRepository = $coordenadorTurmaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->periodoRepository = $periodoRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
    }

    public function index(Request $request){
        $params = $request->getQueryParams();
        $coordenadores = $this->coordenadorRepository->allCoordinators($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($coordenadores, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();
        
        return $this->router->view('/coordination/index', [
            'active' => 'pedagogico',  
            'coordenadores' => $paginatedBoards,
            'links' => $paginator->links(),
            'searchFilter'=> $params['name_email'] ?? null,
            'situation' => $params['situation'] ?? null
        ]);
    }

    public function create(Request $request)
    {  
        return $this->router->view('/coordination/create', ['active' => 'pedagogico']);
    }

    public function store(Request $request) {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'graduacao' => 'required',
            'phone' => 'required',
            'doc' => 'required',
            'type_doc' => 'required',
            'address' => 'required',
        ];
        
        if(!$validator->validate($rules)){
            return $this->router->view('coordination/create', [
                'active' => 'pedagogico', 
                'errors' => $validator->getErrors()
            ]);
        }
        
        $created = $this->coordenadorRepository->saveAll($data);
        
        if(is_null($created)){
            return $this->router->view('coordination/create', ['active' => 'pedagogico',  'danger' => true]);
        }
        
        return $this->router->redirect('coordenadores/');
    }

    public function edit(Request $request, $id) {
        $coordenador = $this->coordenadorRepository->findByUuid($id);

        if(is_null($coordenador)){
            return $this->router->view('coordination/', ['active' => 'register', 'danger' => true]);
        }


        $pessoa_fisica = $this->pessoaFisicaRepository->findById($coordenador->pessoa_fisica_id);

        return $this->router->view('coordination/edit', 
        [
            'active' => 'register', 
            'coordenador' => $coordenador, 
            'pessoa_fisica' => $pessoa_fisica,
        ]);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->getBodyParams();

        $coordenador = $this->coordenadorRepository->findByUuid($id);

        if(is_null($coordenador)){
            return $this->router->view('coordination/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $pessoa_fisica = $this->pessoaFisicaRepository->findById($coordenador->pessoa_fisica_id);

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'email' => 'required',
            'graduacao' => 'required',
            'phone' => 'required',
            'doc' => 'required',
            'type_doc' => 'required',
            'address' => 'required',
        ];

        if(!$validator->validate($rules)){
            return $this->router->view('coordination/edit', [
                'active' => 'register',
                'errors' => $validator->getErrors()
            ]);
        }

        $data['usuario_id'] = $pessoa_fisica->usuario_id;
        $data['pessoa_fisica_id'] = $pessoa_fisica->id;
        $data['id'] = $coordenador->id;
        $data['sector'] = 'coordenador';

        $updated = $this->coordenadorRepository->updateAll($data);

        if(is_null($updated)){
            return $this->router->view('coordination/edit', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        return $this->router->redirect('coordenadores/');
    }

    public function destroy(Request $request, $id) {
        $coordenador = $this->coordenadorRepository->findByUuid($id);
      
        if(is_null($coordenador)){
            return $this->router->view('coordination/', [
                'active' => 'pedagogico', 
                'danger' => true
            ]);
        }

        $this->coordenadorRepository->deleteAll($coordenador);
    }

    public function visible(Request $request, string $id) 
    {
        $turma = $this->turmaRepository->findByUuid($id);
        
        if(is_null($turma)){            
            return $this->router->redirect('minha-coordenacao/');
        }

        $active = $turma->visivel == '0' ? '1' : '0';

        $data['visible'] = $active;

        $updated = $this->turmaRepository->update($data, $turma->id);

        return $this->router->redirect('minha-coordenacao/');
    }

    public function indexStudents(Request $request, string $class_id)
    {
        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        $turma = $this->turmaRepository->findByUuid($class_id);
        
        if(is_null($turma)){            
            return $this->router->redirect("minha-coordenacao/turma/$class_id/disciplinas");
        }

        $params = $request->getQueryParams();
        $params['class_id'] = $turma->id;
        $students = $this->estudanteTurmaRepository
            ->allClassStudents($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($students, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();
        
        return $this->router->view('/coordination/my-coordination/classroom/index', [
            'active' => 'pedagogico',  
            'students' => $paginatedBoards,
            'class' => $turma,
            'links' => $paginator->links(),
            'searchFilter'=> $params['student_name'] ?? null,
            'situation' => $params['situation'] ?? null,
            'period_id' => $params['period_id'] ?? null
        ]);
    }
}