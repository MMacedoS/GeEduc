<?php

namespace App\Transformers\Classe;

use App\Models\Classrooms\Turma;
use App\Repositories\Coordination\CoordenadorTurmaRepository;

class TurmaTransformer
{
    public function transform(Turma $class)
    {
        return [
            'code' => $class->id,
            'id' => $class->uuid,
            'name' => $class->nome,
            'order' => $class->ordem,
            'coordinators' => $this->prepareCoordinators($class->id),
            'visible' => $class->visivel,
            'shift' => $class->turno,
            'active' => $class->ativo,
        ];
    }

    private function prepareCoordinators($classId)
    {
        if (!$classId) {
            return [];
        }

        $coordenadorTurma = CoordenadorTurmaRepository::getInstance()->findByTurmaId($classId);
        $details = "";

        if ($coordenadorTurma) {
            $details = array_map(function ($coordinator) {
                return [
                    'id' => $coordinator['uuid'],
                    'name' => $coordinator['pessoa_fisica_nome'] ?? 'N/A',
                    'active' => $coordinator['ativo'],
                ];
            }, $coordenadorTurma);
        }

        return $details;
    }

    public function transformCollection(array $classes): array
    {
        return array_map(fn($class) => $this->transform($class), $classes);
    }
}
