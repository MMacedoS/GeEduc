<?php

namespace App\Transformers\Students;

use App\Models\Student\Estudante;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Student\EstudanteTurmaRepository;

class EstudanteTransformer
{
    public function transform(Estudante $student)
    {
        if (is_array($student)) {
            $student = (object) $student;
        }

        return [
            'code' => $student->id,
            'id' => $student->uuid,
            'class' => $this->studentClasse($student->id),
            'student_name' => $this->prepareStudentName($student->id),
            'birth_date' => $this->prepareBirthDate($student->id),
            'student_email' => $this->prepareStudentEmail($student->id),
            'active' => $student->ativo,
        ];
    }

    private function studentClasse($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }

        $estudanteTurmaRepository = EstudanteTurmaRepository::getInstance();

        $estudanteTurmaTransformer = new EstudanteTurmaTransformer();

        $studentClasses = $estudanteTurmaRepository->studentClassByStudentId($studentId);

        if (empty($studentClasses)) {
            return null;
        }

        $transformedClasses = (object)$estudanteTurmaTransformer->transform($studentClasses);
        return $transformedClasses->class_name ?? null;
    }

    private function prepareStudentName($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }

        $pessoaFisica = PessoaFisicaRepository::getInstance()->findByStudentId($studentId);
        return $pessoaFisica ? $pessoaFisica->nome : null;
    }

    private function prepareBirthDate($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }

        $pessoaFisica = PessoaFisicaRepository::getInstance()->findByStudentId($studentId);
        return $pessoaFisica ? $pessoaFisica->data_nascimento : null;
    }

    private function prepareStudentEmail($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }
        $pessoaFisica = PessoaFisicaRepository::getInstance()->findByStudentId($studentId);
        return $pessoaFisica ? $pessoaFisica->email : null;
    }

    public function transformCollection(array $students): array
    {
        return array_map(fn($student) => $this->transform($student), $students);
    }
}
