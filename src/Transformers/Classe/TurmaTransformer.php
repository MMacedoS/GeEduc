<?php

namespace App\Transformers\Classe;

use App\Models\Classrooms\Turma;

class TurmaTransformer
{
    public function transform(Turma $class)
    {
        return [
            'id' => $class->id,
            'name' => $class->nome,
            'order' => $class->ordem,
            'visible' => $class->visivel,
            'shift' => $class->turno,
            'status' => $class->ativo ? 'active' : 'inactive',
        ];
    }

    public function transformCollection(array $classes): array
    {
        return array_map(fn($class) => $this->transform($class), $classes);
    }
}
