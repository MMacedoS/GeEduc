<?php

namespace App\Controllers\v1\Progression;

use App\Controllers\Controller;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Transformers\Classe\TurmaTransformer;
use App\Transformers\Students\EstudanteTurmaTransformer;

class ProgressaoController extends Controller
{
    private IEstudanteTurmaRepository $estudanteTurmaRepository;
    private EstudanteTurmaTransformer $estudanteTurmaTransformer;
    private ITurmaRepository $turmaRepository;
    private TurmaTransformer $turmaTransformer;

    public function __construct(
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        EstudanteTurmaTransformer $estudanteTurmaTransformer,
        ITurmaRepository $turmaRepository,
        TurmaTransformer $turmaTransformer
    ) {
        parent::__construct();
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->estudanteTurmaTransformer = $estudanteTurmaTransformer;
        $this->turmaRepository = $turmaRepository;
        $this->turmaTransformer = $turmaTransformer;
    }

    public function index()
    {
        if (!hasPermission('visualizar_progressao')) {
            return $this->router->redirect('dashboard?error=422');
        }

        $params = ['active' => 1];

        $turmas = $this->turmaRepository->allClassRooms($params);
        $turmasTransformed = $this->turmaTransformer->transformCollection($turmas);
    }
}
