<?php

namespace App\Interfaces\Teacher;

interface IProfessorRepository {
    
    public function allTeachers(array $params = []);

    public function teacherWithPersonByUuid(string $uuid);

    public function saveAll(array $data);

    public function create(array $data);

    public function updateAll(array $data);

    public function update(array $data, int $id);

    public function deleteAll($professor);

    public function delete(int $id);

    public function teacherByPersonId(int $person_id);

    public function findByUuid(string $uuid);

    public function teacherWithPersonByID(?int $id);

    public function findById(string $id);
}