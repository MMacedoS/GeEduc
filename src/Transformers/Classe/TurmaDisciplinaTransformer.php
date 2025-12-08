<?php

namespace App\Transformers\Classe;

use App\Models\Classrooms\TurmaDisciplina;
use App\Repositories\Classrooms\TurmaRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Repositories\Work_Load\CargaHorariaRepository;
use App\Transformers\Teacher\ProfessorDisciplinaTransformer;

class TurmaDisciplinaTransformer
{
    public function transform(TurmaDisciplina $turmaDisciplina)
    {
        return (object)[
            'code' => $turmaDisciplina->id,
            'id' => $turmaDisciplina->uuid,
            'class_name' => $this->prepareTurmaName($turmaDisciplina->turma_id),
            'class_id' => $turmaDisciplina->turma_id,
            'class_uuid' => $this->prepareTurmaId($turmaDisciplina->turma_id),
            'subject_id' => $turmaDisciplina->professor_disciplina_id,
            'subject_name' => $this->prepareTurmaDisciplinaName(
                $turmaDisciplina
                    ->professor_disciplina_id
            )->subject_name ?? null,
            'teacher_name' => $this->prepareTurmaDisciplinaName(
                $turmaDisciplina
                    ->professor_disciplina_id
            )->teacher_name ?? null,
            'school_year' => $turmaDisciplina->ano_letivo,
            'workload' => $this->prepareWorkload($turmaDisciplina->carga_horaria_id),
            'active' => $turmaDisciplina->ativo,
            'created_at' => $turmaDisciplina->created_at,
            'updated_at' => $turmaDisciplina->updated_at,
        ];
    }

    public function transformCollection(array $turmaDisciplinas)
    {
        return array_map(fn($class) => $this->transform($class), $turmaDisciplinas);
    }

    private function prepareTurmaDisciplinaName($id)
    {
        $professorDisciplinaRepository = ProfessorDisciplinaRepository::getInstance();
        $professorDisciplina = $professorDisciplinaRepository->findById($id);
        $professorDisciplinaTransformer = new ProfessorDisciplinaTransformer();

        return (object)$professorDisciplinaTransformer->transform($professorDisciplina);
    }

    private function prepareTurmaName($id)
    {
        if (is_null($id)) {
            return null;
        }

        $turma = TurmaRepository::getInstance()->findById($id);
        return $turma ? $turma->nome : null;
    }

    private function prepareTurmaId($id)
    {
        if (is_null($id)) {
            return null;
        }

        $turma = TurmaRepository::getInstance()->findById($id);
        return $turma ? $turma->uuid : null;
    }

    private function prepareWorkload($id)
    {
        $cargaHorariaRepository = CargaHorariaRepository::getInstance();
        $cargaHoraria = $cargaHorariaRepository->findById($id);
        return $cargaHoraria ? $cargaHoraria->carga : null;
    }
}
