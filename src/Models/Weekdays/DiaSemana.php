<?php

namespace App\Models\Weekdays;

use App\Models\Traits\UuidTrait;

class DiaSemana{

    use UuidTrait;

    public $id;
    public $uuid;
    public $dia;
    public $horario;
    public $turno;
    public $created_at; 
    public $updated_at;

    public function __construct(){}
}