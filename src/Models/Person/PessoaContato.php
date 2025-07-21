<?php

namespace App\Models\Person;

use App\Models\Traits\UuidTrait;

class PessoaContato {
    
    use UuidTrait;

    public $id;
    public string $uuid;
    public string $responsavel_legal;
    public string $pessoa_fisica_id;
    public $pessoa_fisica;
    public string $ativo;
    public $created_at;
    public $updated_at;

    public function __construct () {}

    public function create(
        array $data
    ): PessoaContato {
        $pessoa_contato = new PessoaContato();
        $pessoa_contato->id = $data['id'] ?? null;
        $pessoa_contato->uuid = $data['uuid'] ?? $this->generateUUID();
        $pessoa_contato->responsavel_legal = (int)$data['legal_responsive'] ?? 1;
        $pessoa_contato->pessoa_fisica_id = (int)$data['person_id'];
        $pessoa_contato->ativo = $data['active'] ?? 1; 
        $pessoa_contato->created_at = $data['created_at'] ?? null;
        $pessoa_contato->updated_at = $data['updated_at'] ?? null;
        return $pessoa_contato;
    }

    public function update(array $data, PessoaContato $pessoaContato): PessoaContato
    {
        $pessoaContato->pessoa_fisica_id = $data['person_id'] ?? $pessoaContato->pessoa_fisica_id;
        $pessoaContato->responsavel_legal = $data['legal_responsive'] ?? $pessoaContato->responsavel_legal;
        $pessoaContato->ativo = $data['active'] ?? $pessoaContato->ativo;

        return $pessoaContato;
    }
}