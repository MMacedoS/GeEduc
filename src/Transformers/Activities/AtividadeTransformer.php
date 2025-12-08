<?php

namespace App\Transformers\Activities;

use App\Models\Activitie\Atividade;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Transformers\Classe\TurmaDisciplinaTransformer;

class AtividadeTransformer
{
    public function transform(Atividade $atividade)
    {
        return (object)[
            'id' => $atividade->uuid,
            'code' => $atividade->id,
            'title' => $atividade->tipo,
            'class_room' => $this->prepareClassRoom($atividade->turma_disciplina_id),
            'total_points' => $atividade->valor,
            'active' => $atividade->ativo,
            'created_at' => $atividade->created_at,
            'updated_at' => $atividade->updated_at,
        ];
    }

    public function transformCollection(array $atividades)
    {
        return array_map([$this, 'transform'], $atividades);
    }

    private function prepareClassRoom($id)
    {
        if (is_null($id)) {
            return null;
        }

        $turmaDisciplinaRepository = TurmaDisciplinaRepository::getInstance();
        $turmaDisciplina = $turmaDisciplinaRepository->findById($id);
        if (!$turmaDisciplina) {
            return null;
        }
        $turmaDisciplinaTransformer = new TurmaDisciplinaTransformer();
        return $turmaDisciplinaTransformer->transform($turmaDisciplina);
    }
}
