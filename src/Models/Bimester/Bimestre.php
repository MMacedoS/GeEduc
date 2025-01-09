<?php

namespace App\Models\Bimester;

use App\Models\Traits\UuidTrait;

class Bimestre{

    use UuidTrait;

    public $id;
    public $uuid;
    public $bimestre;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(
        array $data
    ): Bimestre{
        $bimestre = new Bimestre();
        $bimestre->id = $data['id'] ?? null;
        $bimestre->uuid = $data['uuid'] ?? $this->generateUUID();
        $bimestre->bimestre = $data['bimester'] ?? null;
        $bimestre->created_at = $data['created_at'] ?? null;
        $bimestre->updated_at = $data['updated_at'] ?? null;
        return $bimestre;
    }
}