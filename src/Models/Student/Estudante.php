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
    public ?string $pessoa_fisica;
    public $matricula;
    public $ativo;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : Estudante{
        
        $estudante = new Estudante();
        if (isset($data['id'])) {
            $estudante->id = $data['id'];
        }
        if (!isset($data['uuid'])) {
            $estudante->uuid = $data['uuid'] ?? $this->generateUUID();
        }
        if (isset($data['person_id'])) {
            $estudante->pessoa_fisica_id = $data['person_id'];
        }
        if (isset($data['legal_responsible_id'])) {
            $estudante->pessoa_contato_id = $data['legal_responsible_id'];
        }
        if (isset($data['matricula'])) {
            $estudante->matricula = $data['matricula'];
        }
        if (isset($data['active'])) {
            $estudante->ativo = $data['active'] ?? null;   
        }  
        if (isset($data['updated_at'])) {
            $estudante->updated_at = $data['updated_at'] ?? null;
        }
        if (isset($data['created_at'])) {
            $estudante->created_at = $data['created_at'] ?? null;
        }
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