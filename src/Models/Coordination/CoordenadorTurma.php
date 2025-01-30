<?php

namespace App\Models\Coordination;

use App\Models\Traits\UuidTrait;

class CoordenadorTurma {

    use UuidTrait;
    
    public $id;
    public $uuid;
    public $coordenador_id;
    public $turma_id;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): CoordenadorTurma {
        $coordination = new CoordenadorTurma();
        $coordination->id = $data['id'] ?? null;
        $coordination->uuid = $data['uuid'] ?? $this->generateUUID();
        $coordination->coordenador_id = $data['coordenador_id'] ?? null;          
        $coordination->turma_id = $data['turma_id'];
        $coordination->created_at = $data['created_at'] ?? null;
        $coordination->updated_at = $data['updated_at'] ?? null;
        return $coordination;
    }
}