<?php

namespace App\Models\Frequencies;

use App\Models\Traits\uuidTrait;

class Frequencia {

    use uuidTrait;

    public $id;
    public $uuid;
    public $turma_disciplina_id;
    public $bimestre_id;
    public $turma_estudante_id;
    public $faltas;
    public $data;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : Frequencia{
        $frequencia = new Frequencia();
        $frequencia->id = $data['id'] ?? null;
        $frequencia->uuid = $data['uuid'] ?? $this->generateUUID();
        $frequencia->turma_disciplina_id = $data['class_discipline_id'];
        $frequencia->bimestre_id = $data['bimester_id'] ?? null;
        $frequencia->turma_estudante_id = $data['class_student_id'] ?? '0.0';     
        $frequencia->faltas = (int)$data['frequency'] ?? 1;  
        $frequencia->data = $data['data'] ?? null;  
        $frequencia->updated_at = $data['updated_at'] ?? null;
        $frequencia->created_at = $data['created_at'] ?? null;
        return $frequencia;
    }

    public function update(array $data, Frequencia $frequencia): Frequencia
    {
        $frequencia->turma_disciplina_id = $data['class_discipline_id'] ?? $frequencia->turma_disciplina_id;
        $frequencia->bimestre_id = $data['bimester_id'] ?? $frequencia->bimestre_id;
        $frequencia->turma_estudante_id = $data['class_student_id'] ?? $frequencia->turma_estudante_id;
        $frequencia->faltas = $data['frequency'] ?? $frequencia->faltas;
        $frequencia->data = $data['data'] ?? $frequencia->data;

        return $frequencia;
    }

}