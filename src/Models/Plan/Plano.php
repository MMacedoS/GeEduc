<?php

namespace App\Models\Plan;

use App\Models\Traits\UuidTrait;

class Plano {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $nome;
    public $descricao;
    public $valor;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Plano {
        $plan = new Plano();
        $plan->id = $data['id'] ?? null;
        $plan->uuid = $data['uuid'] ?? $this->generateUUID();
        $plan->nome = $data['name'];          
        $plan->descricao = $data['description'];  
        $plan->ativo = $data['active'] ?? null; 
        $plan->valor = $data['amount'];        
        $plan->created_at = $data['created_at'] ?? null;
        $plan->updated_at = $data['updated_at'] ?? null;
        return $plan;
    }
}