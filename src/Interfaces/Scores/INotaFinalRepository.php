<?php

namespace App\Interfaces\Scores;

interface INotaFinalRepository
{
    public function create(array $params);
    public function update(array $params, int $id);
    public function findById(int $id);
    public function findByUuid(string $uuid);
    public function findByStudentAndDiscipline(int $estudante_turma_id, int $turma_disciplina_id);
}
