<?php

namespace App\Interfaces\Classrooms;

use App\Models\Classrooms\Turma;

interface ITurmaRepository {
    
    public function allClassRooms(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function findByName(string $name): ?Turma;

    public function findByUuid(string $uuid);

    public function findById(string $id);

    public function delete(int $id);
}