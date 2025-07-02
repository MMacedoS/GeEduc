<?php

namespace App\Models\Calendar;

use App\Models\Traits\uuidTrait;

class DiaLetivo
{
    use uuidTrait;

    public $id;
    public $uuid;
    public $data;
    public $ativo;
    public $evento;
    public $created_at;
    public $updated_at;

    public function __construct() {}

    public function create(array $data): DiaLetivo
    {
        $dia = new DiaLetivo();
        $dia->id = $data['id'] ?? null;
        $dia->uuid = $data['uuid'] ?? $this->generateUUID();
        $dia->data = usDate($data['start']);
        $dia->evento = $data['title'];
        $dia->ativo = $data['ativo'] ?? 1;
        $dia->created_at = $data['created_at'] ?? null;
        $dia->updated_at = $data['updated_at'] ?? null;

        return $dia;
    }

    public function update(array $data, DiaLetivo $dia): DiaLetivo
    {
        $dia->data = $data['data'] ?? $dia->data;
        $dia->ativo = $data['ativo'] ?? $dia->ativo;
        $dia->evento = $data['title'] ?? $dia->evento;
        return $dia;
    }
}
