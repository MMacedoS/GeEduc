<?php

namespace App\Interfaces\Teacher;

interface IProfessorDisciplinaRepository
{

    public function allTeacherDisciplines(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);

    public function duplicateForYear(int $turmaId, int $newYear): bool;
}
