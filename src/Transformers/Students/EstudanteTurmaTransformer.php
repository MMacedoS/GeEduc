<?php

namespace App\Transformers\Students;

use App\Models\Classrooms\Turma;
use App\Models\Student\EstudanteTurma;
use App\Repositories\Classrooms\TurmaRepository;

class EstudanteTurmaTransformer
{
    public function transform(EstudanteTurma $studentClass)
    {
        return [
            'student_id' => $studentClass->estudante_id,
            'student_name' => $this->prepareNameStudent($studentClass->estudante_id),
            'class_id' => $studentClass->turma_id,
            'class_name' => $this->prepareNameClass($studentClass->turma_id),
            'school_year' => $studentClass->ano_letivo,
            'status' => $studentClass->ativo ? 'active' : 'inactive',
        ];
    }

    public function transformCollection(array $studentClasses)
    {
        return array_map(fn($studentClass) => $this->transform($studentClass), $studentClasses);
    }

    private function prepareNameStudent($id)
    {
        if (is_null($id)) {
            return null;
        }
    }

    private function prepareNameClass($id)
    {
        if (is_null($id)) {
            return null;
        }

        $turma = TurmaRepository::getInstance()->findById($id);
        return $turma ? $turma->nome : null;
    }
}
