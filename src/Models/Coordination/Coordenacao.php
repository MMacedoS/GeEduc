<?php

namespace App\Models\Classrooms;

use App\Models\Traits\UuidTrait;

class Coordenacao {
    
    use UuidTrait;

    protected $id;
    protected $uuid;
    protected $pessoa_fisica_id;
    protected $graduacao;
    protected $ativo;
    protected $created_at;
    protected $updated_at;

    public function __construct (array $data = []) {
        if(!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->uuid = $data['uuid'] ?? $this->generateUUID();
            $this->pessoa_fisica_id = $data['pessoa_fisica_id'] ?? null;          
            $this->graduacao = $data['graduacao'];  
            $this->ativo = $data['active'] ?? null;  
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    public function create(
        array $data
    ): self {
        return new self($data);
    }
}