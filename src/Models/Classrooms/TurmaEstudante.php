<?php

namespace App\Models\Classrooms;

use App\Models\Traits\UuidTrait;

class TurmaEstudante {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $turma_id;
    public $turma;
    public $estudante_id;
    public $estudante;
    public $ano_letivo;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): TurmaEstudante {
        $turma_estudante = new TurmaEstudante();
        $turma_estudante->id = $data['id'] ?? null;
        $turma_estudante->uuid = $data['uuid'] ?? $this->generateUUID();
        $turma_estudante->turma_id = $data['class_id'];          
        $turma_estudante->estudante_id = $data['student_id'];  
        $turma_estudante->ativo = $data['active'] ?? null;    
        $turma_estudante->ano_letivo = $data['school_year'];
        $turma_estudante->created_at = $data['created_at'] ?? null;
        $turma_estudante->updated_at = $data['updated_at'] ?? null;
        return $turma_estudante;
    }

    public function update(array $data): void
    {
        $this->turma_id = $data['class_id'] ?? $this->turma_id;
        $this->estudante_id = $data['student_id'] ?? $this->estudante_id;
        $this->ano_letivo = $data['school_year'] ?? $this->ano_letivo;
        $this->ativo = $data['active'] ?? $this->ativo;
    }
}