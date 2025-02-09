<?php

namespace App\Models\Contracts;

use App\Models\Traits\UuidTrait;

class Contrato {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $estudante_id;
    public $ano_letivo;
    public $document_id;
    public $quantidade_assinaturas;
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
        $contrato->document_id = $data['document_id']; 
        $contrato->quantidade_assinaturas = $data['quantity_assigned'] ?? 0;    
        $contrato->created_at = $data['created_at'] ?? null;
        $contrato->updated_at = $data['updated_at'] ?? null;
        return $contrato;
    }
}