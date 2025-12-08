<?php

namespace App\Controllers\v1\ClassRooms;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Coordination\ICoordenadorRepository;
use App\Interfaces\Coordination\ICoordenadorTurmaRepository;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Interfaces\Profile\IUsuarioRepository;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Interfaces\Work_Load\ICargaHorariaRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaDisciplinaTransformer;
use App\Transformers\Classe\TurmaTransformer;
use App\Utils\LoggerHelper;
use App\Utils\Paginator;
use App\Utils\Validator;

class TurmaDisciplinaController extends Controller
{
    private $turmaRepository;
    protected $turmaDisciplinaRepository;
    protected $cargaHorariaRepository;
    protected $professorDisciplinaRepository;
    protected $coordenadorRepository;
    protected $pessoaFisicaRepository;
    protected $usuarioRepository;
    protected $coordenadorTurmaRepository;
    protected $turmaDisciplinaTransformer;
    protected $turmaTransformer;

    public function __construct(
        ITurmaRepository $turmaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        ICargaHorariaRepository $cargaHorariaRepository,
        IProfessorDisciplinaRepository $professorDisciplinaRepository,
        ICoordenadorRepository $coordenadorRepository,
        IPessoaFisicaRepository $pessoaFisicaRepository,
        IUsuarioRepository $usuarioRepository,
        ICoordenadorTurmaRepository $coordenadorTurmaRepository,
        TurmaDisciplinaTransformer $turmaDisciplinaTransformer,
        TurmaTransformer $turmaTransformer
    ) {
        parent::__construct();
        $this->turmaRepository = $turmaRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->cargaHorariaRepository = $cargaHorariaRepository;
        $this->professorDisciplinaRepository = $professorDisciplinaRepository;
        $this->coordenadorRepository = $coordenadorRepository;
        $this->pessoaFisicaRepository = $pessoaFisicaRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->coordenadorTurmaRepository = $coordenadorTurmaRepository;
        $this->turmaDisciplinaTransformer = $turmaDisciplinaTransformer;
        $this->turmaTransformer = $turmaTransformer;
    }

    public function index(Request $request, $turma_id)
    {
        if (!hasPermission('visualizar_turmas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $classRooms = $this->turmaRepository->findByUuid($turma_id);

        if (is_null($classRooms)) {
            return $this->router->redirect('turmas?error=not_found');
        }

        $classRooms = (object)$this->turmaTransformer->transform($classRooms);

        $class_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                [
                    'class_id' => $classRooms->code
                ]
            );

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($class_disciplines, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $paginatedBoards = $this->turmaDisciplinaTransformer->transformCollection($paginatedBoards);

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

    public function indexClassRoomDisciplineByCoordenador(Request $request, $turma_id)
    {
        if (!hasPermission('visualizar_disciplinas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $classRoom = $this->turmaRepository->findByUuid($turma_id);

        $class_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                [
                    'class_id' => $classRoom->id
                ]
            );

        $class_disciplines = $this->turmaDisciplinaTransformer->transformCollection($class_disciplines);

        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($class_disciplines, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            'coordination/my-coordination/discipline/index',
            [
                'turma' => $classRoom,
                'disciplinas' => $paginatedBoards,
                'links' => $paginator->links(),
                'active' => 'coordinator',
                'name_discipline' => $params['name_discipline'] ?? null,
                'situation' => $params['situation'] ?? null
            ]
        );
    }

    public function indexByCoordenador(Request $request)
    {
        $params = $request->getQueryParams();

        $usuario = $this->usuarioRepository
            ->findByUuid(
                $_SESSION["user"]->id
            );

        $pessoaFisica = $this->pessoaFisicaRepository
            ->findPessoaFisica(
                ["usuario_id" => $usuario->id]
            );

        if (is_null($pessoaFisica)) {
            return $this->router->redirect('dashboard?error=422');
        }

        $coordenador = $this->coordenadorRepository
            ->allCoordinators(
                ["pessoa_fisica_id" => $pessoaFisica->id]
            );

        if (!empty($coordenador) && !empty($coordenador[0]->id)) {
            $turmas = $this->coordenadorTurmaRepository
                ->allCoordinatorClass(
                    ["coordenador_id" => $coordenador[0]->id]
                );
        }

        if (empty($coordenador) || is_null($coordenador)) {
            $turmas = $this->coordenadorTurmaRepository
                ->allCoordinatorClassWithoutCoordinator(
                    [
                        'active' => 1
                    ]
                );
        }

        $perPage = 10;

        $currentPage = $request->getParam('page')
            ? (int)$request->getParam('page')
            : 1;

        $paginator = new Paginator(
            $turmas,
            $perPage,
            $currentPage
        );

        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view(
            '/coordination/my-coordination/index',
            [
                'active' => 'coordinator',
                'turmas' => $paginatedBoards,
                'links' => $paginator->links(),
                'searchFilter' => $params['name_email'] ?? null,
                'situation' => $params['situation'] ?? null
            ]
        );
    }

    public function create(Request $request, string $class_id)
    {
        $classRooms = $this->turmaRepository
            ->findByUuid($class_id);

        $disciplinas = $this->professorDisciplinaRepository
            ->allTeacherDisciplines(
                ['active' => 1]
            );

        $carga_horaria = $this->cargaHorariaRepository
            ->allWorkLoad();

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
        if (!hasPermission('vincular_turmas_disciplina')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $classRooms = $this->turmaRepository
            ->findByUuid($class_id);

        $disciplinas = $this->professorDisciplinaRepository
            ->allTeacherDisciplines(
                ['active' => 1]
            );

        $carga_horaria = $this->cargaHorariaRepository
            ->allWorkLoad();

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

        if (is_null($created)) {
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
        if (!hasPermission('vincular_turmas_disciplina')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $class_disciplines = $this->turmaDisciplinaRepository
            ->findByUuid($id);

        $classRooms = $this->turmaRepository
            ->findByUuid($class_id);

        $disciplinas = $this->professorDisciplinaRepository
            ->allTeacherDisciplines(
                ['active' => 1]
            );

        $carga_horaria = $this->cargaHorariaRepository
            ->allWorkLoad();

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

    public function update(Request $request, string $class_id, string $id)
    {
        if (!hasPermission('vincular_turmas_disciplina')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $class_disciplines = $this->turmaDisciplinaRepository
            ->findByUuid($id);

        $classRooms = $this->turmaRepository
            ->findByUuid($class_id);

        $disciplinas = $this->professorDisciplinaRepository
            ->allTeacherDisciplines(
                ['active' => 1]
            );

        $carga_horaria = $this->cargaHorariaRepository
            ->allWorkLoad();

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

        if (is_null($updated)) {
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
        if (!hasPermission('deletar_turmas_disciplinas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $class_disciplines = $this->turmaDisciplinaRepository
            ->findByUuid($id);

        $classRooms = $this->turmaRepository
            ->findByUuid($class_id);

        if (is_null($class_disciplines)) {
            return $this->router->redirect("turmas/$classRooms->uuid/disciplinas?error=not_deleted");
        }

        $this->turmaDisciplinaRepository->delete($class_disciplines->id);

        return $this->router->redirect("turmas/$classRooms->uuid/disciplinas/");
    }
}
