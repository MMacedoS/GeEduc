<?php

namespace App\Models\Teacher;

use App\Models\Traits\UuidTrait;

class Professor {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $graduacao;
    public $pessoa_fisica_id;
    public $pessoa_fisica;
    public $matricula;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Professor {
        $professor = new Professor();
        $professor->id = $data['id'] ?? null;
        $professor->uuid = $data['uuid'] ?? $this->generateUUID();
        $professor->graduacao = $data['graduation'];          
        $professor->pessoa_fisica_id = $data['pessoa_fisica_id'];  
        $professor->matricula = $data['matricula'] ?? null;
        $professor->ativo = $data['active'] ?? null; 
        $professor->pessoa_fisica = $data['pessoa_fisica'] ?? null;        
        $professor->created_at = $data['created_at'] ?? null;
        $professor->updated_at = $data['updated_at'] ?? null;
        return $professor;
    }
}