<?php

namespace App\Models\Activitie;

use App\Models\Traits\uuidTrait;

class Atividade {

    use uuidTrait;

    public $id;
    public $uuid;
    public $turma_disciplina_id;
    public $tipo;
    public $valor;
    public $ativo;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : Atividade{
        $atividade = new Atividade();
        $atividade->id = $data['id'] ?? null;
        $atividade->uuid = $data['uuid'] ?? $this->generateUUID();
        $atividade->turma_disciplina_id = $data['class_discipline_id'];
        $atividade->tipo = $data['type'] ?? null;
        $atividade->valor = floatval($data['value']) ?? '0.0';     
        $atividade->ativo = (int)$data['active'] ?? 1;     
        $atividade->updated_at = $data['updated_at'] ?? null;
        $atividade->created_at = $data['created_at'] ?? null;
        return $atividade;
    }

    public function update(array $data, Atividade $atividade): Atividade
    {
        $atividade->turma_disciplina_id = $data['class_discipline_id'] ?? $atividade->turma_disciplina_id;
        $atividade->tipo = $data['type'] ?? $atividade->tipo;
        $atividade->valor = floatval($data['value']) ?? $atividade->valor;
        $atividade->ativo = $data['active'] ?? $atividade->ativo;

        return $atividade;
    }

}