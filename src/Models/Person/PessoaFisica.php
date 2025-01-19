<?php

namespace App\Models\Person;

use App\Models\Traits\UuidTrait;

class PessoaFisica {
    
    use UuidTrait;

    public $id;
    public string $uuid;
    public string $nome;
    public string $email;
    public string $usuario_id;
    public string $endereco;
    public string $ativo;
    public string $nome_mae;
    public string $nome_pai;
    public string $doc;
    public string $tipo_doc;
    public string $genero;
    public string $telefone;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): PessoaFisica {
        $pessoa_fisica = new PessoaFisica();
        $pessoa_fisica->id = $data['id'] ?? null;
        $pessoa_fisica->uuid = $data['uuid'] ?? $this->generateUUID();
        $pessoa_fisica->nome = $data['name'];
        $pessoa_fisica->email = $data['email'];
        $pessoa_fisica->endereco = $data['address'];
        $pessoa_fisica->telefone = $data['phone'];   
        $pessoa_fisica->usuario_id = $data['usuario_id'];   
        $pessoa_fisica->nome_mae = $data['mother'] ?? null;   
        $pessoa_fisica->nome_pai = $data['father'] ?? null;   
        $pessoa_fisica->genero = $data['gender'] ?? null;     
        $pessoa_fisica->doc = $data['doc'];   
        $pessoa_fisica->tipo_doc = $data['type_doc'];   
        $pessoa_fisica->ativo = (int)$data['active']; 
        $pessoa_fisica->created_at = $data['created_at'] ?? null;
        $pessoa_fisica->updated_at = $data['updated_at'] ?? null;
        return $pessoa_fisica;
    }
}