<?php

namespace App\Models\Work_Load;

use App\Models\Traits\UuidTrait;

class CargaHoraria{

    use UuidTrait;

    public $id;
    public $uuid;
    public $carga;
    public $ativo;
    public $created_at; 
    public $updated_at;

    public function __construct(){}

    public function create(
        array $data
    ): CargaHoraria {
        $carga_horaria = new CargaHoraria();
        $carga_horaria->id = $data['id'] ?? null;
        $carga_horaria->uuid = $data['uuid'] ?? $this->generateUUID();
        $carga_horaria->carga = $data['load'] ?? null;
        $carga_horaria->ativo = $data['active'] ?? null;
        $carga_horaria->created_at = $data['created_at'] ?? null;
        $carga_horaria->updated_at = $data['updated_at'] ?? null;
        return $carga_horaria;
    }

}