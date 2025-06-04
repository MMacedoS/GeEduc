<?php

namespace App\Models\Student;

use App\Models\Traits\UuidTrait;

class EstudanteTurma {
    
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
    ): EstudanteTurma {
        $estudante_turma = new EstudanteTurma();
        if (isset($data['id'])) {
            $estudante_turma->id = $data['id'];
        }
        if (!isset($data['uuid'])) {
            $estudante_turma->uuid = $estudante_turma->generateUUID();
        }
        if (isset($data['class_id'])) {
            $estudante_turma->turma_id = $data['class_id'];
        }
        if (isset($data['student_id'])) {
            $estudante_turma->estudante_id = $data['student_id'];
        }
        if (isset($data['active'])) {
            $estudante_turma->ativo = $data['active'];
        }
        if (isset($data['school_year'])) {
            $estudante_turma->ano_letivo = $data['school_year'];
        }
        if (isset($data['created_at'])) {
            $estudante_turma->created_at = $data['created_at'];
        }
        if (isset($data['updated_at'])) {
            $estudante_turma->updated_at = $data['updated_at'];
        }

        return $estudante_turma;
    }

    public function update(array $data, EstudanteTurma $estudante_turma): EstudanteTurma
    {
        $estudante_turma->turma_id = $data['class_id'] ?? $estudante_turma->turma_id;
        $estudante_turma->estudante_id = $data['student_id'] ?? $estudante_turma->estudante_id;
        $estudante_turma->ano_letivo = $data['school_year'] ?? $estudante_turma->ano_letivo;
        $estudante_turma->ativo = $data['active'] ?? $estudante_turma->ativo;

        return $estudante_turma;
    }
}