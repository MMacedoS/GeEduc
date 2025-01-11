<?php

namespace App\Models\Classrooms;

use App\Models\Traits\uuidTrait;
use App\Utils\LoggerHelper;

class TurmaDisciplina {

    use uuidTrait;

    public $id;
    public $uuid;
    public $turma_id;
    public $turma;
    public $carga_horaria_id;
    public $carga_horaria;
    public $professor_disciplina_id;
    public $professor_disciplina;
    public $ativo;
    public $ano_letivo;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : TurmaDisciplina{
        $turma_disciplina = new TurmaDisciplina();
        $turma_disciplina->id = $data['id'] ?? null;
        $turma_disciplina->uuid = $data['uuid'] ?? $this->generateUUID();
        $turma_disciplina->turma_id = $data['class_id'];
        $turma_disciplina->carga_horaria_id = $data['work_load_id'] ?? null;
        $turma_disciplina->professor_disciplina_id = $data['teacher_discipline_id'] ?? null;     
        $turma_disciplina->ativo = $data['active'] ?? null;     
        $turma_disciplina->ano_letivo = $data['academic_year'] ?? null;     
        $turma_disciplina->updated_at = $data['updated_at'] ?? null;
        $turma_disciplina->created_at = $data['created_at'] ?? null;
        return $turma_disciplina;
    }

    public function update(array $data, TurmaDisciplina $turma_disciplina): TurmaDisciplina
    {
        $turma_disciplina->turma_id = $data['class_id'] ?? $turma_disciplina->turma_id;
        $turma_disciplina->carga_horaria_id = $data['work_load_id'] ?? $turma_disciplina->carga_horaria_id;
        $turma_disciplina->professor_disciplina_id = $data['teacher_discipline_id'] ?? $turma_disciplina->professor_disciplina_id;
        $turma_disciplina->ativo = $data['active'] ?? $turma_disciplina->ativo;

        return $turma_disciplina;
    }

}