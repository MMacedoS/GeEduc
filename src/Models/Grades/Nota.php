<?php

namespace App\Models\Grades;

use App\Models\Traits\uuidTrait;

class Nota {

    use uuidTrait;

    public $id;
    public $uuid;
    public $atividade_id;
    public $bimestre_id;
    public $estudante_turma_id;
    public $nota;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ):Nota {
        $nota = new Nota();
        $nota->id = $data['id'] ?? null;
        $nota->uuid = $data['uuid'] ?? $this->generateUUID();
        $nota->atividade_id = $data['activity_id'] ?? null;
        $nota->bimestre_id = $data['bimester_id'] ?? null;
        $nota->estudante_turma_id = $data['class_student_id'] ?? null;     
        $nota->nota = (int)$data['grade'] ?? '0';  
        $nota->updated_at = $data['updated_at'] ?? null;
        $nota->created_at = $data['created_at'] ?? null;
        return $nota;
    }

    public function update(array $data, Nota $nota): Nota
    {
        $nota->atividade_id = $data['activity_id'] ?? $nota->atividade_id;
        $nota->bimestre_id = $data['bimester_id'] ?? $nota->bimestre_id;
        $nota->estudante_turma_id = $data['class_student_id'] ?? $nota->estudante_turma_id;
        $nota->nota = $data['grade'] ?? $nota->nota;

        return $nota;
    }

}