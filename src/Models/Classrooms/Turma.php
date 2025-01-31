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
    public $detalhes;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Turma {
        $classroom = new Turma();
        $classroom->id = $data['id'] ?? null;
        $classroom->uuid = $data['uuid'] ?? $this->generateUUID();
        $classroom->nome = $data['name'];          
        $classroom->ordem = $data['order'];  
        $classroom->ativo = $data['active'] ?? null; 
        $classroom->turno = $data['shift'];        
        $classroom->created_at = $data['created_at'] ?? null;
        $classroom->updated_at = $data['updated_at'] ?? null;
        return $classroom;
    }
}