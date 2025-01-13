<?php

namespace App\Models\Coordination;

use App\Models\Traits\UuidTrait;

class Coordenador {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $pessoa_fisica_id;
    public $graduacao;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Coordenador {
        $coordination = new Coordenador();
        $coordination->id = $data['id'] ?? null;
        $coordination->uuid = $data['uuid'] ?? $this->generateUUID();
        $coordination->pessoa_fisica_id = $data['person_id'] ?? null;          
        $coordination->graduacao = $data['graduacao'];  
        $coordination->ativo = $data['active'] ?? 1;  
        $coordination->created_at = $data['created_at'] ?? null;
        $coordination->updated_at = $data['updated_at'] ?? null;
        return $coordination;
    }
}