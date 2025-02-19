<?php

namespace App\Controllers\v1\Dashboard;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Controllers\v1\Traits\UserToPerson;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Discipline\IDisciplinaRepository;
use App\Interfaces\Frequencies\IFrequenciaRepository;
use App\Interfaces\MonthlyFees\IMensalidadeRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Interfaces\Teacher\IProfessorRepository;
use App\Interfaces\Work_Load\ICargaHorariaRepository;
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
    protected $mensalidadeRepository;
    protected $turmaRepository;
    protected $professorRepository;
    protected $disciplinaRepository;

    public function __construct(
        ICargaHorariaRepository $cargaHorariaRepository,
        IFrequenciaRepository $frequenciaRepository,
        ITurmaRepository $turmaRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        IMensalidadeRepository $mensalidadeRepository,
        IEstudanteRepository $estudanteRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        IProfessorRepository $professorRepository,
        IDisciplinaRepository $disciplinaRepository
    ) {
        parent::__construct();

        $this->cargaHorariaRepository = $cargaHorariaRepository;
        $this->frequenciaRepository = $frequenciaRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->estudanteRepository = $estudanteRepository;
        $this->mensalidadeRepository = $mensalidadeRepository;
        $this->turmaRepository = $turmaRepository;
        $this->professorRepository = $professorRepository;
        $this->disciplinaRepository = $disciplinaRepository;
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

        if($painel == 'professor') {
            return $this->indexAdministrators();
        }

        if($painel == 'administrativo') {
            return $this->indexAdministrators();
        }

        if ($painel == 'coordenador') {
            return $this->indexAdministrators();
        }

        return $this->router->view('dashboard/index', ['active' => 'dashboard']);
    }

    private function indexStudents() 
    {
        $data = [
            'active' => 'dashboard',
        ];  
        $pessoaAuth = $this->authUser();

        $estudante = $this->estudanteRepository
            ->studentByPersonId($pessoaAuth->id);

        if (is_null($estudante)) {
            return $this->router->view('dashboard/index', ['active' => 'dashboard']);
        }

        $estudante_turma = $this->estudanteTurmaRepository
            ->studentClassByStudentId($estudante->id);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $estudante_turma->id,
                    'class_id' => $estudante_turma->turma_id
                ]
            );
        
        if(isset($frequencias) && !empty($frequencias)) {
            $class_discipline = $this->turmaDisciplinaRepository->findById($frequencias[0]->turma_disciplina_id);
    
            $carga_horaria = $this->cargaHorariaRepository->findById($class_discipline->id);
    
            $total_faltas = $this->sumAbsences($frequencias); 
            $carga = $carga_horaria->carga ?? 80;
    
            $presenca = $carga - $total_faltas;
            $percentual_faltas = round(($total_faltas / $carga) * 100, 2);
            $percentual_presenca = round(($presenca / $carga) * 100, 2);

            array_merge($data, ['percentual_faltas' => $percentual_faltas,
            'percentual_presenca' => $percentual_presenca,
            'total_faltas' => $total_faltas,
            'presenca' => $presenca]);
        }

        $data['type_visualization'] = 'student';
        return $this->router->view(
            'dashboard/index',
            $data
        ); 
    }

    private function indexAdministrators () 
    {
        $estudante_turmas = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'active' => 1, 
                    'school_year' => Date('Y')
                ]
            );

        $discipline = $this->disciplinaRepository
            ->allDisciplines(
                [
                    'active' => 1
                ]
            );

        $class = $this->turmaRepository
            ->allClassRooms(
                [
                    'active' => 1
                ]
            );

        $professor = $this->professorRepository
            ->allTeachers(
                [
                    'active' => 1
                ]
            );

        $monthlyfees = $this->mensalidadeRepository->allMonthlyfeesGraph();
        
        $total_monthly = $this->sumMonthlyFees($monthlyfees); 

        $late_monthly = $this->sumMonthlyFees($monthlyfees, 'atrasado'); 
        
        $canceled_monthly = $this->sumMonthlyFees($monthlyfees, 'cancelado'); 
        
        $paid_monthly = $this->sumMonthlyFees($monthlyfees, 'pago'); 
        
        $pending_monthly = $this->sumMonthlyFees($monthlyfees, 'pendente'); 

        $percentual_pending = $this->calculatePercentage($pending_monthly, $total_monthly);
        $percentual_late = $this->calculatePercentage($late_monthly, $total_monthly);
        $percentual_paid = $this->calculatePercentage($paid_monthly, $total_monthly);        
        $percentual_canceled = $this->calculatePercentage($canceled_monthly, $total_monthly);

        $total_monthly -= $canceled_monthly; 

        return $this->router->view(
            'dashboard/index',
            [
                'active' => 'dashboard',
                'percentual_pending' => $percentual_pending,
                'pending_monthly' => $pending_monthly,
                'percentual_late' => $percentual_late,
                'late_monthly' => $late_monthly,
                'percentual_paid' => $percentual_paid,
                'paid_monthly' => $paid_monthly,
                'percentual_canceled' => $percentual_canceled,
                'canceled_monthly' => $canceled_monthly,
                'total_monthly' => $total_monthly,
                'estudante_turmas' => $estudante_turmas,
                'discipline' => $discipline,
                'teachers' => $professor,
                'class' => $class,
                'type_visualization' => 'admin'
            ]
        ); 
    }
}