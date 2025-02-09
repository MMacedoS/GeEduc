<?php

namespace App\Models\Contracts;

use App\Models\Traits\UuidTrait;

class Contrato {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $estudante_id;
    public $ano_letivo;
    public $url_contrato;
    public $url_contrato_assinado;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Contrato {
        $contrato = new Contrato();
        $contrato->id = $data['id'] ?? null;
        $contrato->uuid = $data['uuid'] ?? $this->generateUUID();
        $contrato->estudante_id = $data['student_id'];          
        $contrato->ano_letivo = $data['school_year'];  
        $contrato->url_contrato = $data['contract_url'] ?? null; 
        $contrato->url_contrato_assinado = $data['signed_contract_url'] ?? null;        
        $contrato->created_at = $data['created_at'] ?? null;
        $contrato->updated_at = $data['updated_at'] ?? null;
        return $contrato;
    }
}