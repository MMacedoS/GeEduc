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
    public ?string $endereco;
    public string $ativo;
    public ?string $nome_mae;
    public ?string $nome_pai;
    public string $doc;
    public string $tipo_doc;
    public ?string $genero;
    public ?string $data_nascimento;
    public ?string $telefone;
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
        $pessoa_fisica->endereco = $data['address'] ?? null;
        $pessoa_fisica->telefone = $data['phone'] ?? null;   
        $pessoa_fisica->usuario_id = $data['usuario_id'];   
        $pessoa_fisica->data_nascimento = $data['birthday'] ?? null;
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

    public function update(array $data, PessoaFisica $pessoaFisica): PessoaFisica
    {
        $pessoaFisica->usuario_id = $data['usuario_id'] ?? $pessoaFisica->usuario_id;
        $pessoaFisica->nome = $data['name'] ?? $pessoaFisica->nome;
        $pessoaFisica->email = $data['email'] ?? $pessoaFisica->email;
        $pessoaFisica->telefone = $data['phone'] ?? $pessoaFisica->telefone;
        $pessoaFisica->data_nascimento = usDate($data['birthday']) ?? $pessoaFisica->data_nascimento;
        $pessoaFisica->doc = $data['doc'] ?? $pessoaFisica->doc;
        $pessoaFisica->nome_mae = $data['mother'] ?? $pessoaFisica->nome_mae;
        $pessoaFisica->nome_pai = $data['father'] ?? $pessoaFisica->nome_pai;
        $pessoaFisica->genero = $data['gender'] ?? $pessoaFisica->genero;
        $pessoaFisica->tipo_doc = $data['type_doc'] ?? $pessoaFisica->tipo_doc;
        $pessoaFisica->ativo = $data['active'] ?? $pessoaFisica->ativo;

        return $pessoaFisica;
    }
}