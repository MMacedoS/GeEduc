<?php

namespace App\Controllers\v1\ClassRooms;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Coordination\ICoordenadorRepository;
use App\Interfaces\Coordination\ICoordenadorTurmaRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaTransformer;
use App\Utils\Paginator;
use App\Utils\Validator;

class TurmaController extends Controller
{
    use GenericTrait;
    protected $turmaRepository;
    protected $turmaDisciplinaRepository;
    protected $coordenadorRepository;
    protected $coordenadorTurmaRepository;
    protected $turmaTransformer;

    public function __construct(
        ITurmaRepository $turmaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        ICoordenadorRepository $coordenadorRepository,
        ICoordenadorTurmaRepository $coordenadorTurmaRepository,
        TurmaTransformer $turmaTransformer
    ) {
        parent::__construct();
        $this->turmaRepository = $turmaRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->coordenadorRepository = $coordenadorRepository;
        $this->coordenadorTurmaRepository = $coordenadorTurmaRepository;
        $this->turmaTransformer = $turmaTransformer;
    }

    public function index(Request $request)
    {
        if (!hasPermission('visualizar_turmas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $params = $request->getQueryParams();

        $classRooms = $this->turmaRepository->allClassRooms($params);
        $perPage = 10;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($classRooms, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        $paginatedBoards = $this->turmaTransformer->transformCollection($paginatedBoards);

        return $this->router->view('classRooms/index', [
            'active' => 'pedagogico',
            'turmas' => $paginatedBoards,
            'links' => $paginator->links(),
            'searchFilter' => $params['classroom'] ?? null,
            'shift' => $params['shift'] ?? null,
            'coordinator' => $params['coordinator'] ?? null,
            'situation' => $params['situation'] ?? null
        ]);
    }

    public function create(Request $request)
    {
        if (!hasPermission('cadastrar_turma')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $coordenators = $this->coordenadorRepository->allCoordinators(['active' => 1]);
        return $this->router->view('classRooms/create', ['active' => 'register', 'coordenadores' => $coordenators]);
    }

    public function store(Request $request)
    {
        if (!hasPermission('cadastrar_turma')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'order' => 'required',
            'shift' => 'required',
            'active' => 'required',
            'coordinator_id' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/create',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $created = $this->turmaRepository->create($data);

        if (is_null($created)) {
            return $this->router->view('classRooms/create', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('turmas/');
    }

    public function edit(Request $request, string $id)
    {
        if (!hasPermission('editar_turma')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $turma = $this->turmaRepository->findByUuid($id);
        $coordenatorsClass = $this->coordenadorTurmaRepository->allCoordinatorClass(['class_id' => $turma->id]);
        $coordenatorsClass = $this->extractItemOfObject($coordenatorsClass, 'coordenador_id');
        $coordenators = $this->coordenadorRepository->allCoordinators(['active' => 1]);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->view('classRooms/edit', ['active' => 'pedagogico', 'turma' => $turma, 'coordenadores' => $coordenators, 'coordenadores_inseridos' => $coordenatorsClass]);
    }

    public function update(Request $request, string $id)
    {
        if (!hasPermission('editar_turma')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $data = $request->getBodyParams();
        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $validator = new Validator($data);

        $rules = [
            'name' => 'required|min:1|max:100',
            'order' => 'required',
            'shift' => 'required',
            'coordinator_id' => 'required'
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view(
                'classRooms/edit',
                [
                    'active' => 'pedagogico',
                    'errors' => $validator->getErrors()
                ]
            );
        }

        $updated = $this->turmaRepository->update($data, $turma->id);

        if (is_null($updated)) {
            return $this->router->view('classRooms/edit', ['active' => 'pedagogico', 'danger' => true]);
        }

        return $this->router->redirect('turmas/');
    }

    public function destroy(Request $request, string $id)
    {
        if (!hasPermission('deletar_turmas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->view('classRooms/', ['active' => 'pedagogico', 'danger' => true]);
        }

        $this->turmaRepository->delete($turma->id);

        return $this->router->redirect('turmas/');
    }

    public function duplicateDisciplines(Request $request, string $id)
    {
        if (!hasPermission('cadastrar_turma')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $turma = $this->turmaRepository->findByUuid($id);

        if (is_null($turma)) {
            return $this->router->redirect('turmas?danger=1');
        }

        $currentYear = (int)date('Y');

        $duplicated = $this->turmaDisciplinaRepository->duplicateDisciplinesForYear(
            $turma->id,
            $currentYear
        );

        if (!$duplicated) {
            return $this->router->redirect('turmas?danger=1');
        }

        return $this->router->redirect('turmas?success=1');
    }
}
