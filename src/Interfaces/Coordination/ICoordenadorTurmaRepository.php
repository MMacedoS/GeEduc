<?php

namespace App\Interfaces\Coordination;

use App\Models\Coordination\CoordenadorTurma;

interface ICoordenadorTurmaRepository {
    
    public function allCoordinatorClass(array $params = []);

    public function allCoordinatorClassWithoutCoordinator(array $params = []);

    public function saveAll(array $data, $turma_id);

    public function create(array $data): ?CoordenadorTurma;

    public function delete(int $id);

    public function deleteByClassId(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}