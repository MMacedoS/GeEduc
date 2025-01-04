<?php

namespace App\Models\MonthlyFees;

use App\Models\Traits\UuidTrait;

class Mensalidade {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $estudante_id;
    public $estudante;
    public $plano_id;
    public $plano;
    public $valor;
    public $data_vencimento;
    public $dia_vencimento;
    public $situacao;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): Mensalidade
    {
        $mensalidade = new Mensalidade();
        $mensalidade->id = $data['id'] ?? null;
        $mensalidade->uuid = $data['uuid'] ?? $this->generateUUID();
        $mensalidade->estudante_id = $data['studante_id'];
        $mensalidade->plano_id = $data['plan_id'];
        $mensalidade->data_vencimento = $data['expiration_date'];
        $mensalidade->dia_vencimento = $data['expiration_day'];
        $mensalidade->valor = $data['amount'];
        $mensalidade->situacao = $data['situation'] ?? '1';

        return $mensalidade;
    }

    public function update(array $data, Mensalidade $mensalidade): Mensalidade
    {
        $mensalidade->estudante_id = $data['studante_id'] ?? $mensalidade->estudante_id;
        $mensalidade->plano_id = $data['plan_id'] ?? $mensalidade->plano_id;
        $mensalidade->data_vencimento = $data['expiration_date'] ?? $mensalidade->data_vencimento;
        $mensalidade->dia_vencimento = $data['expiration_day'] ?? $mensalidade->dia_vencimento;
        $mensalidade->valor = $data['amount'] ?? $mensalidade->valor;
        $mensalidade->situacao = $data['situation'] ?? $mensalidade->situacao;

        return $mensalidade;
    }
}