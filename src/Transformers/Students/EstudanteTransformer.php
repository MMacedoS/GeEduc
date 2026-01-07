<?php

namespace App\Transformers\Students;

use App\Models\Student\Estudante;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Student\EstudanteTurmaRepository;

class EstudanteTransformer
{
    public static function transform(Estudante $student)
    {
        if (is_array($student)) {
            $student = (object) $student;
        }

        return [
            'code' => $student->id,
            'id' => $student->uuid,
            'class' => self::studentClasse($student->id),
            'student_name' => self::prepareStudentName($student->id),
            'birth_date' => self::prepareBirthDate($student->id),
            'student_email' => self::prepareStudentEmail($student->id),
            'active' => $student->ativo,
        ];
    }

    private static function studentClasse($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }

        $estudanteTurmaRepository = EstudanteTurmaRepository::getInstance();

        $studentClasses = $estudanteTurmaRepository->studentClassByStudentId($studentId);

        if (empty($studentClasses)) {
            return null;
        }

        $transformedClasses = (object)EstudanteTurmaTransformer::transform($studentClasses);
        return $transformedClasses->class_name ?? null;
    }

    private static function prepareStudentName($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }

        $pessoaFisica = PessoaFisicaRepository::getInstance()->findByStudentId($studentId);
        return $pessoaFisica ? $pessoaFisica->nome : null;
    }

    private static function prepareBirthDate($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }

        $pessoaFisica = PessoaFisicaRepository::getInstance()->findByStudentId($studentId);
        return $pessoaFisica ? $pessoaFisica->data_nascimento : null;
    }

    private static function prepareStudentEmail($studentId)
    {
        if (is_null($studentId)) {
            return null;
        }
        $pessoaFisica = PessoaFisicaRepository::getInstance()->findByStudentId($studentId);
        return $pessoaFisica ? $pessoaFisica->email : null;
    }

    public static function transformCollection(array $students): array
    {
        return array_map(fn($student) => self::transform($student), $students);
    }
}
