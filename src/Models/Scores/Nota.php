<?php

namespace App\Models\Scores;

use App\Models\Traits\uuidTrait;

class nota {

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
    ) : Nota{
        $nota = new Nota();
        $nota->id = $data['id'] ?? null;
        $nota->uuid = $data['uuid'] ?? $this->generateUUID();
        $nota->atividade_id = $data['atividade_id'];
        $nota->bimestre_id = $data['bimester_id'];
        $nota->estudante_turma_id = $data['estudante_turma_id'];
        $nota->nota = floatval($data['nota']) ?? '0.0';     
        $nota->updated_at = $data['updated_at'] ?? null;
        $nota->created_at = $data['created_at'] ?? null;
        return $nota;
    }
}