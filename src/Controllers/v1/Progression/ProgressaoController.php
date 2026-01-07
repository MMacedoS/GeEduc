<?php

namespace App\Controllers\v1\Progression;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Coordination\ICoordenadorRepository;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Interfaces\Profile\IUsuarioRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaTransformer;
use App\Transformers\Students\EstudanteTurmaTransformer;

class ProgressaoController extends Controller
{
    private IEstudanteTurmaRepository $estudanteTurmaRepository;
    private ITurmaRepository $turmaRepository;
    private IPessoaFisicaRepository $pessoaFisicaRepository;
    private IUsuarioRepository $usuarioRepository;
    private ICoordenadorRepository $coordenadorRepository;

    public function __construct(
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        ITurmaRepository $turmaRepository,
        IPessoaFisicaRepository $pessoaFisicaRepository,
        IUsuarioRepository $usuarioRepository,
        ICoordenadorRepository $coordenadorRepository
    ) {
        parent::__construct();
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->pessoaFisicaRepository = $pessoaFisicaRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->coordenadorRepository = $coordenadorRepository;
    }

    public function index()
    {
        if (!hasPermission('visualizar_progressao')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $params = [
            'active' => 1,
            'school_year' => 2025
        ];

        if ($_SESSION['user']->painel === 'coordenador') {
            $pessoaFisica = $this->pessoaFisicaRepository->personByUserId($_SESSION['user']->code);

            if ($pessoaFisica) {
                $coordenador = $this->coordenadorRepository->allCoordinators(['pessoa_fisica_id' => $pessoaFisica->id]);

                if (!empty($coordenador)) {
                    $params['coordinator_id'] = $coordenador[0]->id;
                }
            }
        }

        $estudantesTurma = $this->estudanteTurmaRepository->allClassStudents($params);
        $estudantesTurmaTransformed = EstudanteTurmaTransformer::transformCollection($estudantesTurma);

        $turmasGrouped = [];
        foreach ($estudantesTurmaTransformed as $estudanteTurma) {
            $turmaData = $estudanteTurma['turma'];

            if (!$turmaData || !isset($turmaData['id'])) {
                continue;
            }

            $turmaId = $turmaData['id'];

            if (!isset($turmasGrouped[$turmaId])) {
                $turmasGrouped[$turmaId] = [
                    'turma' => $turmaData,
                    'estudantes' => []
                ];
            }
            $turmasGrouped[$turmaId]['estudantes'][] = $estudanteTurma;
        }

        return $this->router->view(
            '/progression/index',
            [
                'active' => 'progression',
                'turmasGrouped' => $turmasGrouped
            ]
        );
    }

    public function getAvailableClasses(Request $request)
    {
        if (!hasPermission('visualizar_progressao')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Sem permissão']);
            exit;
        }

        $currentClassOrder = $request->getParam('current_order');
        $actionType = $request->getParam('action');

        $orderValue = $actionType === 'maintain'
            ? $currentClassOrder
            : ($currentClassOrder + 1);

        $classes = $this->turmaRepository->getClassroomsWithDisciplinesByYear($orderValue, 2026);

        $turmasTransformed = TurmaTransformer::transformCollection($classes);

        header('Content-Type: application/json');
        echo json_encode(['classes' => $turmasTransformed]);
        exit;
    }

    public function processProgression(Request $request)
    {
        if (!hasPermission('editar_progressao')) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Sem permissão']);
            exit;
        }

        $estudanteTurmaUuid = $request->getParam('estudante_turma_uuid');
        $newClassId = $request->getParam('new_class_id');
        $actionType = $request->getParam('action');

        $estudanteTurma = $this->estudanteTurmaRepository->findByUuid($estudanteTurmaUuid);

        if (!$estudanteTurma) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Estudante não encontrado']);
            exit;
        }

        if ($actionType === 'dropout') {
            $this->estudanteTurmaRepository->update(['ativo' => 0], $estudanteTurma->id);

            $estudanteTurmaData = EstudanteTurmaTransformer::transform($estudanteTurma);

            if ($estudanteTurmaData && isset($estudanteTurmaData['estudante']['pessoa_fisica_id'])) {
                $pessoaFisica = $this->pessoaFisicaRepository->findById($estudanteTurmaData['estudante']['pessoa_fisica_id']);

                if ($pessoaFisica && $pessoaFisica->usuario_id) {
                    $this->usuarioRepository->update(['ativo' => 0], $pessoaFisica->usuario_id);
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => 'Estudante marcado como desistente/formado']);
            exit;
        }

        $this->estudanteTurmaRepository->update(['ativo' => 0], $estudanteTurma->id);

        $newTurma = $this->turmaRepository->findByUuid($newClassId);

        if (!$newTurma) {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['error' => 'Turma não encontrada']);
            exit;
        }

        $newData = [
            'turma_id' => $newTurma->id,
            'estudante_id' => $estudanteTurma->estudante_id,
            'ano_letivo' => 2026
        ];

        $this->estudanteTurmaRepository->create($newData);

        header('Content-Type: application/json');
        echo json_encode(['success' => 'Progressão realizada com sucesso']);
        exit;
    }
}
