<?php

namespace App\Models\Scores;

use App\Models\Traits\UuidTrait;

class NotaFinal
{
    use UuidTrait;

    public $id;
    public $uuid;
    public $turma_disciplina_id;
    public $estudante_turma_id;
    public $nota;
    public $situacao;
    public $obs;
    public $ano_letivo;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        $this->uuid = $this->generateUUID();
        $this->ano_letivo = date('Y');
    }
}
