<?php

namespace App\Controllers\v1\Dashboard;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Controllers\v1\Traits\UserToPerson;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\Product\ProdutoRepository;
use App\Repositories\Reservate\ReservaRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Work_Load\CargaHorariaRepository;
use App\Request\Request;
use App\Utils\Paginator;

class DashboardController extends Controller
{
    use UserToPerson;
    use GenericTrait;

    protected $estudanteTurmaRepository;
    protected $estudanteRepository;
    protected $turmaDisciplinaRepository;
    protected $frequenciaRepository;
    protected $cargaHorariaRepository;

    public function __construct() {
        parent::__construct();

        $this->cargaHorariaRepository = new CargaHorariaRepository();
        $this->frequenciaRepository = new FrequenciaRepository();
        $this->turmaDisciplinaRepository = new TurmaDisciplinaRepository();
        $this->estudanteTurmaRepository = new EstudanteTurmaRepository();
        $this->estudanteRepository = new EstudanteRepository();
    }
    
    public function index(Request $request) {
        return $this->prepareIndex($_SESSION['user']->painel);
    }

    public function indexFacility(Request $request) 
    {
        return $this->router->view('dashboard/facility', ['active' => 'dashboard']);
    }

    private function prepareIndex(string $painel)
    {
        if($painel == 'estudante') {
            return $this->indexStudents();
        }
    }

    private function indexStudents() 
    {
        $pessoaAuth = $this->authUser();

        $estudante = $this->estudanteRepository
            ->studentByPersonId($pessoaAuth->id);

        $estudante_turma = $this->estudanteTurmaRepository
            ->studentClassByStudentId($estudante->id);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $estudante_turma->id,
                    'class_id' => $estudante_turma->turma_id
                ]
            );

        $class_discipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);

        $carga_horaria = $this->cargaHorariaRepository->findById($class_discipline->id);

        $total_faltas = $this->sumAbsences($frequencias); 
        $carga = $carga_horaria->carga ?? 80;

        $presenca = $carga - $total_faltas;
        $percentual_faltas = round(($total_faltas / $carga) * 100, 2);
        $percentual_presenca = round(($presenca / $carga) * 100, 2);

        return $this->router->view(
            'dashboard/index',
            [
                'active' => 'dashboard',
                'percentual_faltas' => $percentual_faltas,
                'percentual_presenca' => $percentual_presenca,
                'total_faltas' => $total_faltas,
                'presenca' => $presenca,
            ]
        ); 
    }
}