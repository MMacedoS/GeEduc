<?php

namespace App\Models\MonthlyFees;

use App\Models\Traits\UuidTrait;

class Mensalidade {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $estudante_mensalidade_id;
    public $estudante;
    public $estudante_mensalidade;
    public $mensalidades;
    public $gerou_boleto;
    public $nosso_numero;
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
        $mensalidade->estudante_mensalidade_id = $data['studante_monthly_id'];
        $mensalidade->data_vencimento = $data['expiration_date'];
        $mensalidade->dia_vencimento = $data['monthly_day'];
        $mensalidade->valor = $data['amount'];
        $mensalidade->nosso_numero = $data['our_number'];
        $mensalidade->situacao = $data['situation'] ?? 'pendente';

        return $mensalidade;
    }

    public function update(array $data, Mensalidade $mensalidade): Mensalidade
    {
        $mensalidade->estudante_mensalidade_id = $data['studante_id'] ?? $mensalidade->estudante_mensalidade_id;
        $mensalidade->data_vencimento = $data['expiration_date'] ?? $mensalidade->data_vencimento;
        $mensalidade->dia_vencimento = $data['monthly_day'] ?? $mensalidade->dia_vencimento;
        $mensalidade->valor = $data['amount'] ?? $mensalidade->valor;
        $mensalidade->situacao = $data['situation'] ?? $mensalidade->situacao;

        return $mensalidade;
    }
}