<?php

namespace App\Transformers\Teacher;

use App\Models\Teacher\ProfessorDisciplina;
use App\Repositories\Discipline\DisciplinaRepository;
use App\Repositories\Teacher\ProfessorRepository;

class ProfessorDisciplinaTransformer
{
    public function transform(ProfessorDisciplina $professorDisciplina)
    {
        return [
            'id' => $professorDisciplina->id,
            'teacher_id' => $professorDisciplina->professor_id,
            'teacher_name' => $this->prepareNameTeacher($professorDisciplina->professor_id),
            'subject_id' => $professorDisciplina->disciplina_id,
            'subject_name' => $this->prepareNameSubject($professorDisciplina->disciplina_id),
        ];
    }

    public function transformCollection(array $professorDisciplinas)
    {
        return array_map([$this, 'transform'], $professorDisciplinas);
    }

    private function prepareNameTeacher($id)
    {
        $professorRepository = ProfessorRepository::getInstance();
        $professor = $professorRepository->findById($id);

        return $professor ? $professor->name : null;
    }

    private function prepareNameSubject($id)
    {
        $disciplinaRepository = DisciplinaRepository::getInstance();
        $disciplina = $disciplinaRepository->findById($id);

        return $disciplina ? $disciplina->name : null;
    }
}
