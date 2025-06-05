<?php

namespace App\Interfaces\Classrooms;

interface IAulaRepository {
    
    public function allClass(array $params = []);

    public function create(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function classByTeacherDisciplineId(int $teacherDisciplineId);

    public function classByTeacherId(int $teacherId);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}