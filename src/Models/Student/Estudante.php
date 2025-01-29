<?php

namespace App\Models\Student;

use App\Models\Traits\UuidTrait;
use App\Utils\LoggerHelper;

class Estudante {

    use UuidTrait;

    public $id;
    public $uuid;
    public $pessoa_fisica_id;
    public $pessoa_contato_id;
    public $matricula;
    public $ativo;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : Estudante{
        
        $estudante = new Estudante();
        $estudante->id = $data['id'] ?? null;
        $estudante->uuid = $data['uuid'] ?? $this->generateUUID();
        $estudante->pessoa_fisica_id = $data['person_id'];
        $estudante->pessoa_contato_id = $data['legal_responsible_id'];
        $estudante->matricula = $data['matricula'];
        $estudante->ativo = $data['active'] ?? null;     
        $estudante->updated_at = $data['updated_at'] ?? null;
        $estudante->created_at = $data['created_at'] ?? null;
        return $estudante;
    }

    public function update(array $data, Estudante $estudante): Estudante
    {
        $estudante->pessoa_fisica_id = $data['person_id'] ?? $estudante->pessoa_fisica_id;
        $estudante->matricula = $data['matricula'] ?? $estudante->matricula;
        $estudante->pessoa_contato_id = $data['legal_responsible_id'] ?? $estudante->pessoa_contato_id;
        $estudante->ativo = $data['active'] ?? $estudante->ativo;

        return $estudante;
    }

}