<?php

namespace App\Models\Activitie;

use App\Models\Traits\uuidTrait;

class Note {

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
    ) : Note{
        $note = new Note();
        $note->id = $data['id'] ?? null;
        $note->uuid = $data['uuid'] ?? $this->generateUUID();
        $note->atividade_id = $data['activie_id'];
        $note->bimestre_id = $data['bimester_id'];
        $note->turma_disciplina_id = $data['class_discipline_id'];
        $note->nota = floatval($data['value']) ?? '0.0';     
        $note->updated_at = $data['updated_at'] ?? null;
        $note->created_at = $data['created_at'] ?? null;
        return $note;
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