<?php

namespace App\Interfaces\Discipline;

interface IDisciplinaRepository{
    
    public function allDisciplines(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findAllDisciplineByClassID($turma_id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}