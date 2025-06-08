<?php

namespace App\Models\Classrooms;

use App\Models\Traits\uuidTrait;
use App\Utils\LoggerHelper;

class Aula {

    use uuidTrait;

    public $id;
    public $uuid;
    public $turma_disciplina_id;
    public $detalhes;
    public $dia_id;
    public $dia;
    public $updated_at;
    public $created_at;

    public function __construct(){}

    public function create(
        array $data
    ) : Aula{
        $day = new Aula();
        $day->id = $data['id'] ?? null;
        $day->uuid = $data['uuid'] ?? $this->generateUUID();
        $day->turma_disciplina_id = $data['classroom_discipline_id'] ?? null;     
        $day->dia_id = $data['days_id'] ?? null;     
        $day->updated_at = $data['updated_at'] ?? null;
        $day->created_at = $data['created_at'] ?? null;
        return $day;
    }

    public function update(array $data, Aula $day): Aula
    {
        $day->dia_id = $data['days_id'] ?? $day->dia_id;
        $day->turma_disciplina_id = $data['classroom_discipline_id'] ?? $day->turma_disciplina_id;

        return $day;
    }

}