<?php

namespace App\Models\Student;

use App\Models\Traits\UuidTrait;

class EstudanteMensalidade {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $estudante_id;
    public $plano_id;
    public $dia_mensalidade;
    public $ativo;
    public $desconto;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): EstudanteMensalidade
    {
        $mensalidade = new EstudanteMensalidade();
        $mensalidade->id = $data['id'] ?? null;
        $mensalidade->uuid = $data['uuid'] ?? $this->generateUUID();
        $mensalidade->estudante_id = $data['student_id'];
        $mensalidade->desconto = $data['discont'];
        $mensalidade->dia_mensalidade = $data['monthly_day'];
        $mensalidade->plano_id = $data['plan_id'];
        $mensalidade->ativo = $data['active'] ?? '1';

        return $mensalidade;
    }

    public function update(array $data, EstudanteMensalidade $mensalidade): EstudanteMensalidade
    {
        $mensalidade->estudante_id = $data['studante_id'] ?? $mensalidade->estudante_id;
        $mensalidade->plano_id = $data['plan_id'] ?? $mensalidade->plano_id;
        $mensalidade->dia_mensalidade = $data['monthly_day'] ?? $mensalidade->dia_mensalidade;
        $mensalidade->desconto = $data['discont'] ?? $mensalidade->desconto;
        $mensalidade->ativo = $data['active'] ?? $mensalidade->ativo;

        return $mensalidade;
    }
}