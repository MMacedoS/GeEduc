<?php

namespace App\Models\recuperation;

use App\Models\Traits\UuidTrait;

class Recuperacao {
    use UuidTrait;

    public ?int $id;
    public string $uuid;
    public int $ano_letivo;
    public int $turma_disciplina_id;
    public $turma;
    public string $periodo;
    public string $nota;
    public int $estudante_turma_id;
    public string $obs;
    public $estudante;
    public $updated_at;
    public $created_at;

    public function __construct() { }

    public function create(array $params): Recuperacao
    {
        $recuperacao = new Recuperacao();
        $recuperacao->id = $params['id'] ?? null;
        $recuperacao->uuid = $params['uuid'] ?? $this->generateUUID();
        $recuperacao->ano_letivo = $params['year_school'] ?? Date('Y');
        $recuperacao->turma_disciplina_id = $params['class_discipline_id'];
        $recuperacao->periodo = $params['period'];
        $recuperacao->nota = $params['score'];
        $recuperacao->estudante_turma_id = $params['student_class_id'];
        $recuperacao->obs = $params['obs'];
        return $recuperacao;
    }

    public function update(array $params, Recuperacao $recuperacao): Recuperacao
    {
        $recuperacao->ano_letivo = $params['year_school'] ?? $recuperacao->ano_letivo;
        $recuperacao->turma_disciplina_id = $params['class_discipline_id'] ?? $recuperacao->turma_disciplina_id;
        $recuperacao->periodo = $params['period'] ?? $recuperacao->periodo;
        $recuperacao->estudante_turma_id = $params['student_class_id'] ?? $recuperacao->estudante_turma_id;
        return $recuperacao;
    }
}