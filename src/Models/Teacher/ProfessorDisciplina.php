<?php

namespace App\Models\Teacher;

use App\Models\Traits\UuidTrait;

class ProfessorDisciplina {

    use UuidTrait;

    public $id;
    public $uuid;
    public $professor_id;
    public $professor;
    public $disciplina_id;
    public $disciplina;
    public $ano_letivo;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data 
    ): ProfessorDisciplina {
        $professor_disciplina = new ProfessorDisciplina();
        $professor_disciplina->id = $data['id' ] ?? null; 
        $professor_disciplina->uuid = $data['uuid' ] ?? $professor_disciplina->generateUUID(); 
        $professor_disciplina->professor_id = $data['teacher_id' ];
        $professor_disciplina->disciplina_id = $data['discipline_id' ];
        $professor_disciplina->ano_letivo = $data['school_year'];
        $professor_disciplina->ativo = $data['active'] ?? null;
        $professor_disciplina->created_at = $data['created_at'] ?? null;
        $professor_disciplina->updated_at = $data['updated_at'] ?? null;
        return $professor_disciplina;
    }

    public function update(
        array $data, ProfessorDisciplina $professor_disciplina
    ) : ProfessorDisciplina {
        $professor_disciplina->professor_id = $data['teacher_id'] ?? $professor_disciplina->professor_id;
        $professor_disciplina->disciplina_id = $data['discipline_id'] ?? $professor_disciplina->disciplina_id;
        $professor_disciplina->ano_letivo = $data['school_year'] ?? $professor_disciplina->ano_letivo;
        $professor_disciplina->ano_letivo = $data['active'] ?? $professor_disciplina->ativo;
        return $professor_disciplina;
    }


}