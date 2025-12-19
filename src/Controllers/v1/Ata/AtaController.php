<?php

namespace App\Controllers\v1\Ata;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Interfaces\Scores\IBoletimRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaDisciplinaTransformer;
use App\Transformers\Classe\TurmaTransformer;
use App\Transformers\Students\EstudanteTurmaTransformer;

class AtaController extends Controller
{
    protected ITurmaDisciplinaRepository $turmaDisciplinaRepository;
    protected ITurmaRepository $turmaRepository;
    protected TurmaTransformer $turmaTransformer;
    protected TurmaDisciplinaTransformer $turmaDisciplinaTransformer;
    protected IEstudanteTurmaRepository $estudanteTurmaRepository;
    protected EstudanteTurmaTransformer $estudanteTurmaTransformer;
    protected IBoletimRepository $boletimRepository;
    protected IPeriodoRepository $periodoRepository;

    public function __construct(
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        ITurmaRepository $turmaRepository,
        TurmaTransformer $turmaTransformer,
        TurmaDisciplinaTransformer $turmaDisciplinaTransformer,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        EstudanteTurmaTransformer $estudanteTurmaTransformer,
        IBoletimRepository $boletimRepository,
        IPeriodoRepository $periodoRepository
    ) {
        parent::__construct();
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->turmaTransformer = $turmaTransformer;
        $this->turmaDisciplinaTransformer = $turmaDisciplinaTransformer;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->estudanteTurmaTransformer = $estudanteTurmaTransformer;
        $this->boletimRepository = $boletimRepository;
        $this->periodoRepository = $periodoRepository;
    }


    public function index(Request $request)
    {
        if (!hasPermission('visualizar_atas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $queryParams = $request->getQueryParams();

        $params = ['active' => 1];

        $turmas = $this->turmaRepository->allClassRooms($params);
        $turmasTransformed = $this->turmaTransformer->transformCollection($turmas);

        $turmasComDisciplinas = array_map(function ($turma) use ($queryParams) {
            $disciplinasParams = [
                'class_id' => $turma['code'],
                'academic_year' => $queryParams['academic_year'] ?? date('Y'),
                'active' => 1
            ];

            $disciplinas = $this->turmaDisciplinaRepository->allClassDisciplines($disciplinasParams);
            $turma['disciplinas'] = $this->turmaDisciplinaTransformer->transformCollection($disciplinas);
            $turma['total_disciplinas'] = count($disciplinas);

            return $turma;
        }, $turmasTransformed);

        return $this->router->view('ata/index', [
            'active' => 'relatorios',
            'data' => [
                'turmas' => $turmasComDisciplinas,
                'todasTurmas' => $turmasTransformed,
                'filtros' => $queryParams
            ]
        ]);
    }

    public function create(Request $request)
    {
        if (!hasPermission('visualizar_atas')) {
            return $this->router->redirect('dashboard?error=422');
        }
    }

    public function readScoresToAta(Request $request, $turmaUuid)
    {
        if (!hasPermission('visualizar_atas')) {
            return $this->router->redirect('dashboard?error=422');
        }

        if (!$turmaUuid) {
            return $this->router->redirect('ata?error=404');
        }

        $class = $this->turmaRepository->findByUuid($turmaUuid);

        if (is_null($class)) {
            return $this->router->redirect('ata?error=404');
        }

        $anoLetivo = $request->getQueryParams()['academic_year'] ?? date('Y');

        $periodos = $this->periodoRepository->all(['active' => '1']);

        $all_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines([
                'class_id' => $class->id,
                'academic_year' => $anoLetivo,
                'active' => 1
            ]);

        $all_disciplines = $this->turmaDisciplinaTransformer
            ->transformCollection($all_disciplines);

        $all_students = $this->estudanteTurmaRepository
            ->allClassStudents([
                'class_id' => $class->id,
                'school_year' => $anoLetivo
            ]);

        $all_students = $this->estudanteTurmaTransformer
            ->transformCollection($all_students);

        return $this->router->view('ata/ata', [
            'active' => 'relatorios',
            'data' => [
                'turma' => [
                    'id' => $class->id,
                    'uuid' => $class->uuid,
                    'nome' => $class->nome,
                    'turno' => $class->turno,
                    'ano_letivo' => $anoLetivo
                ],
                'disciplinas' => $all_disciplines,
                'estudantes' => $all_students,
                'periodos' => $periodos,
                'notaService' => $this->boletimRepository
            ]
        ]);
    }
}
