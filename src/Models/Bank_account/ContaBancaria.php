<?php

namespace App\Models\Bank_account;

use App\Models\Traits\UuidTrait;

class ContaBancaria {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $convenio;
    public $agencia;
    public $conta;
    public $banco;
    public $codigo_banco;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): ContaBancaria
    {
        $conta = new ContaBancaria();
        $conta->id = $data['id'] ?? null;
        $conta->uuid = $data['uuid'] ?? $this->generateUUID();
        $conta->convenio = $data['agreement'];
        $conta->agencia = $data['branch'];
        $conta->conta = $data['account'];
        $conta->banco = $data['bank'];
        $conta->codigo_banco = $data['bank_code'];
        $conta->ativo = $data['active'] ?? '1';

        return $conta;
    }

    public function update(array $data, ContaBancaria $conta_bancaria): void
    {
        $this->convenio = $data['agreement'] ?? $conta_bancaria->convenio;
        $this->agencia = $data['branch'] ?? $conta_bancaria->agencia;
        $this->conta = $data['account'] ?? $conta_bancaria->conta;
        $this->banco = $data['bank'] ?? $conta_bancaria->banco;
        $this->codigo_banco = $data['bank_code'] ?? $conta_bancaria->codigo_banco;
        $this->ativo = $data['active'] ?? $conta_bancaria->ativo;
    }
}