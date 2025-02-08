<?php

namespace App\Interfaces\Frequencies;

interface IFrequenciaRepository {
    
    public function allFrequencies(array $params = []);

    public function create(array $params);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function selectFrequencies(int $turmaDisciplinaId, int $turmaEstudanteId);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}