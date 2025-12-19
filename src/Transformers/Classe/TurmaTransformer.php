<?php

namespace App\Transformers\Classe;

use App\Models\Classrooms\Turma;
use App\Repositories\Coordination\CoordenadorTurmaRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;

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
            'disciplines' => $this->prepareDisciplines($class->id),
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

    private function prepareDisciplines($classId)
    {
        if (!$classId) {
            return [];
        }

        $turmaDisciplina = TurmaDisciplinaRepository::getInstance()->allClassDisciplines(['class_id' => $classId, 'active' => 1]);

        if (!$turmaDisciplina) {
            return [];
        }

        $details = array_map(function ($td) {
            // Buscar detalhes completos
            $tdDetails = TurmaDisciplinaRepository::getInstance()->classDisciplineByParams(['id' => $td->id]);

            if (!$tdDetails) {
                return null;
            }

            $professorDisciplina = json_decode($tdDetails->professor_disciplina ?? '{}', true);
            $disciplina = $professorDisciplina['disciplina'] ?? null;
            $professor = $professorDisciplina['professor'] ?? null;
            $cargaHoraria = json_decode($tdDetails->carga_horaria ?? '{}', true);

            return [
                'id' => $td->uuid,
                'turma_disciplina_id' => $td->id,
                'disciplina_nome' => $disciplina['nome'] ?? 'N/A',
                'disciplina_id' => $disciplina['id'] ?? null,
                'professor_nome' => $professor['nome'] ?? 'N/A',
                'professor_id' => $professor['id'] ?? null,
                'carga_horaria' => $cargaHoraria['carga_horaria'] ?? 'N/A',
                'ano_letivo' => $td->ano_letivo ?? null,
            ];
        }, $turmaDisciplina);

        return array_filter($details);
    }

    public function transformCollection(array $classes): array
    {
        return array_map(fn($class) => $this->transform($class), $classes);
    }
}
