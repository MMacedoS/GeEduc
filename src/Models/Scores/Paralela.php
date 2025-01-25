<?php

namespace App\Models\Scores;

use App\Models\Traits\uuidTrait;

class Paralela {

    use uuidTrait;

    public $id;
    public $uuid;
    public $periodo_id;
    public $estudante_turma_id;
    public $turma_disciplina_id;
    public $nota;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ):Paralela {
        $paralela = new Paralela();
        $paralela->id = $data['id'] ?? null;
        $paralela->uuid = $data['uuid'] ?? $this->generateUUID();
        $paralela->periodo_id = $data['period_id'] ?? null;
        $paralela->estudante_turma_id = $data['class_student_id'];     
        $paralela->turma_disciplina_id = $data['class_discipline_id'];
        $paralela->nota = $data['nota'] ?? '0';  
        $paralela->updated_at = $data['updated_at'] ?? null;
        $paralela->created_at = $data['created_at'] ?? null;
        return $paralela;
    }

    public function update(array $data, Paralela $paralela): Paralela
    {
        $paralela->periodo_id = $data['period_id'] ?? $paralela->periodo_id;
        $paralela->turma_disciplina_id = $data['class_discipline_id'] ?? $paralela->turma_disciplina_id;
        $paralela->estudante_turma_id = $data['class_student_id'] ?? $paralela->estudante_turma_id;
        $paralela->nota = $data['nota'] ?? $paralela->nota;

        return $paralela;
    }

}