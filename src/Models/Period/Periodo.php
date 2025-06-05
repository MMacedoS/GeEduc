<?php

namespace App\Models\Period;

use App\Models\Traits\UuidTrait;

class Periodo{

    use UuidTrait;

    public $id;
    public $uuid;
    public $periodo;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): Periodo{
        $periodo = new Periodo();
        $periodo->id = $data['id'] ?? null;
        $periodo->uuid = $data['uuid'] ?? $this->generateUUID();
        $periodo->periodo = $data['period'] ?? null;
        $periodo->created_at = $data['created_at'] ?? null;
        $periodo->updated_at = $data['updated_at'] ?? null;
        return $periodo;
    }

    public function update(array $data, Periodo $periodo): Periodo
    {
        $periodo->periodo = $data['periodo'] ?? $periodo->periodo;
        $periodo->ativo = $data['active'] ?? $periodo->ativo;

        return $periodo;
    }
}