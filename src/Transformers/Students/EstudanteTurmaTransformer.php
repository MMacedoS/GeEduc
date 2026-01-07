<?php

namespace App\Transformers\Students;

use App\Models\Student\EstudanteTurma;
use App\Repositories\Classrooms\TurmaRepository;

class EstudanteTurmaTransformer
{
    public static function transform(EstudanteTurma $studentClass)
    {
        $turmaData = self::prepareClassData($studentClass->turma);
        $estudanteData = self::prepareStudentData($studentClass->estudante);

        return [
            'code' => $studentClass->id,
            'uuid' => $studentClass->uuid,
            'id' => $studentClass->uuid,
            'student_id' => $studentClass->estudante_id,
            'estudante' => $estudanteData,
            'class_id' => $studentClass->turma_id,
            'turma' => $turmaData,
            'school_year' => $studentClass->ano_letivo,
            'status' => $studentClass->ativo ? 'active' : 'inactive',
        ];
    }

    public static function transformCollection(array $studentClasses)
    {
        return array_map(fn($studentClass) => self::transform($studentClass), $studentClasses);
    }

    private static function prepareStudentData($student)
    {
        if (is_null($student)) {
            return null;
        }

        $student = getJsonToObject($student);

        return [
            'id' => $student->id ?? null,
            'uuid' => $student->uuid ?? null,
            'nome' => $student->nome ?? 'N/A',
            'matricula' => $student->matricula ?? null,
            'pessoa_fisica_id' => $student->pessoa_fisica_id ?? null
        ];
    }

    private static function prepareClassData($turma)
    {
        if (is_null($turma)) {
            return null;
        }

        $turma = getJsonToObject($turma);

        return [
            'id' => $turma->id ?? null,
            'nome' => $turma->nome ?? 'N/A',
            'visivel' => $turma->visivel ?? 1,
            'ordem' => $turma->ordem ?? 0
        ];
    }
}
