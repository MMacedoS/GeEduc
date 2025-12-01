<?php

namespace App\Controllers\v1\GradeReport;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Interfaces\Activitie\IAtividadeRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Discipline\IDisciplinaRepository;
use App\Interfaces\Frequencies\IFrequenciaRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Recuperation\IRecuperacaoRepository;
use App\Interfaces\Scores\IBoletimRepository;
use App\Interfaces\Scores\INotaRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Request\Request;
use App\Transformers\Classe\TurmaDisciplinaTransformer;

class GradeReportController extends Controller
{
    use GenericTrait;
    protected $atividadeRepository;
    protected $turmaDisciplinaRepository;
    protected $turmaDisciplinaTransformer;
    protected $turmaRepository;
    protected $estudanteRepository;
    protected $frequenciaRepository;
    protected $estudanteTurmaRepository;
    protected $periodoRepository;
    protected $notaRepository;
    protected $boletimRepository;
    protected $disciplinaRepository;
    protected $recuperacaoRepository;

    public function __construct(
        IAtividadeRepository $atividadeRepository,
        ITurmaDisciplinaRepository $turmaDisciplinaRepository,
        ITurmaRepository $turmaRepository,
        IEstudanteRepository $estudanteRepository,
        IFrequenciaRepository $frequenciaRepository,
        IEstudanteTurmaRepository $estudanteTurmaRepository,
        TurmaDisciplinaTransformer $turmaDisciplinaTransformer,
        IDisciplinaRepository $disciplinaRepository,
        IPeriodoRepository $periodoRepository,
        INotaRepository $notaRepository,
        IBoletimRepository $boletimRepository,
        IRecuperacaoRepository $recuperacaoRepository
    ) {
        parent::__construct();
        $this->frequenciaRepository = $frequenciaRepository;
        $this->atividadeRepository = $atividadeRepository;
        $this->turmaDisciplinaRepository = $turmaDisciplinaRepository;
        $this->estudanteTurmaRepository = $estudanteTurmaRepository;
        $this->periodoRepository = $periodoRepository;
        $this->estudanteRepository = $estudanteRepository;
        $this->notaRepository = $notaRepository;
        $this->disciplinaRepository = $disciplinaRepository;
        $this->turmaRepository = $turmaRepository;
        $this->boletimRepository = $boletimRepository;
        $this->recuperacaoRepository = $recuperacaoRepository;
        $this->turmaDisciplinaTransformer = $turmaDisciplinaTransformer;
    }

    public function indexTeacher(Request $request, string $class_discipline_id)
    {
        $turma_disciplina = $this->turmaDisciplinaRepository
            ->findByUuid($class_discipline_id);

        $estudantes = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'class_id' => $turma_disciplina->turma_id,
                    'school_year' => Date('Y')
                ]
            );

        $atividades = $this->atividadeRepository->allActivities(['class_discipline_id' => $turma_disciplina->id]);

        $notas = $this->notaRepository->allScores([
            'class_discipline_id' => $turma_disciplina->id,
        ]);

        $recuperacoes = $this->recuperacaoRepository->all(
            ['class_discipline_id' => $turma_disciplina->id]
        );

        $periodos = $this->periodoRepository->all(['active' => '1']);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'class_discipline_id' => $turma_disciplina->id,
                    'class_id' => $turma_disciplina->turma_id,
                ]
            );

        return $this->router->view(
            'teacher/reports/bimesterGrades/index',
            [
                'turma_disciplina' => $this->turmaDisciplinaTransformer->transform($turma_disciplina),
                'estudantes' => $estudantes,
                'atividades' => $atividades,
                'periodos' => $periodos,
                'notas' => $notas,
                'recuperacoes' => $recuperacoes,
                'frequencias' => $frequencias
            ]
        );
    }

    public function indexStudents(Request $request, string $student_class_id)
    {
        $studentClass = $this->estudanteTurmaRepository->findByUuid($student_class_id);

        $allStudentClass = $this->estudanteTurmaRepository
            ->allClassStudents(
                ["student_id" => $studentClass->estudante_id]
            )[0];

        $periodos = $this->periodoRepository->all(['active' => '1']);

        $allDisciplines = $this->disciplinaRepository->findAllDisciplineByClassID($studentClass->turma_id);

        $notas = $this->notaRepository->allScores([
            'student_class_id' => $studentClass->id,
        ]);

        $frequencias = $this->frequenciaRepository
            ->allFrequencies(
                [
                    'student_id' => $studentClass->id,
                    'class_id' => $studentClass->turma_id
                ]
            );

        return $this->router->view(
            'student/reports/bimesterGrades/index',
            [
                'allStudentClass' => $allStudentClass,
                'allDisciplines' => $allDisciplines,
                'periodos' => $periodos,
                'frequencias' => $frequencias,
                'notas' => $notas,
            ]
        );
    }

    private function generateArrayIDsForWhere(array $params = []): string
    {
        $ids = array_map(fn($param) => $param->id, $params);
        return implode(',', $ids);
    }

    public function boletins(Request $request, string $turma_id)
    {
        $class = $this->turmaRepository->findByUuid($turma_id);

        $periodos = $this->periodoRepository->all(['active' => '1']);

        $all_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                [
                    'class_id' => $class->id,
                    'academic_year' => Date('Y')
                ]
            );

        $all_students = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'class_id' => $class->id,
                    'school_year' => Date('Y')
                ]
            );

        return $this->router->view(
            'reports/boletim',
            [
                'allStudentClass' => $all_students,
                'allDisciplines' => $all_disciplines,
                'periodos' => $periodos,
                'notaService' => $this->boletimRepository
            ]
        );
    }

    public function allTicketsDetails(Request $request, string $turma_id)
    {
        $class = $this->turmaRepository->findByUuid($turma_id);

        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        $all_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                [
                    'class_id' => $class->id,
                    'academic_year' => Date('Y')
                ]
            );

        $all_students = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'class_id' => $class->id,
                    'school_year' => Date('Y')
                ]
            );

        return $this->router->view(
            'reports/details',
            [
                'allStudentClass' => $all_students,
                'allDisciplines' => $all_disciplines,
                'periodos' => $periodos,
                'notaService' => $this->boletimRepository,
                'activitiesService' => $this->atividadeRepository
            ]
        );
    }

    public function ticketsDetailsByStudent(Request $request, string $turma_id, string $estudante_id)
    {
        $class = $this->turmaRepository->findByUuid($turma_id);

        $periodos = array_reverse($this->periodoRepository->all(['active' => '1']));

        $all_disciplines = $this->turmaDisciplinaRepository
            ->allClassDisciplines(
                [
                    'class_id' => $class->id,
                    'academic_year' => Date('Y')
                ]
            );

        $all_students = $this->estudanteTurmaRepository
            ->allClassStudents(
                [
                    'class_id' => $class->id,
                    'uuid' => $estudante_id,
                    'school_year' => Date('Y')
                ]
            );

        return $this->router->view(
            'reports/details',
            [
                'allStudentClass' => $all_students,
                'allDisciplines' => $all_disciplines,
                'periodos' => $periodos,
                'notaService' => $this->boletimRepository,
                'activitiesService' => $this->atividadeRepository
            ]
        );
    }
}
