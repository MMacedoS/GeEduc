<?php

namespace App\Models\Ticket;

use App\Models\Traits\UuidTrait;

class Boleto {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $mensalidade_id;
    public $conta_bancaria_id;
    public $valor;
    public $data;
    public $boleto;
    public $codigo_barras;
    public $pix;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): Boleto
    {
        $boleto = new Boleto();
        $boleto->id = $data['id'] ?? null;
        $boleto->uuid = $data['uuid'] ?? $this->generateUUID();
        $boleto->mensalidade_id = $data['monthly_id'];
        $boleto->conta_bancaria_id = $data['bank_id'];
        $boleto->data = $data['data'];
        $boleto->boleto = $data['ticket'];
        $boleto->valor = $data['amount'];
        $boleto->codigo_barras = $data['barcode'];
        $boleto->pix = $data['pix'];

        return $boleto;
    }
}