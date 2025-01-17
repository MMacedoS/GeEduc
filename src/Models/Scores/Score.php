<?php

namespace App\Models\Scores;

use App\Models\Traits\uuidTrait;

class Score {

    use uuidTrait;

    public $id;
    public $uuid;
    public $atividade_id;
    public $bimestre_id;
    public $turma_disciplina_id;
    public $nota;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : Score{
        $score = new Score();
        $score->id = $data['id'] ?? null;
        $score->uuid = $data['uuid'] ?? $this->generateUUID();
        $score->atividade_id = $data['activie_id'];
        $score->bimestre_id = $data['bimester_id'];
        $score->turma_disciplina_id = $data['class_discipline_id'];
        $score->nota = floatval($data['value']) ?? '0.0';     
        $score->updated_at = $data['updated_at'] ?? null;
        $score->created_at = $data['created_at'] ?? null;
        return $score;
    }

    // public function update(array $data, Note $note): Note
    // {
    //     $note->turma_disciplina_id = $data['class_discipline_id'] ?? $note->turma_disciplina_id;
    //     $note->tipo = $data['type'] ?? $note->tipo;
    //     $note->valor = floatval($data['value']) ?? $note->valor;
    //     $note->ativo = $data['active'] ?? $note->ativo;

    //     return $note;
    // }

}