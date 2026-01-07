<?php

namespace App\Transformers\Teacher;

use App\Models\Teacher\ProfessorDisciplina;
use App\Repositories\Discipline\DisciplinaRepository;
use App\Repositories\Teacher\ProfessorRepository;

class ProfessorDisciplinaTransformer
{
    public static function transform(ProfessorDisciplina $professorDisciplina)
    {
        return (object) [
            'code' => $professorDisciplina->id,
            'id' => $professorDisciplina->uuid,
            'teacher_id' => $professorDisciplina->professor_id,
            'teacher_name' => self::prepareNameTeacher($professorDisciplina->professor_id),
            'subject_id' => $professorDisciplina->disciplina_id,
            'subject_name' => self::prepareNameSubject($professorDisciplina->disciplina_id),
            'active' => $professorDisciplina->ativo,
            'created_at' => $professorDisciplina->created_at,
            'updated_at' => $professorDisciplina->updated_at,
        ];
    }

    public static function transformCollection(array $professorDisciplinas)
    {
        return array_map(fn($pd) => self::transform($pd), $professorDisciplinas);
    }

    private static function prepareNameTeacher($id)
    {
        $professorRepository = ProfessorRepository::getInstance();
        $professor = $professorRepository->findById($id);

        $professorData = (object)ProfessorTransformer::transform($professor);

        return $professorData ? $professorData->name : null;
    }

    private static function prepareNameSubject($id)
    {
        $disciplinaRepository = DisciplinaRepository::getInstance();
        $disciplina = $disciplinaRepository->findById($id);

        return $disciplina ? $disciplina->nome : null;
    }
}
