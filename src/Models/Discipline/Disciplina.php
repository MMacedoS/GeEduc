<?php

namespace App\Models\Discipline;

use App\Models\Traits\UuidTrait;

class Disciplina {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $name;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Disciplina {
        $disciplina = new Disciplina();
        $disciplina->id = $data['id'] ?? null;
        $disciplina->uuid = $data['uuid'] ?? $this->generateUUID();
        $disciplina->nome = $data['name'] ?? null;
        $disciplina->ativo = $data['active'] ?? null; 
        $disciplina->created_at = $data['created_at'] ?? null;
        $disciplina->updated_at = $data['updated_at'] ?? null;
        return $disciplina;
    }
}