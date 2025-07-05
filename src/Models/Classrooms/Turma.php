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
    public $visivel;
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
        $classroom->visivel = $data['visible'];        
        $classroom->created_at = $data['created_at'] ?? null;
        $classroom->updated_at = $data['updated_at'] ?? null;
        return $classroom;
    }

    public function update(
        array $data,
        Turma $register
    ): Turma {
        $register->nome = $data['name'] ?? $register->nome;          
        $register->ordem = $data['order'] ?? $register->ordem;  
        $register->ativo = $data['active'] ?? $register->ativo; 
        $register->turno = $data['shift'] ?? $register->turno;        
        $register->visivel = $data['visible'] ?? $register->visivel;        
        return $register;
    }
}