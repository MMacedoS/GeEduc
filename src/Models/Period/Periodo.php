<?php

namespace App\Models\Period;

use App\Models\Traits\UuidTrait;

class Periodo{

    use UuidTrait;

    public $id;
    public $uuid;
    public $periodo;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): Periodo{
        $periodo = new Periodo();
        $periodo->id = $data['id'] ?? null;
        $periodo->uuid = $data['uuid'] ?? $this->generateUUID();
        $periodo->periodo = $data['bimester'] ?? null;
        $periodo->created_at = $data['created_at'] ?? null;
        $periodo->updated_at = $data['updated_at'] ?? null;
        return $periodo;
    }
}