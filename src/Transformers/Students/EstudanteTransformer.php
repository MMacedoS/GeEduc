<?php

namespace App\Transformers\Students;

class EstudanteTransformer
{
    public static function transform(array $student): array
    {
        return [
            'id' => $student['id'],
            'nome' => $student['nome'],
            'email' => $student['email'],
            'data_nascimento' => $student['data_nascimento'],
            'turma' => $student['turma'],
            'media_geral' => $student['media_geral'],
            'status' => $student['status'],
        ];
    }

    public static function transformCollection(array $students): array
    {
        return array_map([self::class, 'transform'], $students);
    }
}
