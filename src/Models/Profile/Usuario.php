<?php

namespace App\Models\Profile;

use App\Models\Traits\UuidTrait;

class Usuario {
    
    use UuidTrait;

    public $id;
    public $uuid;
    public $nome;
    public $email;
    public $senha;
    public $ativo;
    public $painel;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): Usuario {
        $user = new Usuario();
        $user->id = $data['id'] ?? null;
        $user->uuid = $data['uuid'] ?? $this->generateUUID();
        $user->nome = $data['name'];
        $user->email = $data['email'];
        $user->painel = $data['sector'];
        $user->ativo = $data['status'] ?? 1; 
        $user->senha = $data['password'];        
        $user->created_at = $data['created_at'] ?? null;
        $user->updated_at = $data['updated_at'] ?? null;
        return $user;
    }
}