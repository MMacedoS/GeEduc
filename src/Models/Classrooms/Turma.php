<?php

namespace App\Models\Classrooms;

use App\Models\Traits\UuidTrait;

class Turma {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $nome;
    public $ordem;
    public $turno;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Turma {
        $shift = new Turma();
        $shift->id = $data['id'] ?? null;
        $shift->uuid = $data['uuid'] ?? $this->generateUUID();
        $shift->nome = $data['name'];          
        $shift->ordem = $data['order'];  
        $shift->ativo = $data['active'] ?? null; 
        $shift->turno = $data['shift'];        
        $shift->created_at = $data['created_at'] ?? null;
        $shift->updated_at = $data['updated_at'] ?? null;
        return $shift;
    }
}